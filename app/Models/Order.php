<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const BELUM_BAYAR = 'unpaid';
    const DP_DITERIMA = 'dp_received';
    const LUNAS = 'paid';

    protected $fillable = [
        'order_code',
        'customer_id',
        'service_id',
        'status_id',
        'payment_status',
        'created_by',
        'created_via',
        'reorder_from_id',
        'title',
        'description',
        'expected_deadline',
        'budget_estimate',
        'extra_fields',
        'contact_name',
        'contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'expected_deadline' => 'date',
            'budget_estimate' => 'decimal:2',
            'extra_fields' => 'array',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusDefinition::class, 'status_id');
    }

    public function references()
    {
        return $this->hasMany(OrderReference::class);
    }

    public function consultation()
    {
        return $this->hasOne(OrderConsultation::class);
    }

    public function pricing()
    {
        return $this->hasOne(OrderPricing::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function closure()
    {
        return $this->hasOne(OrderClosure::class);
    }

    // catatan tidak bisa diubah lagi kalau sudah dikunci atau pesanan sudah selesai/dibatalkan
    public function bolehCatatKonsultasi()
    {
        return ! $this->consultation?->locked_at && ! $this->status->is_final;
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function ubahStatus($kode)
    {
        $statusBaru = StatusDefinition::pesanan($kode);

        $this->statusHistories()->create([
            'from_status' => $this->status->code,
            'to_status' => $statusBaru->code,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        $this->status()->associate($statusBaru);
        $this->save();
    }

    // contoh kode: ORD-0001
    public static function generateCode()
    {
        $nomor = (static::max('id') ?? 0) + 1;

        return 'ORD-'.str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
}
