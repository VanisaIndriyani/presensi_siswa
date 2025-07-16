<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PresensiExport implements FromCollection, WithHeadings
{
    protected $tanggal;
    public function __construct($tanggal = null)
    {
        $this->tanggal = $tanggal;
    }
    public function collection()
    {
        $query = Presensi::with('siswa')
            ->when($this->tanggal, function($q) {
                $q->whereDate('tanggal', $this->tanggal);
            })
            ->orderByDesc('waktu_scan');
        $data = $query->get()->map(function($p, $i) {
            return [
                'No' => $i+1,
                'Nama Siswa' => $p->siswa->nama ?? '-',
                'NISN' => $p->siswa->nisn ?? '-',
                'Kelas' => $p->siswa->kelas ?? '-',
                'Waktu Scan' => $p->waktu_scan,
                'Status' => $p->status == 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat',
                'Keterangan' => ($p->status == 'tepat_waktu' && empty($p->keterangan)) ? 'Tepat waktu masuk sekolah' : $p->keterangan,
            ];
        });
        return collect($data);
    }
    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Waktu Scan', 'Status', 'Keterangan'];
    }
}
