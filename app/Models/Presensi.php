<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id', 'waktu_scan', 'jam_pulang', 'status', 'tanggal', 'keterangan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
