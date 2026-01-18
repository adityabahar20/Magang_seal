<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class datacuti extends Model
{
    protected $table = 'data_cuti';
    protected $primaryKey = 'id_cuti';
    protected $fillable = [
        'user_id',
        'start_date',   // Tanggal mulai
        'end_date',     // Tanggal berakhir
        'reason',       // Alasan cuti
        'attachment',   // File pendukung 
        'status',       // Default: Pending
    ];

    // Relasi: Pengajuan ini milik seorang User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
