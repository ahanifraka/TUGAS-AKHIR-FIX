<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanInformasiLog extends Model
{
    protected $fillable = [
        'permohonan_informasi_id',
        'action',
        'old_status',
        'new_status',
        'notes',
        'user_id',
        'user_name',
    ];

    /**
     * Get the permohonan informasi that owns the log.
     */
    public function permohonanInformasi()
    {
        return $this->belongsTo(PermohonanInformasi::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted action label.
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'created' => 'Permohonan Dibuat',
            'status_changed' => 'Status Diubah',
            'note_added' => 'Catatan Ditambahkan',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    /**
     * Get status label.
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pending',
            'processed' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
        ];

        return $labels[$status] ?? $status;
    }
}
