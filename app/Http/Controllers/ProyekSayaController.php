<?php

namespace App\Http\Controllers;

use App\Models\ProgressAttachment;
use App\Models\Project;
use Illuminate\Http\Request;

class ProyekSayaController extends Controller
{
    public function index()
    {
        $proyek = Project::with('order.service', 'order.references', 'status')
            ->where('designer_id', auth()->id())
            ->orderBy('deadline')
            ->get();

        return view('proyek-saya.index', ['proyek' => $proyek]);
    }

    public function show(Project $project)
    {
        $this->pastikanMilikSendiri($project);

        $project->load('order.service', 'order.customer', 'order.references', 'order.consultation', 'status', 'progressUpdates.attachments', 'assignments.designer');

        return view('proyek-saya.show', ['project' => $project]);
    }

    public function progres(Request $request, Project $project)
    {
        $this->pastikanMilikSendiri($project);

        if ($project->status->is_final || $project->status->code == 'menunggu_penugasan') {
            abort(403);
        }

        $request->validate([
            'stage' => 'required|max:100',
            'percentage' => 'required|integer|min:0|max:100',
            'note' => 'nullable',
            'lampiran_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'lampiran_link' => 'nullable|url|max:2048',
        ], [
            'stage.required' => 'Tahap wajib diisi',
            'stage.max' => 'Tahap maksimal 100 karakter',
            'percentage.required' => 'Persentase wajib diisi',
            'percentage.integer' => 'Persentase harus berupa angka',
            'percentage.min' => 'Persentase minimal 0',
            'percentage.max' => 'Persentase maksimal 100',
            'lampiran_file.mimes' => 'File bukti harus JPG, PNG, atau PDF',
            'lampiran_file.max' => 'File bukti maksimal 10 MB',
            'lampiran_link.url' => 'Tautan hasil harus berupa alamat web yang valid',
        ]);

        $pembaruan = $project->progressUpdates()->create([
            'updated_by' => auth()->id(),
            'stage' => $request->stage,
            'percentage' => $request->percentage,
            'note' => $request->note,
        ]);

        if ($request->hasFile('lampiran_file')) {
            $file = $request->file('lampiran_file');

            $pembaruan->attachments()->create([
                'attachment_type' => ProgressAttachment::FILE,
                'url_or_path' => $file->store('lampiran-progres'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        // tautan dipakai untuk file besar, misalnya video
        if ($request->filled('lampiran_link')) {
            $pembaruan->attachments()->create([
                'attachment_type' => ProgressAttachment::LINK,
                'url_or_path' => $request->lampiran_link,
            ]);
        }

        $project->update(['progress_percentage' => $request->percentage]);

        return redirect()->route('proyek-saya.show', $project)->with('status', 'Progres berhasil diperbarui');
    }

    public function revisi(Request $request, Project $project)
    {
        $this->pastikanMilikSendiri($project);

        if (! $project->bolehPindahKe('revisi')) {
            return back()->withErrors(['status' => 'Status tidak sesuai urutan']);
        }

        // tanda revisi ikut masuk riwayat pembaruan
        $project->progressUpdates()->create([
            'updated_by' => auth()->id(),
            'stage' => 'Ditandai Revisi',
            'percentage' => $project->progress_percentage,
            'note' => $request->note,
        ]);

        $project->ubahStatus('revisi');
        $project->order->ubahStatus('revisi');

        return redirect()->route('proyek-saya.show', $project)->with('status', 'Proyek ditandai Revisi');
    }

    public function selesai(Project $project)
    {
        $this->pastikanMilikSendiri($project);

        if (! $project->bolehPindahKe('selesai')) {
            return back()->withErrors(['status' => 'Status tidak sesuai urutan']);
        }

        // lampiran pada pembaruan terakhir yang punya lampiran menjadi hasil akhir
        $pembaruanTerakhir = $project->progressUpdates()->has('attachments')->first();

        if ($pembaruanTerakhir) {
            $pembaruanTerakhir->attachments()->update(['is_final_result' => true]);
        }

        $project->completed_at = now();
        $project->ubahStatus('selesai');
        $project->order->ubahStatus('selesai');

        return redirect()->route('proyek-saya.show', $project)->with('status', 'Proyek ditandai Selesai');
    }

    // proyek yang sudah dipindahkan ke desainer lain tidak bisa dibuka lagi
    private function pastikanMilikSendiri(Project $project)
    {
        if ($project->designer_id != auth()->id()) {
            abort(403);
        }
    }
}
