<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PresensiExport implements FromCollection, WithHeadings, WithEvents
{
    protected $tanggal_mulai;
    protected $tanggal_akhir;
    protected $kelas;
    protected $rekap;
    protected $total;
    protected $rata;

    public function __construct($tanggal_mulai = null, $tanggal_akhir = null, $kelas = null)
    {
        $this->tanggal_mulai = $tanggal_mulai;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->kelas = $kelas;
    }

    public function collection()
    {
        $siswaQuery = Siswa::query();
        if ($this->kelas) {
            $siswaQuery->where('kelas', $this->kelas);
        }
        $siswas = $siswaQuery->orderBy('nama')->get();

        $tanggal_mulai = $this->tanggal_mulai;
        $tanggal_akhir = $this->tanggal_akhir;

        // Ambil semua tanggal unik presensi dalam rentang
        $tanggalList = Presensi::query()
            ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                $q->whereDate('tanggal', '>=', $tanggal_mulai);
            })
            ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                $q->whereDate('tanggal', '<=', $tanggal_akhir);
            })
            ->when($this->kelas, function($q) {
                $q->whereHas('siswa', function($q) {
                    $q->where('kelas', $this->kelas);
                });
            })
            ->distinct('tanggal')
            ->pluck('tanggal');
        $totalHari = $tanggalList->count();

        $rekap = [];
        $total = [
            'total_hari' => $totalHari,
            'hadir' => 0,
            'tidak_hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];

        foreach ($siswas as $i => $siswa) {
            $presensis = Presensi::where('siswa_id', $siswa->id)
                ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                    $q->whereDate('tanggal', '>=', $tanggal_mulai);
                })
                ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                    $q->whereDate('tanggal', '<=', $tanggal_akhir);
                })
                ->get();
            $hadir = $presensis->whereIn('status', ['tepat_waktu', 'terlambat'])->count();
            $izin = $presensis->where('status', 'izin')->count();
            $sakit = $presensis->where('status', 'sakit')->count();
                    $alpa = $presensis->where('status', 'alpa')->count();
        $tidak_hadir = $izin + $sakit + $alpa;
            $persen = $totalHari > 0 ? round(($hadir / $totalHari) * 100) : 0;

            $rekap[] = [
                'No' => $i+1,
                'Nama Siswa' => $siswa->nama,
                'NISN' => $siswa->nisn,
                'Kelas' => $siswa->kelas,
                'Total Hari Sekolah' => $totalHari,
                'Hadir' => $hadir,
                'Tidak Hadir' => $tidak_hadir,
                'Izin' => $izin,
                'Sakit' => $sakit,
                'Alpa' => $alpa,
                'Persentase Kehadiran' => $persen . '%',
            ];
            $total['hadir'] += $hadir;
            $total['tidak_hadir'] += $tidak_hadir;
            $total['izin'] += $izin;
            $total['sakit'] += $sakit;
            $total['alpa'] += $alpa;
        }
        $jumlahSiswa = max(count($siswas), 1);
        $this->rekap = $rekap;
        $this->total = [
            '', 'Total', '', '', $totalHari * $jumlahSiswa, $total['hadir'], $total['tidak_hadir'], $total['izin'], $total['sakit'], $total['alpa'], ''
        ];
        $this->rata = [
            '', 'Rata-rata', '', '', $totalHari, 
            $jumlahSiswa > 0 ? round($total['hadir'] / $jumlahSiswa) : 0,
            $jumlahSiswa > 0 ? round($total['tidak_hadir'] / $jumlahSiswa) : 0,
            $jumlahSiswa > 0 ? round($total['izin'] / $jumlahSiswa) : 0,
            $jumlahSiswa > 0 ? round($total['sakit'] / $jumlahSiswa) : 0,
            $jumlahSiswa > 0 ? round($total['alpa'] / $jumlahSiswa) : 0,
            $totalHari > 0 ? round(($total['hadir'] / ($jumlahSiswa * $totalHari)) * 100) . '%' : '0%'
        ];
        return collect($rekap);
    }

    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Total Hari Sekolah', 'Hadir', 'Tidak Hadir', 'Izin', 'Sakit', 'Alpa', 'Persentase Kehadiran'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = count($this->rekap) + 2;
                
                // Tambahkan baris total
                $event->sheet->setCellValue("A{$lastRow}", 'Total');
                $event->sheet->setCellValue("E{$lastRow}", $this->total[4]);
                $event->sheet->setCellValue("F{$lastRow}", $this->total[5]);
                $event->sheet->setCellValue("G{$lastRow}", $this->total[6]);
                $event->sheet->setCellValue("H{$lastRow}", $this->total[7]);
                $event->sheet->setCellValue("I{$lastRow}", $this->total[8]);
                $event->sheet->setCellValue("J{$lastRow}", $this->total[9]);
                
                // Tambahkan baris rata-rata
                $lastRow++;
                $event->sheet->setCellValue("A{$lastRow}", 'Rata-rata');
                $event->sheet->setCellValue("E{$lastRow}", $this->rata[4]);
                $event->sheet->setCellValue("F{$lastRow}", $this->rata[5]);
                $event->sheet->setCellValue("G{$lastRow}", $this->rata[6]);
                $event->sheet->setCellValue("H{$lastRow}", $this->rata[7]);
                $event->sheet->setCellValue("I{$lastRow}", $this->rata[8]);
                $event->sheet->setCellValue("J{$lastRow}", $this->rata[9]);
            }
        ];
    }
}
