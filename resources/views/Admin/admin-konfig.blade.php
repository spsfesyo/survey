@extends('layouts.app')

@section('title', 'General Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif


    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Konfigurasi Survey</h1>
            </div>
            <div class="section-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Rekap & Analisis Survey</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-6">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h4>Periode Survey</h4>

                                                <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#modalTambahPeriode">
                                                    Tambah Periode
                                                </button>

                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table-striped table-md table">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama Periode</th>
                                                            <th>Tanggal Mulai</th>
                                                            <th>Tanggal Selesai</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                        @foreach ($periode as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $item->nama_periode }}</td>
                                                                <td>{{ $item->tanggal_mulai }}</td>
                                                                <td>{{ $item->tanggal_selesai }}</td>
                                                                <td>
                                                                    @if ($item->status == 'aktif')
                                                                        <div class="badge badge-success">Aktif</div>
                                                                    @else
                                                                        <div class="badge badge-danger">Non Aktif</div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-info btn-detail"
                                                                        data-id="{{ $item->id }}"
                                                                        data-nama="{{ $item->nama_periode }}"
                                                                        data-mulai="{{ $item->tanggal_mulai }}"
                                                                        data-selesai="{{ $item->tanggal_selesai }}"
                                                                        data-status="{{ $item->status }}"
                                                                        data-toggle="modal" data-target="#modalEditPeriode">
                                                                        Detail
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-footer text-right">
                                                <div class="d-inline-block">
                                                    {{ $periode->links() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-6">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h4>Hadiah Survey</h4>
                                                <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#modalTambahHadiah">
                                                    Tambah Hadiah
                                                </button>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table-striped table-md table">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Periode</th>
                                                            <th>Kode Hadiah</th>
                                                            <th>Nama Hadiah</th>
                                                            <th>Jumlah Hadiah</th>
                                                            <th>Status Hadiah</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        @foreach ($hadiah as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $item->periode->tanggal_mulai }} -
                                                                    {{ $item->periode->tanggal_selesai }}</td>
                                                                <td>{{ $item->kode_hadiah }}</td>
                                                                <td>{{ $item->nama_hadiah }}</td>
                                                                <td>{{ $item->jumlah_hadiah }}</td>
                                                                <td>
                                                                    @if ($item->status == 'Y')
                                                                        <div class="badge badge-success">Aktif</div>
                                                                    @else
                                                                        <div class="badge badge-danger">Non Aktif</div>
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <button class="btn btn-info btn-edit-hadiah"
                                                                        data-id="{{ $item->id }}"
                                                                        data-nama="{{ $item->nama_hadiah }}"
                                                                        data-jumlah-hadiah="{{ $item->jumlah_hadiah }}"
                                                                        data-status="{{ $item->status }}"
                                                                        data-periode-id="{{ $item->periode_survey_id }}"
                                                                        data-kode-hadiah="{{ $item->kode_hadiah }}">
                                                                        Detail
                                                                    </button>

                                                                    {{-- <form action="{{ route('hadiah.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form> --}}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-footer text-right">
                                                <div class="d-inline-block">
                                                    {{ $hadiah->links() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Plot Pemenang Survey</h4>
                                                <div class="card-header-form">
                                                    <form>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" placeholder="Search">
                                                            <div class="input-group-btn">
                                                                <button class="btn btn-primary"
                                                                    style="padding: 0.5rem 1rem; font-size: 1rem;">
                                                                    <i class="fas fa-search"></i>
                                                                </button>
                                                    </form>
                                                </div>
                                                <button type="button" class="btn btn-primary btn-lg ml-3"
                                                    data-toggle="modal" data-target="#modalTambahPlotLabel">
                                                    Tambah Plot
                                                </button>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table-striped table">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Periode Survey</th>
                                                    <th>Status Periode Survey</th>
                                                    <th>Provinsi</th>
                                                    <th>Kabupaten</th>
                                                    <th>Area</th>
                                                    <th>Nama Outlet</th>
                                                    <th>Nama Hadiah</th>
                                                    <th>Status Menang</th>
                                                    <th>Status Pengisian</th>
                                                    <th>Tanggal Pengisian</th>
                                                    <th>Aksi</th>
                                                </tr>
                                                @forelse ($plot as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $item->periode->tanggal_mulai ?? '' }} -
                                                            {{ $item->periode->tanggal_selesai ?? '' }}</td>
                                                        <td>
                                                            @if (optional($item->periode)->status == 'aktif')
                                                                <div class="badge badge-success">Aktif</div>
                                                            @else
                                                                <div class="badge badge-danger">Non Aktif</div>
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($item->provinsi)->nama_provinsi ?? '-' }}</td>
                                                        <td>{{ optional($item->kabupaten)->nama_kabupaten ?? '-' }}</td>
                                                        <td>{{ optional($item->area)->nama_area ?? '-' }}</td>
                                                        <td>{{ optional($item->outletSurvey)->nama_outlet ?? '-' }}</td>
                                                        <td>{{ optional($item->hadiah)->nama_hadiah ?? '-' }}</td>
                                                        <td>
                                                            <span
                                                                class="badge badge-{{ $item->is_winning == 'Y' ? 'success' : 'secondary' }}">
                                                                {{ $item->is_winning == 'Y' ? 'Menang' : 'Belum' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-{{ $item->status_respondent_assigned == 'Y' ? 'info' : 'light' }}">
                                                                {{ $item->status_respondent_assigned == 'Y' ? 'Sudah' : 'Belum' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ $item->tanggal_menang ? \Carbon\Carbon::parse($item->tanggal_pengisian)->format('d M Y') : '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($role->id == 1)
                                                                <button
                                                                    class="btn btn-info btn-sm fas fa-pencil-alt btnEdit"
                                                                    data-id="{{ $item->id }}"
                                                                    data-periode="{{ $item->periode_survey_id }}"
                                                                    data-hadiah="{{ $item->hadiah_id }}">
                                                                </button>

                                                                <button class="btn btn-danger btn-sm btnDelete fas fa-trash"
                                                                    data-id="{{ $item->id }}">

                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">Belum ada data plot</td>
                                                    </tr>
                                                @endforelse
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <div class="d-inline-block">
                                            {{ $plot->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section>
    </div>

@endsection
@include('components.modal.periode.create')
@include('components.modal.periode.update')
@include('components.modal.hadiah.create')
@include('components.modal.hadiah.update')
@include('components.modal.plot.create')
@include('components.modal.plot.update')




@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    </script>

    <script>
        $(document).ready(function() {
            $('.btn-detail').on('click', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const mulai = $(this).data('mulai');
                const selesai = $(this).data('selesai');
                const status = $(this).data('status');

                $('#edit_id').val(id);
                $('#edit_nama_periode').val(nama);
                $('#edit_tanggal_mulai').val(mulai);
                $('#edit_tanggal_selesai').val(selesai);
                $('#edit_status').val(status);
            });
        });
    </script>

    {{-- untuk update di update hadiah --}}
    <script>
        document.querySelectorAll('.btn-edit-hadiah').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;

                // Isi otomatis ke input modal
                document.getElementById('update_id').value = data.id;
                document.getElementById('update_nama_hadiah').value = data.nama;
                document.getElementById('update_kode_hadiah').value = data.kodeHadiah;
                document.getElementById('update_jumlah_hadiah').value = data.jumlahHadiah;
                document.getElementById('update_status').value = data.status;


                // Set periode yang sesuai
                document.getElementById('update_periode_survey_id').value = data.periodeId;

                // Tampilkan modal update
                $('#modalUpdateHadiah').modal('show');
            });
        });

        // Cegah input huruf/simbol di jumlah
        document.getElementById('update_jumlah').addEventListener('keydown', function(e) {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) {
                e.preventDefault();
            }
        });
    </script>


    {{-- untuk tambah plot hadiah --}}
    <script>
        document.getElementById('filter_type').addEventListener('change', function() {
            const val = this.value;
            document.querySelectorAll('.filter-field').forEach(el => el.style.display = 'none');
            if (val === 'provinsi') document.getElementById('filter_provinsi').style.display = 'block';
            if (val === 'kabupaten') document.getElementById('filter_kabupaten').style.display = 'block';
            if (val === 'area') document.getElementById('filter_area').style.display = 'block';
        });
    </script>

    {{-- untuk  --}}

    <script src="{{ asset('js/konfig/plot/update.js') }}"></script>


    {{-- untuk delete plot --}}

    {{-- <script>
        $(document).on("click", ".btnDelete", function() {
            let id = $(this).data("id");

            Swal.fire({
                title: 'Hapus Plot?',
                text: "Data plot ini akan dihapus dan stok hadiah dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `/plot/${id}/delete`,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(res) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        },
                        error: function() {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });

                }
            });
        });
    </script>> --}}
    <script>
        $(document).on("click", ".btnDelete", function() {
            let id = $(this).data("id");

            Swal.fire({
                title: 'Hapus Plot?',
                text: "Data plot ini akan dihapus dan stok hadiah dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `/plot/${id}/delete`,
                        type: "POST",
                        data: {
                            _method: "DELETE",
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(res) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Gagal!',
                                xhr.responseJSON?.message ?? 'Terjadi kesalahan.',
                                'error'
                            );
                        }
                    });

                }
            });
        });
    </script>
@endpush
