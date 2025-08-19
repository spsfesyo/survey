@extends('layouts.app')

@section('title', 'General Dashboard')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Kontrol Blasting WA</h1>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="mb-3">Data tersisa untuk blast: <strong>{{ $remaining ?? 0 }}</strong></h4>

                            {{-- Tombol Mulai Blast --}}
                            <form action="{{ route('admin-blast-wa.post') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-circle mb-2"
                                    style="width: 80px; height: 80px; font-size: 24px;">
                                    <i class="fas fa-paper-plane" style="font-size: 32px;"></i>
                                </button>
                                <div style="font-weight: bold; font-size: 18px;">Mulai Blast Batch Berikutnya</div>
                            </form>

                            {{-- Tombol Pause --}}
                            <form action="{{ route('admin-blast.pause') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-warning rounded mb-2" style="width: 150px;">
                                    <i class="fas fa-pause"></i> Pause
                                </button>
                                <div>Hentikan sementara blasting</div>
                            </form>

                            {{-- Tombol Resume --}}
                            <form action="{{ route('admin-blast.resume') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success rounded mb-2" style="width: 150px;">
                                    <i class="fas fa-play"></i> Resume
                                </button>
                                <div>Lanjutkan blasting yang dihentikan</div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush
