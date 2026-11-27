<?php

namespace App\Http\Controllers;

use App\Models\ChatParticipant;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    public function index()
    {
        $proyek = Project::with('order.service', 'order.references')
            ->whereRelation('status', 'code', 'menunggu_penugasan')
            ->orderBy('deadline')
            ->get();

        return view('penugasan.index', [
            'proyek' => $proyek,
            'desainer' => $this->daftarDesainer(),
        ]);
    }

    public function simpan(Request $request, Project $project)
    {
        if ($project->status->code != 'menunggu_penugasan') {
            abort(403);
        }

        $request->validate([
            'designer_id' => 'required',
        ], [
            'designer_id.required' => 'Desainer wajib dipilih',
        ]);

        // Kepala Staf Desainer boleh memilih dirinya sendiri
        $desainer = User::role([Role::DESAINER, Role::KEPALA_STAF_DESAINER])->find($request->designer_id);

        if (! $desainer) {
            return back()->withErrors(['designer_id' => 'Desainer tidak ditemukan']);
        }

        $project->assignments()->create([
            'designer_id' => $desainer->id,
            'assigned_by' => auth()->id(),
            'assignment_type' => ProjectAssignment::AWAL,
            'assigned_at' => now(),
        ]);

        $project->designer_id = $desainer->id;
        $project->started_at = now();
        $project->ubahStatus('dikerjakan');

        $project->order->ubahStatus('dikerjakan');

        // chat customer dengan desainer dibuka setelah proyek ditugaskan
        $chat = $project->chat()->create(['opened_at' => now()]);

        $chat->participants()->create([
            'user_id' => $project->order->customer_id,
            'participant_role' => ChatParticipant::CUSTOMER,
            'joined_at' => now(),
        ]);

        $chat->participants()->create([
            'user_id' => $desainer->id,
            'participant_role' => ChatParticipant::DESAINER,
            'joined_at' => now(),
        ]);

        return redirect()->route('penugasan.index')->with('status', 'Proyek berhasil ditugaskan ke '.$desainer->name);
    }

    // keahlian dan jumlah proyek aktif dipakai untuk menimbang beban kerja
    private function daftarDesainer()
    {
        return User::role([Role::DESAINER, Role::KEPALA_STAF_DESAINER])
            ->with('designerSkills.service')
            ->withCount(['projects as proyek_aktif' => function ($query) {
                $query->whereRelation('status', 'code', '!=', 'selesai');
            }])
            ->orderBy('name')
            ->get();
    }
}
