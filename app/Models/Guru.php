<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'nip', 'email', 'mapel', 'jenis_kelamin', 'alamat', 'kelas'
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas', 'kelas');
    }

    public function presensis()
    {
        return $this->hasManyThrough(Presensi::class, Siswa::class, 'kelas', 'siswa_id', 'kelas', 'id');
    }
}
