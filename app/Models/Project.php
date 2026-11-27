<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'order_id',
        'status_id',
        'designer_id',
        'deadline',
        'progress_percentage',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusDefinition::class, 'status_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ProjectStatusHistory::class);
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProjectProgressUpdate::class)->latest('id');
    }

    public function costs()
    {
        return $this->hasMany(ProjectCost::class);
    }

    public function rating()
    {
        return $this->hasOne(ProjectRating::class);
    }

    public function chat()
    {
        return $this->hasOne(ProjectChat::class);
    }

    public function assignments()
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    // negatif berarti sudah lewat tenggat
    public function sisaHari()
    {
        return (int) now()->startOfDay()->diffInDays($this->deadline, false);
    }

    public function bolehPindahKe($kode)
    {
        return in_array($kode, $this->status->allowed_next ?? []);
    }

    public function ubahStatus($kode)
    {
        $statusBaru = StatusDefinition::proyek($kode);

        $this->statusHistories()->create([
            'from_status' => $this->status->code,
            'to_status' => $statusBaru->code,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        $this->status()->associate($statusBaru);
        $this->save();
    }
}
