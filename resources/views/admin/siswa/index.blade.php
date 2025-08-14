@extends('layouts.admin')

@section('content')
<style>
    .aksi-btn {
        border: none;
        border-radius: 10px;
        width: 36px; /* dari 52px */
        height: 36px; /* dari 52px */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem; /* dari 1.5rem */
        margin-right: 6px;
        margin-bottom: 2px;
        transition: box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }

    .aksi-btn:active {
        transform: scale(0.96);
    }

    .aksi-btn.info { background: #22d3ee; color: #fff; }
    .aksi-btn.warning { background: #facc15; color: #fff; }
    .aksi-btn.danger { background: #ef4444; color: #fff; }
    .aksi-btn.success { background: #10b981; color: #fff; }

    .aksi-btn.info:hover { background: #06b6d4; }
    .aksi-btn.warning:hover { background: #eab308; }
    .aksi-btn.danger:hover { background: #dc2626; }
    .aksi-btn.success:hover { background: #059669; }

    .aksi-btn i {
        font-size: 0.9rem; /* dari 1.3rem */
    }

    .table td, .table th {
        vertical-align: middle !important;
    }
    
    /* Warna teks yang lebih lembut */
    .page-title {
        color: #7C3AED !important; /* Ungu untuk judul */
        font-weight: 700;
    }
    .table thead th {
        background: #007bff !important; /* Blue background */
        color: #ffffff !important; /* White text */
        font-weight: 600;
        border: 1px solid #0056b3;
        padding: 12px 8px;
        text-align: center;
        vertical-align: middle;
    }
    .table td {
        color: #4B5563 !important; /* Abu-abu medium untuk isi tabel */
    }
    .form-label {
        color: #7C3AED !important; /* Ungu untuk label */
        font-weight: 500;
    }
    
    /* Search box styling */
    #searchInput {
        border-color: #e5e7eb;
        border-radius: 8px 0 0 8px;
    }
    
    #searchInput:focus {
        border-color: #7C3AED;
        box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
    }
    
    #searchBtn {
        border-color: #e5e7eb;
        border-radius: 0 8px 8px 0;
        border-left: none;
    }
    
    #searchBtn:hover {
        background-color: #7C3AED;
        border-color: #7C3AED;
        color: white;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 page-title">Daftar Siswa</h1>
    <div class="d-flex gap-2">
        <div class="input-group" style="width: 300px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari siswa...">
            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Siswa</button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $kelas => $list)
                        <tr>
                            <td colspan="6" class="bg-light fw-bold">Kelas {{ $kelas }}</td>
                        </tr>
                        @foreach($list as $i => $siswa)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>{{ $siswa->nisn }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                                <td class="text-center">
                                    <button class="aksi-btn info btn-show" data-id="{{ $siswa->id }}" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="aksi-btn warning btn-edit" data-id="{{ $siswa->id }}" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <a href="{{ route('admin.siswa.idcard', $siswa->id) }}" class="aksi-btn success" target="_blank" title="Cetak ID Card">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                    <button class="aksi-btn danger btn-hapus" data-id="{{ $siswa->id }}" data-nama="{{ $siswa->nama }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.siswa.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">NISN</label>
          <input type="text" name="nisn" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kelas</label>
          <input type="text" name="kelas" class="form-control" required>
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

<!-- Modal Edit Siswa -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formEdit" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" name="nama" id="edit-nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">NISN</label>
          <input type="text" name="nisn" id="edit-nisn" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kelas</label>
          <input type="text" name="kelas" id="edit-kelas" class="form-control" required>
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

<!-- Modal Show Siswa -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalShowLabel">Detail Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
          <li class="list-group-item"><b>Nama:</b> <span id="show-nama"></span></li>
          <li class="list-group-item"><b>NISN:</b> <span id="show-nisn"></span></li>
          <li class="list-group-item"><b>Kelas:</b> <span id="show-kelas"></span></li>
          <li class="list-group-item"><b>Jenis Kelamin:</b> <span id="show-jenis_kelamin"></span></li>

        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Hapus Siswa -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formHapus">
      @csrf
      @method('DELETE')
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel">Hapus Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus siswa <b id="hapus-nama"></b>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
const baseUrl = "{{ url('admin/siswa') }}";
// Show detail siswa
const showBtns = document.querySelectorAll('.btn-show');
showBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`${baseUrl}/${btn.dataset.id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('show-nama').textContent = data.nama;
                document.getElementById('show-nisn').textContent = data.nisn;
                document.getElementById('show-kelas').textContent = data.kelas;
                document.getElementById('show-jenis_kelamin').textContent = data.jenis_kelamin || '-';

                new bootstrap.Modal(document.getElementById('modalShow')).show();
            });
    });
});
// Edit siswa
const editBtns = document.querySelectorAll('.btn-edit');
editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`${baseUrl}/${btn.dataset.id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-nama').value = data.nama;
                document.getElementById('edit-nisn').value = data.nisn;
                document.getElementById('edit-kelas').value = data.kelas;
                document.getElementById('edit-jenis_kelamin').value = data.jenis_kelamin || '';

                document.getElementById('formEdit').action = `${baseUrl}/${data.id}`;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});
// Hapus siswa
const hapusBtns = document.querySelectorAll('.btn-hapus');
hapusBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('hapus-nama').textContent = btn.dataset.nama;
        document.getElementById('formHapus').action = `${baseUrl}/${btn.dataset.id}`;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});

// Search functionality
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const tableRows = document.querySelectorAll('tbody tr');

function performSearch() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    tableRows.forEach(row => {
        const nama = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const nisn = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
        const kelas = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        
        // Skip header rows (kelas headers)
        if (row.querySelector('td[colspan]')) {
            return;
        }
        
        if (nama.includes(searchTerm) || nisn.includes(searchTerm) || kelas.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide kelas headers based on visible rows
    const kelasHeaders = document.querySelectorAll('tbody tr td[colspan]');
    kelasHeaders.forEach(header => {
        const nextRows = [];
        let currentRow = header.parentElement.nextElementSibling;
        
        // Find all rows until next header or end
        while (currentRow && !currentRow.querySelector('td[colspan]')) {
            if (currentRow.style.display !== 'none') {
                nextRows.push(currentRow);
            }
            currentRow = currentRow.nextElementSibling;
        }
        
        // Show header only if there are visible rows in this kelas
        if (nextRows.length > 0) {
            header.parentElement.style.display = '';
        } else {
            header.parentElement.style.display = 'none';
        }
    });
}

// Search on button click
searchBtn.addEventListener('click', performSearch);

// Search on Enter key
searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// Clear search on input change (optional - remove if you want manual search only)
searchInput.addEventListener('input', function() {
    if (this.value === '') {
        // Show all rows when search is cleared
        tableRows.forEach(row => {
            row.style.display = '';
        });
    }
});
</script>
@endsection 