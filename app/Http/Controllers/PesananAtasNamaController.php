<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Role;
use App\Models\Service;
use App\Models\StatusDefinition;
use App\Models\User;
use App\Services\KapasitasMingguan;
use Illuminate\Http\Request;

class PesananAtasNamaController extends Controller
{
    public function create()
    {
        return view('pesanan-masuk.form', [
            'pelanggan' => User::role(Role::CUSTOMER)->with('customerProfile')->orderBy('name')->get(),
            'semuaLayanan' => Service::where('is_active', true)->orderBy('sort_order')->get(),
            'kapasitasMinggu' => KapasitasMingguan::beberapaMinggu(26),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'service_id' => 'required',
            'title' => 'required|max:255',
            'description' => 'required',
            'expected_deadline' => 'required|date|after:today',
            'budget_estimate' => 'nullable|numeric|min:0',
        ], [
            'customer_id.required' => 'Pelanggan wajib dipilih',
            'service_id.required' => 'Layanan wajib dipilih',
            'title.required' => 'Judul proyek wajib diisi',
            'title.max' => 'Judul proyek maksimal 255 karakter',
            'description.required' => 'Deskripsi kebutuhan wajib diisi',
            'expected_deadline.required' => 'Tenggat wajib diisi',
            'expected_deadline.date' => 'Format tenggat tidak valid',
            'expected_deadline.after' => 'Tenggat harus setelah hari ini',
            'budget_estimate.numeric' => 'Perkiraan anggaran harus berupa angka',
            'budget_estimate.min' => 'Perkiraan anggaran tidak boleh minus',
        ]);

        // hanya untuk pelanggan yang sudah terdaftar
        $pelanggan = User::role(Role::CUSTOMER)->find($request->customer_id);
        $layanan = Service::where('is_active', true)->find($request->service_id);

        if (! $pelanggan || ! $layanan) {
            return back()->withErrors(['customer_id' => 'Pelanggan atau layanan tidak ditemukan'])->withInput();
        }

        $statusAwal = StatusDefinition::pesanan('menunggu_konsultasi');

        $order = Order::create([
            'order_code' => Order::generateCode(),
            'customer_id' => $pelanggan->id,
            'service_id' => $layanan->id,
            'status_id' => $statusAwal->id,
            'created_by' => auth()->id(),
            'created_via' => 'admin',
            'title' => $request->title,
            'description' => $request->description,
            'expected_deadline' => $request->expected_deadline,
            'budget_estimate' => $request->budget_estimate,
            'contact_name' => $pelanggan->name,
            'contact_phone' => $pelanggan->customerProfile->phone,
        ]);

        $order->statusHistories()->create([
            'to_status' => $statusAwal->code,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'Pesanan atas nama '.$pelanggan->name.' berhasil dibuat');
    }
}
