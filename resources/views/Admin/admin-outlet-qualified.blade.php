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
                <h1>Status Outlet Telah Mengisi</h1>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header ">
                            <h4>Status Outlet Telah Mengisi </h4>






                            {{-- <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none mr-2 ">
                                    <i class="fas fa-search text-primary"></i>
                                </a>

                                <form action="{{ route('admin-status-outlet') }}" method="GET"
                                    class="search-element d-flex align-items-center mr-3">
                                    <input class="form-control" type="search" name="search" placeholder="Search"
                                        aria-label="Search" data-width="250" value="{{ request('search') }}">
                                    <button class="btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>

                                    <div class="search-backdrop"></div>
                                    <div class="search-result"></div>
                                </form> --}}


                            {{-- <form action="{{ route('status.export') }}" method="GET" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success mr-2">
                                        Export Excel
                                    </button>
                                </form> --}}
                            {{--
                                @if ($role->id == 1)
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">

                                            <form action="{{ route('outlet-regenerate-unik-code') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="fas fa-sync-alt mr-1"></i> Regenerate Kode
                                                </button>
                                            </form>

                                            <form action="{{ route('enable-kode-unik') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-info">
                                                    <i class="fas fa-toggle-on mr-1"></i> Enable Kode Unik
                                                </button>
                                            </form>

                                            <form action="{{ route('disable-kode-unik') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-ban mr-1"></i> Disable Kode Unik
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                @endif --}}
                        </div>

                        <div class="card-body">
                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Pilih Tanggal Periode</h4>
                                    </div>

                                    <div class="card-body">
                                        <form method="GET" action="">
                                            <div class="row align-items-end">

                                                <div class="form-group col-md-3">
                                                    <label>Tanggal Periode</label>
                                                    <input type="date" class="form-control" name="tanggal">
                                                </div>

                                                {{-- <div class="form-group col-md-3">
                                                    <label>Tanggal Selesai</label>
                                                    <input type="date" class="form-control" name="end_date">
                                                </div> --}}

                                                <div class="form-group col-md-2">
                                                    <button type="submit" class="btn btn-success btn-block">
                                                        Cari
                                                    </button>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table-bordered table-md table">
                                        <tr>
                                            <th>No</th>
                                            <th>Periode Mulai</th>
                                            <th>Periode Selesai</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>

                                        @forelse ($periode as $item)
                                            <tr>
                                                <td>{{ $periode->firstItem() + $loop->index }}</td>
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
                                                    <form action="{{route('report-outlet-qualified-export', $item->id)}}"
                                                        method="GET">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-file-excel mr-1"></i> Download
                                                        </button>
                                                    </form>

                                                    {{-- <a href="{{ route('admin.status.outlet.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <a href="{{ route('admin.status.outlet.destroy', $item->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a> --}}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data periode.</td>
                                            </tr>
                                        @endforelse

                                    </table>
                                </div>
                            </div>
                            {{-- <table class="table-bordered table-md table">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Outlet</th>
                                        <th>Nama Outlet Console</th>
                                        <th>Nomor Telp</th>
                                        <th>Kode Unik</th>
                                        <th>Status Kode Unik</th>
                                        <th>Provinsi</th>
                                        <th>Kabupaten</th>
                                        <th>Area</th>
                                        @if ($role->id == 1)
                                            <th>Action</th>
                                        @endif
                                    </tr>

                                    @foreach ($status as $index => $item)
                                        <tr>
                                            <td>{{ $status->firstItem() + $index }}</td>
                                            <td>{{ $item->nama_outlet }}</td>
                                            <td>{{ $item->sps_internal_name }}</td>
                                            <td>{{ $item->telepone_outlet }}</td>
                                            <td>{{ $item->kode_unik }}</td>
                                            <td>
                                                @if ($item->status_kode_unik === 'Y')
                                                    <div class="badge badge-success">Aktif</div>
                                                @elseif ($item->status_kode_unik === 'N')
                                                    <div class="badge badge-danger">Non Aktif</div>
                                                @else
                                                    <div class="badge badge-secondary">Tidak Diketahui</div>
                                                @endif
                                            </td>


                                            <td>{{ $item->kabupaten?->provinsi?->nama_provinsi }}</td>


                                            <td>{{ $item->kabupaten?->nama_kabupaten }}</td>


                                            <td>{{ $item->kabupaten?->area?->nama_area }}</td>
                                            @if ($role->id == 1)
                                                <td>

                                                    <button type="button" data-id="{{ $item->id }}"
                                                        data-status="{{ $item->status_kode_unik }}"
                                                        class="btn btn-warning mr-2 fas fa-pencil-alt btn-edit"
                                                        data-toggle="modal" data-target="#modalEditPerdataOutlet">
                                                    </button>

                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach


                                </table> aaaaaaaa --}}

                        </div>
                    </div>
                    {{-- <div class="card-footer text-right">

                            <nav class="d-inline-block">
                                {{ $status->onEachSide(0)->links() }}
                            </nav>
                        </div> --}}

                </div>
            </div>
        </section>
    </div>
@endsection

@include('components.modal.status-outlet.edit-perdata')

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
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let status = $(this).data('status');

            $('#edit_id').val(id);
            $('#edit_status').val(status);
        });
    </script>
@endpush


{{-- untuk menampilkan outlet yang sudah di blast ketika nomor sebelum di blokir --}}
