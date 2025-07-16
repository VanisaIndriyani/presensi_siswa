@extends('layouts.admin')

@section('content')
<style>
    .aksi-btn-group {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .aksi-btn {
        border: none;
        border-radius: 10px;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .aksi-btn:active { transform: scale(0.96);}
    .aksi-btn.info { background: #22d3ee; color: #fff; }
    .aksi-btn.warning { background: #facc15; color: #fff; }
    .aksi-btn.danger { background: #ef4444; color: #fff; }
    .aksi-btn.info:hover { background: #06b6d4; }
    .aksi-btn.warning:hover { background: #eab308; }
    .aksi-btn.danger:hover { background: #dc2626; }
    .aksi-btn i { font-size: 1.15rem; }
    .table td, .table th { vertical-align: middle !important; }
    
    /* Warna teks yang lebih lembut */
    .page-title {
        color: #7C3AED !important; /* Ungu untuk judul */
        font-weight: 700;
    }
    .table th {
        color: #3B82F6 !important; /* Biru untuk header tabel */
        font-weight: 600;
    }
    .table td {
        color: #4B5563 !important; /* Abu-abu medium untuk isi tabel */
    }
    .form-label {
        color: #7C3AED !important; /* Ungu untuk label */
        font-weight: 500;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 page-title">Daftar Guru</h1>
  
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>Jenis Kelamin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $i => $guru)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $guru->nama }}</td>
                            <td>{{ $guru->nip }}</td>
                            <td>{{ $guru->email }}</td>
                            <td>{{ $guru->jenis_kelamin }}</td>
                            <td class="text-center">
                                <div class="aksi-btn-group">
                                    <button class="aksi-btn info btn-show" data-id="{{ $guru->id }}" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="aksi-btn warning btn-edit" data-id="{{ $guru->id }}" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <button class="aksi-btn danger btn-hapus" data-id="{{ $guru->id }}" data-nama="{{ $guru->nama }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data guru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.guru.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">NIP</label>
          <input type="text" name="nip" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" class="form-select">
            <option value="">- Pilih -</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Guru -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formEdit">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" name="nama" id="edit-nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">NIP</label>
          <input type="text" name="nip" id="edit-nip" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" id="edit-email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" id="edit-jenis_kelamin" class="form-select">
            <option value="">- Pilih -</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Show Guru -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalShowLabel">Detail Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
          <li class="list-group-item"><b>Nama:</b> <span id="show-nama"></span></li>
          <li class="list-group-item"><b>NIP:</b> <span id="show-nip"></span></li>
          <li class="list-group-item"><b>Email:</b> <span id="show-email"></span></li>
          <li class="list-group-item"><b>Jenis Kelamin:</b> <span id="show-jenis_kelamin"></span></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Hapus Guru -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formHapus">
      @csrf
      @method('DELETE')
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel">Hapus Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus guru <b id="hapus-nama"></b>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
// Show detail guru
const showBtns = document.querySelectorAll('.btn-show');
showBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`{{ url('admin/guru') }}/${btn.dataset.id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('show-nama').textContent = data.nama;
                document.getElementById('show-nip').textContent = data.nip;
                document.getElementById('show-email').textContent = data.email;
                document.getElementById('show-jenis_kelamin').textContent = data.jenis_kelamin || '-';
                new bootstrap.Modal(document.getElementById('modalShow')).show();
            });
    });
});
// Edit guru
const editBtns = document.querySelectorAll('.btn-edit');
editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`{{ url('admin/guru') }}/${btn.dataset.id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-nama').value = data.nama;
                document.getElementById('edit-nip').value = data.nip;
                document.getElementById('edit-email').value = data.email;
                document.getElementById('edit-jenis_kelamin').value = data.jenis_kelamin || '';
                document.getElementById('formEdit').action = `{{ url('admin/guru') }}/${data.id}`;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});
// Hapus guru
const hapusBtns = document.querySelectorAll('.btn-hapus');
hapusBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('hapus-nama').textContent = btn.dataset.nama;
        document.getElementById('formHapus').action = `{{ url('admin/guru') }}/${btn.dataset.id}`;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});
</script>
@endsection 