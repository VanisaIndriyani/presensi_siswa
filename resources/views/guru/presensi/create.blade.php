@extends('layouts.guru')

@section('title', 'Tambah Presensi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>
                    Tambah Presensi Manual
                </h5>
            </div>
            <div class="card-body">
                @if(true)
                    <form method="POST" action="{{ route('guru.presensi.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <select id="selectKelas" class="form-select" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas }}">{{ $kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Siswa <span class="text-danger">*</span></label>
                                    <select name="siswa_id" id="selectSiswa" class="form-select @error('siswa_id') is-invalid @enderror" required>
                                        <option value="">Pilih Siswa</option>
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id }}" data-kelas="{{ $siswa->kelas }}">
                                                {{ $siswa->nama }} ({{ $siswa->nisn }}) - {{ $siswa->kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('siswa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" value="{{ old('tanggal', today()->format('Y-m-d')) }}" 
                                           class="form-control @error('tanggal') is-invalid @enderror" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jam masuk <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_scan" value="{{ old('waktu_scan', '07:00') }}" 
                                           class="form-control @error('waktu_scan') is-invalid @enderror" required>
                                    @error('waktu_scan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: HH:MM (contoh: 07:30)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="">Pilih Status</option>
                                        <option value="tepat_waktu" {{ old('status') == 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                                        <option value="terlambat" {{ old('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                        <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                        <option value="alfa" {{ old('status') == 'alfa' ? 'selected' : '' }}>Alfa (Tidak Hadir)</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                      rows="3" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('guru.presensi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Presensi
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectKelas = document.getElementById('selectKelas');
    const selectSiswa = document.getElementById('selectSiswa');
    function filterSiswaByKelas() {
        const kelas = selectKelas.value;
        Array.from(selectSiswa.options).forEach(opt => {
            if (!opt.value) return; // skip placeholder
            opt.style.display = (opt.getAttribute('data-kelas') === kelas) ? '' : 'none';
        });
        selectSiswa.value = '';
    }
    selectKelas.addEventListener('change', filterSiswaByKelas);
    // Trigger filter on page load if needed
    filterSiswaByKelas();
});
</script>
@endpush 