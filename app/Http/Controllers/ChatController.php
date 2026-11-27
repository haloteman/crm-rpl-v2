<?php

namespace App\Http\Controllers;

use App\Models\ChatAttachment;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\ProjectChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index', ['daftarChat' => $this->daftarChat()]);
    }

    public function show(ProjectChat $chat)
    {
        $peserta = $this->pastikanPeserta($chat);

        $chat->load('project.order.service', 'project.order.consultation', 'project.status', 'project.statusHistories', 'project.designer', 'messages.sender', 'messages.attachments');

        // pesan dianggap sudah dibaca saat chat dibuka
        $peserta->update(['last_read_at' => now()]);

        return view('chat.show', [
            'chat' => $chat,
            'daftarChat' => $this->daftarChat(),
            'hanyaBaca' => $chat->project->status->code == 'selesai',
        ]);
    }

    public function kirim(Request $request, ProjectChat $chat)
    {
        $peserta = $this->pastikanPeserta($chat);

        // chat proyek yang sudah selesai hanya bisa dibaca
        if ($chat->project->status->code == 'selesai') {
            abort(403);
        }

        $request->validate([
            'body' => 'required',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], [
            'body.required' => 'Pesan wajib diisi',
            'lampiran.mimes' => 'Lampiran harus gambar JPG atau PNG',
            'lampiran.max' => 'Ukuran gambar maksimal 2 MB',
        ]);

        if ($peserta->participant_role == ChatParticipant::DESAINER) {
            $jenis = ChatMessage::BALASAN;
        } elseif ($request->boolean('permintaan_revisi')) {
            $jenis = ChatMessage::PERMINTAAN_REVISI;
        } else {
            $jenis = ChatMessage::PERTANYAAN;
        }

        $pesan = $chat->messages()->create([
            'sender_id' => auth()->id(),
            'message_type' => $jenis,
            'body' => $request->body,
        ]);

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');

            $pesan->attachments()->create([
                'file_path' => $file->store('lampiran-chat'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        $peserta->update(['last_read_at' => now()]);

        return redirect()->route('chat.show', $chat);
    }

    public function unduh(ChatAttachment $lampiran)
    {
        $this->pastikanPeserta($lampiran->message->chat);

        return Storage::download($lampiran->file_path, $lampiran->original_name);
    }

    // chat milik user yang sedang login beserta jumlah pesan belum dibaca
    private function daftarChat()
    {
        return ChatParticipant::with('chat.project.order.service', 'chat.project.status')
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->latest('updated_at')
            ->get();
    }

    private function pastikanPeserta(ProjectChat $chat)
    {
        $peserta = $chat->pesertaAktif(auth()->user());

        if (! $peserta) {
            abort(403);
        }

        return $peserta;
    }
}
