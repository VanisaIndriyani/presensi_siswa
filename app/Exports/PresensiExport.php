<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PresensiExport implements FromCollection, WithHeadings
{
    protected $tanggal;
    protected $kelas;
    public function __construct($tanggal = null, $kelas = null)
    {
        $this->tanggal = $tanggal;
        $this->kelas = $kelas;
    }
    public function collection()
    {
        $query = Presensi::with('siswa')
            ->when($this->tanggal, function($q) {
                $q->whereDate('tanggal', $this->tanggal);
            })
            ->when($this->kelas, function($q) {
                $q->whereHas('siswa', function($q) {
                    $q->where('kelas', $this->kelas);
                });
            })
            ->orderByDesc('waktu_scan');
        $data = $query->get()->map(function($p, $i) {
            return [
                'No' => $i+1,
                'Nama Siswa' => $p->siswa->nama ?? '-',
                'NISN' => $p->siswa->nisn ?? '-',
                'Kelas' => $p->siswa->kelas ?? '-',
                'Waktu Scan' => $p->waktu_scan,
                'Jam Pulang' => $p->jam_pulang ?? '-',
                'Status' => $p->status == 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat',
                'Keterangan' => ($p->status == 'tepat_waktu' && empty($p->keterangan)) ? 'Tepat waktu masuk sekolah' : $p->keterangan,
            ];
        });
        return collect($data);
    }
    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan'];
    }
}
