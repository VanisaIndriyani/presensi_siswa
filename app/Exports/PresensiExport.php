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
                'Keterangan' => $p->status == 'terlambat' ? 'Datang pukul ' . (isset($p->waktu_scan) ? \Carbon\Carbon::parse($p->waktu_scan)->format('H:i') : '-') . ', melewati jam masuk 07:30'
    : ($p->status == 'tepat_waktu' ? 'Datang pukul ' . (isset($p->waktu_scan) ? \Carbon\Carbon::parse($p->waktu_scan)->format('H:i') : '-') . ', sesuai waktu kedatangan'
    : ($p->status == 'izin' ? ($p->keterangan ?? '-')
    : ($p->status == 'sakit' ? 'Izin sakit, surat diserahkan ke TU'
    : ($p->status == 'alfa' ? 'Tidak hadir tanpa keterangan' : ($p->keterangan ?? '-'))))),
            ];
        });
        return collect($data);
    }
    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan'];
    }
}
