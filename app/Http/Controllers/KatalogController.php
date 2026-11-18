<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KatalogController extends Controller
{
    public function index()
    {
        return view('katalog.index', [
            'layanans' => Service::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('katalog.form', ['layanan' => new Service(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $this->validasi($request);

        Service::create($this->ambilData($request) + [
            'sort_order' => Service::max('sort_order') + 1,
        ]);

        return redirect()->route('katalog.index')->with('status', 'Layanan berhasil ditambahkan');
    }

    public function edit(Service $layanan)
    {
        return view('katalog.form', ['layanan' => $layanan]);
    }

    public function update(Request $request, Service $layanan)
    {
        $this->validasi($request, $layanan);

        $layanan->update($this->ambilData($request));

        return redirect()->route('katalog.index')->with('status', 'Layanan berhasil diperbarui');
    }

    private function validasi(Request $request, $layanan = null)
    {
        $request->validate([
            'name' => ['required', 'max:100', Rule::unique('services', 'name')->ignore($layanan)],
            'short_description' => 'required|max:500',
            'description' => 'nullable',
            'workflow_steps' => 'nullable',
        ], [
            'name.required' => 'Nama layanan wajib diisi',
            'name.max' => 'Nama layanan maksimal 100 karakter',
            'name.unique' => 'Nama layanan sudah ada',
            'short_description.required' => 'Deskripsi singkat wajib diisi',
            'short_description.max' => 'Deskripsi singkat maksimal 500 karakter',
        ]);
    }

    private function ambilData(Request $request)
    {
        // alur pengerjaan ditulis satu langkah per baris
        $langkah = collect(explode("\n", $request->workflow_steps ?? ''))
            ->map(fn ($baris) => trim($baris))
            ->filter()
            ->values()
            ->all();

        return [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'workflow_steps' => $langkah,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
