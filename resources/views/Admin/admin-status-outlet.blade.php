@extends('layouts.app')

@section('title', 'General Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
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
                            <a href="{{ route('status.export') }}" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table-bordered table-md table">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Outlet</th>
                                        <th>Nomor Telp</th>
                                        <th>Kode Unik</th>
                                        <th>Provinsi</th>
                                        <th>Area</th>
                                        {{-- <th>Action</th> --}}
                                    </tr>

                                    @foreach ($status as $index => $item)
                                        <tr>
                                            <td>{{ $status->firstItem() + $index }}</td>
                                            <td>{{ $item->nama_outlet }}</td>
                                            <td>{{ $item->telepone_outlet }}</td>
                                            <td>{{ $item->kode_unik }}</td>
                                            <td>{{ $item->area?->provinsi?->nama_provinsi }}</td>
                                            <td>{{ $item->area?->nama_area }}</td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-right">

                            <nav class="d-inline-block">
                                {{ $status->links() }}
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

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush


{{-- untuk menampilkan outlet yang sudah di blast ketika nomor sebelum di blokir --}}
