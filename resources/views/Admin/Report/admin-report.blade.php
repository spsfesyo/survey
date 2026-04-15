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
                <h1>Dashboard</h1>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Report All Section</h4>
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
                                        @foreach ($periode as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
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
                                                    <form action="{{ route('admin-report-export', $item->id) }}" method="GET">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary">Detail</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <nav class="d-inline-block">
                                    {{ $periode->links() }}
                                </nav>
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
