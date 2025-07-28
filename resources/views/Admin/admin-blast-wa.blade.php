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

                        </div>
                        <div class="card-body text-center">

                            <!-- Tombol lingkaran -->
                            <form action="{{ route('admin-blast-wa.post') }}" method="POST">
                                @csrf
                                <button class="btn btn-primary rounded-circle mb-4"
                                    style="width: 80px; height: 80px; font-size: 24px;">
                                    <i class="fas fa-paper-plane" style="font-size: 32px;" type="submit"></i> {{-- Ganti ikon sesuai kebutuhan --}}
                                </button>
                            </form>

                                <!-- Tulisan di bawah tombol -->
                                <div>
                                    <p class="mb-0" style="font-size: 25px; font-weight:bold;">Tekan Untuk Mulai Blasting
                                        Pesan
                                    </p>
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
