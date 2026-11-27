<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReference;
use App\Models\PaymentTransaction;
use App\Models\ProgressAttachment;
use App\Models\Service;
use App\Models\StatusDefinition;
use App\Services\KapasitasMingguan;
use App\Services\TimelineProyek;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function create($slug)
    {
        return view('pesanan.form', [
            'layanan' => Service::where('slug', $slug)->where('is_active', true)->firstOrFail(),
            'user' => auth()->user(),
            'kapasitasMinggu' => KapasitasMingguan::beberapaMinggu(26),
            'pesananAsal' => null,
        ]);
    }

    // Pesan Lagi: form baru terisi layanan dan kebutuhan dari pesanan sebelumnya
    public function pesanLagi(Order $order)
    {
        $this->pastikanMilikSendiri($order);

        if (! $order->service->is_active) {
            abort(404);
        }

        return view('pesanan.form', [
            'layanan' => $order->service,
            'user' => auth()->user(),
            'kapasitasMinggu' => KapasitasMingguan::beberapaMinggu(26),
            'pesananAsal' => $order,
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
            'extra.*' => 'nullable|max:255',
            'reorder_from_id' => 'nullable|integer',
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
            'extra.*.max' => 'Isian tambahan maksimal 255 karakter',
        ]);

        // kolom tambahan sesuai jenis layanan, misalnya ukuran kaos atau durasi video
        $isianTambahan = [];

        foreach ($layanan->extra_field_schema ?? [] as $kolom) {
            if ($request->filled('extra.'.$kolom['key'])) {
                $isianTambahan[$kolom['key']] = $request->input('extra.'.$kolom['key']);
            }
        }

        $user = auth()->user();
        $pesananAsal = Order::where('customer_id', $user->id)->find($request->reorder_from_id);
        $statusAwal = StatusDefinition::pesanan('menunggu_konsultasi');

        // nama dan nomor HP disalin dari akun saat pesanan diajukan
        $order = Order::create([
            'order_code' => Order::generateCode(),
            'customer_id' => $user->id,
            'service_id' => $layanan->id,
            'status_id' => $statusAwal->id,
            'created_by' => $user->id,
            'created_via' => 'customer',
            'reorder_from_id' => $pesananAsal?->id,
            'title' => $request->title,
            'description' => $request->description,
            'expected_deadline' => $request->expected_deadline,
            'budget_estimate' => $request->budget_estimate,
            'extra_fields' => $isianTambahan ?: null,
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

        $order->load('service', 'status', 'references', 'statusHistories', 'pricing', 'closure.reason', 'project.rating');

        // riwayat pembayaran customer, yang sudah dibatalkan Owner tidak ditampilkan
        $order->load(['payments' => function ($query) {
            $query->where('status', PaymentTransaction::VALID)->orderBy('paid_at');
        }]);

        return view('pesanan.show', [
            'order' => $order,
            'labelStatus' => StatusDefinition::where('scope', 'order')->pluck('customer_label', 'code'),
        ]);
    }

    public function tracking(Order $order)
    {
        $this->pastikanMilikSendiri($order);

        $order->load('service', 'status', 'statusHistories', 'project.status', 'project.progressUpdates.attachments');

        // hasil akhir baru dibuka setelah proyek selesai
        $hasilAkhir = collect();

        if ($order->project?->status->code == 'selesai') {
            $hasilAkhir = ProgressAttachment::where('is_final_result', true)
                ->whereRelation('progressUpdate', 'project_id', $order->project->id)
                ->get();
        }

        return view('pesanan.tracking', [
            'order' => $order,
            'hasilAkhir' => $hasilAkhir,
            'timeline' => $order->project ? TimelineProyek::untuk($order->project) : collect(),
            'labelStatus' => StatusDefinition::where('scope', 'order')->pluck('customer_label', 'code'),
        ]);
    }

    public function rating(Request $request, Order $order)
    {
        $this->pastikanMilikSendiri($order);

        // rating hanya sekali dan hanya setelah proyek selesai
        if ($order->project?->status->code != 'selesai' || $order->project->rating) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|max:1000',
        ], [
            'rating.required' => 'Rating bintang wajib dipilih',
            'rating.integer' => 'Rating harus 1 sampai 5',
            'rating.min' => 'Rating harus 1 sampai 5',
            'rating.max' => 'Rating harus 1 sampai 5',
            'review.max' => 'Ulasan maksimal 1000 karakter',
        ]);

        $order->project->rating()->create([
            'customer_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->route('pesanan.show', $order)->with('status', 'Terima kasih atas rating dan ulasannya');
    }

    private function pastikanMilikSendiri(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(404);
        }
    }
}
