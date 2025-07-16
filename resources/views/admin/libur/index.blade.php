@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hari Libur & Pengaturan Jam Masuk</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Hari Libur</button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Daftar Hari Libur</h5>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($liburs as $i => $libur)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $libur->tanggal }}</td>
                            <td>{{ $libur->keterangan }}</td>
                            <td>
                                <button class="btn btn-sm btn-info text-white btn-show" data-id="{{ $libur->id }}">Lihat</button>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $libur->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="{{ $libur->id }}" data-tanggal="{{ $libur->tanggal }}">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data hari libur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Hari Libur -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.libur.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Hari Libur -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formEdit">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" id="edit-tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" id="edit-keterangan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Show Hari Libur -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalShowLabel">Detail Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
          <li class="list-group-item"><b>Tanggal:</b> <span id="show-tanggal"></span></li>
          <li class="list-group-item"><b>Keterangan:</b> <span id="show-keterangan"></span></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Hapus Hari Libur -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formHapus">
      @csrf
      @method('DELETE')
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel">Hapus Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus hari libur <b id="hapus-tanggal"></b>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
const liburBaseUrl = "{{ url('admin/libur') }}";
// Show detail hari libur
const showBtns = document.querySelectorAll('.btn-show');
showBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`${liburBaseUrl}/${btn.dataset.id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('show-tanggal').textContent = data.tanggal;
                document.getElementById('show-keterangan').textContent = data.keterangan;
                new bootstrap.Modal(document.getElementById('modalShow')).show();
            });
    });
});
// Edit hari libur
const editBtns = document.querySelectorAll('.btn-edit');
editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`${liburBaseUrl}/${btn.dataset.id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-tanggal').value = data.tanggal;
                document.getElementById('edit-keterangan').value = data.keterangan;
                document.getElementById('formEdit').action = `${liburBaseUrl}/${data.id}`;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});
// Hapus hari libur
const hapusBtns = document.querySelectorAll('.btn-hapus');
hapusBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('hapus-tanggal').textContent = btn.dataset.tanggal;
        document.getElementById('formHapus').action = `${liburBaseUrl}/${btn.dataset.id}`;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});
</script>
@endsection 