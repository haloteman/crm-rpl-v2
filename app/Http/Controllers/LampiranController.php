<?php

namespace App\Http\Controllers;

use App\Models\ProgressAttachment;
use Illuminate\Support\Facades\Storage;

class LampiranController extends Controller
{
    // lampiran progres hanya untuk customer pemilik, desainer proyek, dan pengguna internal yang boleh lihat pesanan
    public function unduh(ProgressAttachment $lampiran)
    {
        $project = $lampiran->progressUpdate->project;
        $boleh = $project->order->customer_id === auth()->id()
            || $project->designer_id === auth()->id()
            || auth()->user()->can('order.view');

        if (! $boleh || $lampiran->attachment_type !== ProgressAttachment::FILE) {
            abort(404);
        }

        return Storage::download($lampiran->url_or_path, $lampiran->original_name);
    }
}
