<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Project;
use App\Models\ProjectAssignment;

class TimelineProyek
{
    // timeline tidak disimpan, dirakit dari data DP, penugasan, progres, status, dan tenggat
    public static function untuk(Project $project)
    {
        $kejadian = [];

        $dp = $project->order->payments()
            ->where('payment_type', PaymentTransaction::DP)
            ->where('status', PaymentTransaction::VALID)
            ->first();

        if ($dp) {
            $kejadian[] = ['jenis' => 'dp_received', 'judul' => 'DP diterima', 'waktu' => $dp->paid_at, 'detail' => null];
        }

        foreach ($project->assignments()->with('designer')->get() as $penugasan) {
            $judul = $penugasan->assignment_type == ProjectAssignment::PINDAH ? 'Dipindahkan ke ' : 'Ditugaskan ke ';
            $kejadian[] = ['jenis' => 'assigned', 'judul' => $judul.$penugasan->designer->name, 'waktu' => $penugasan->assigned_at, 'detail' => null];
        }

        foreach ($project->progressUpdates()->get() as $progres) {
            $kejadian[] = ['jenis' => 'progress', 'judul' => $progres->stage.' ('.$progres->percentage.'%)', 'waktu' => $progres->created_at, 'detail' => $progres->note];
        }

        foreach ($project->statusHistories()->whereIn('to_status', ['revisi', 'selesai'])->get() as $riwayat) {
            $jenis = $riwayat->to_status == 'revisi' ? 'revision' : 'completed';
            $judul = $riwayat->to_status == 'revisi' ? 'Revisi' : 'Selesai';
            $kejadian[] = ['jenis' => $jenis, 'judul' => $judul, 'waktu' => $riwayat->changed_at, 'detail' => null];
        }

        $kejadian[] = ['jenis' => 'deadline', 'judul' => 'Tenggat', 'waktu' => $project->deadline, 'detail' => null];

        return collect($kejadian)->sortBy('waktu')->values();
    }
}
