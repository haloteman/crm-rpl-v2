<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReference;
use App\Models\PaymentTransaction;
use App\Models\Service;
use App\Models\StatusDefinition;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function create($slug)
    {
        return view('pesanan.form', [
            'layanan' => Service::where('slug', $slug)->where('is_active', true)->firstOrFail(),
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, $slug)
    {
        $layanan = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'expected_deadline' => 'required|date|after:today',
            'budget_estimate' => 'nullable|numeric|min:0',
            'referensi_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'referensi_link' => 'nullable|url|max:2048',
        ], [
            'title.required' => 'Judul proyek wajib diisi',
            'title.max' => 'Judul proyek maksimal 255 karakter',
            'description.required' => 'Deskripsi kebutuhan wajib diisi',
            'expected_deadline.required' => 'Tenggat wajib diisi',
            'expected_deadline.date' => 'Format tenggat tidak valid',
            'expected_deadline.after' => 'Tenggat harus setelah hari ini',
            'budget_estimate.numeric' => 'Perkiraan anggaran harus berupa angka',
            'budget_estimate.min' => 'Perkiraan anggaran tidak boleh minus',
            'referensi_file.mimes' => 'File referensi harus JPG, PNG, atau PDF',
            'referensi_file.max' => 'File referensi maksimal 10 MB',
            'referensi_link.url' => 'Tautan referensi harus berupa alamat web yang valid',
        ]);

        $user = auth()->user();
        $statusAwal = StatusDefinition::pesanan('menunggu_konsultasi');

        // nama dan nomor HP disalin dari akun saat pesanan diajukan
        $order = Order::create([
            'order_code' => Order::generateCode(),
            'customer_id' => $user->id,
            'service_id' => $layanan->id,
            'status_id' => $statusAwal->id,
            'created_by' => $user->id,
            'created_via' => 'customer',
            'title' => $request->title,
            'description' => $request->description,
            'expected_deadline' => $request->expected_deadline,
            'budget_estimate' => $request->budget_estimate,
            'contact_name' => $user->name,
            'contact_phone' => $user->customerProfile->phone,
        ]);

        if ($request->hasFile('referensi_file')) {
            $file = $request->file('referensi_file');

            $order->references()->create([
                'reference_type' => OrderReference::FILE,
                'url_or_path' => $file->store('referensi'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        if ($request->filled('referensi_link')) {
            $order->references()->create([
                'reference_type' => OrderReference::LINK,
                'url_or_path' => $request->referensi_link,
            ]);
        }

        $order->statusHistories()->create([
            'to_status' => $statusAwal->code,
            'changed_by' => $user->id,
            'changed_at' => now(),
        ]);

        return redirect()->route('pesanan.terkirim', $order);
    }

    public function terkirim(Order $order)
    {
        $this->pastikanMilikSendiri($order);

        return view('pesanan.terkirim', ['order' => $order]);
    }

    public function index()
    {
        return view('pesanan.index', [
            'orders' => Order::with('service', 'status')->where('customer_id', auth()->id())->latest()->paginate(10),
        ]);
    }

    public function show(Order $order)
    {
        $this->pastikanMilikSendiri($order);

        $order->load('service', 'status', 'references', 'statusHistories', 'pricing');

        // riwayat pembayaran customer, yang sudah dibatalkan Owner tidak ditampilkan
        $order->load(['payments' => function ($query) {
            $query->where('status', PaymentTransaction::VALID)->orderBy('paid_at');
        }]);

        return view('pesanan.show', [
            'order' => $order,
            'labelStatus' => StatusDefinition::where('scope', 'order')->pluck('customer_label', 'code'),
        ]);
    }

    private function pastikanMilikSendiri(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(404);
        }
    }
}
