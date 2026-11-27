<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\StatusDefinition;
use App\Models\User;
use Illuminate\Http\Request;

class PemantauanController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('order.service', 'order.customer', 'designer', 'status')->orderBy('deadline');

        if ($request->filled('status')) {
            $query->whereRelation('status', 'code', $request->status);
        }

        if ($request->filled('desainer')) {
            $query->where('designer_id', $request->desainer);
        }

        return view('pemantauan.index', [
            'proyek' => $query->paginate(10)->withQueryString(),
            'semuaStatus' => StatusDefinition::where('scope', 'project')->orderBy('sort_order')->get(),
            'desainer' => User::daftarDesainer(),
        ]);
    }

    public function show(Project $project)
    {
        $project->load('order.service', 'order.customer', 'order.references', 'order.consultation', 'designer', 'status', 'progressUpdates.attachments', 'assignments.designer');

        return view('pemantauan.show', [
            'project' => $project,
            'desainer' => User::daftarDesainer(),
        ]);
    }
}
