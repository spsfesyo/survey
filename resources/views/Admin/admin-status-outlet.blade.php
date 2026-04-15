@extends('layouts.app')

@section('title', 'General Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')

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
                <h1>Status Outlet</h1>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Tabel Status Blast Outlet Berhasil </h4>
                            <div class="d-flex">
                                {{-- Search Mobile Button --}}
                                <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none mr-2 ">
                                    <i class="fas fa-search text-primary"></i>
                                </a>

                                {{-- Search Box --}}
                                <form action="{{route('admin-status-outlet')}}" method="GET" class="search-element d-flex align-items-center mr-3">
                                    <input class="form-control" type="search" name="search" placeholder="Search"
                                        aria-label="Search" data-width="250" value="{{ request('search') }}">
                                    <button class="btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>

                                    {{-- <div class="search-backdrop"></div>
                                    <div class="search-result"></div> --}}
                                </form>

                                <form action="{{ route('status.export') }}" method="GET" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success mr-2">
                                        Export Excel
                                    </button>
                                </form>

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
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div class="table-responsive">
                                <table class="table-bordered table-md table">
                                    <tr>
                                        <th>No</th>
                                        Disable Kode Unik
                                        </a>
                                        @endif
                            </div> --}}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table-bordered table-md table">
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

                                            {{-- Provinsi via kabupaten --}}
                                            <td>{{ $item->kabupaten?->provinsi?->nama_provinsi }}</td>

                                            {{-- Nama kabupaten langsung dari master_kabupaten_id --}}
                                            <td>{{ $item->kabupaten?->nama_kabupaten }}</td>

                                            {{-- Area via kabupaten --}}
                                            <td>{{ $item->kabupaten?->area?->nama_area }}</td>
                                            @if ($role->id == 1)
                                                <td>
                                                    {{-- <form action="#" method="" class="d-inline">
                                                        @csrf --}}
                                                    <button type="button" data-id="{{ $item->id }}"
                                                        data-status="{{ $item->status_kode_unik }}"
                                                        class="btn btn-warning mr-2 fas fa-pencil-alt btn-edit"
                                                        data-toggle="modal" data-target="#modalEditPerdataOutlet">
                                                    </button>
                                                    {{-- </form> --}}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-right">

                            <nav class="d-inline-block">
                                {{ $status->onEachSide(0)->links() }}
                            </nav>
                        </div>
                        {{-- <div class="card-footer text-right">
                            {{ $status->links() }}
                        </div> --}}
                    </div>
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
