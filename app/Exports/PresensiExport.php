<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PresensiExport implements FromCollection, WithHeadings
{
    protected $tanggal_mulai;
    protected $tanggal_akhir;
    protected $kelas;
    
    public function __construct($tanggal_mulai = null, $tanggal_akhir = null, $kelas = null)
    {
        $this->tanggal_mulai = $tanggal_mulai;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->kelas = $kelas;
    }
    
    public function collection()
    {
        $query = Presensi::with('siswa')
            ->when($this->tanggal_mulai, function($q) {
                $q->whereDate('tanggal', '>=', $this->tanggal_mulai);
            })
            ->when($this->tanggal_akhir, function($q) {
                $q->whereDate('tanggal', '<=', $this->tanggal_akhir);
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
                'Tanggal' => \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y'),
                'Jam Masuk' => $p->waktu_scan ? \Carbon\Carbon::parse($p->waktu_scan)->format('H:i') : '-',
                'Jam Pulang' => $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-',
                'Status' => $this->getStatusText($p->status),
                'Keterangan' => $this->getKeterangan($p),
            ];
        });
        return collect($data);
    }
    
    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan'];
    }
    
    private function getStatusText($status)
    {
        switch($status) {
            case 'tepat_waktu':
                return 'Tepat Waktu';
            case 'terlambat':
                return 'Terlambat';
            case 'sakit':
                return 'Sakit';
            case 'izin':
                return 'Izin';
            case 'alfa':
                return 'Alfa';
            default:
                return ucfirst($status);
        }
    }
    
    private function getKeterangan($presensi)
    {
        $jam = isset($presensi->waktu_scan) ? \Carbon\Carbon::parse($presensi->waktu_scan)->format('H:i') : '-';
        
        switch($presensi->status) {
            case 'terlambat':
                return "Datang pukul {$jam}, melewati jam masuk 07:30";
            case 'tepat_waktu':
                return "Datang pukul {$jam}, sesuai waktu kedatangan";
            case 'izin':
                return $presensi->keterangan ?? '-';
            case 'sakit':
                return 'Izin sakit, surat diserahkan ke TU';
            case 'alfa':
                return 'Tidak hadir tanpa keterangan';
            default:
                return $presensi->keterangan ?? '-';
        }
    }
}
