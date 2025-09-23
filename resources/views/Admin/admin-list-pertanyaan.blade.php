@extends('layouts.app')

@section('title', 'List Pertanyaan')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    {{--
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif --}}

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>List Pertanyaan Survey</h1>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>List Pertanyaan Survey</h4>
                            <a href="{{ route('export-survey-pdf') }}" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                        <div class="card-body text-center">

                            @foreach ($pertanyaans as $pertanyaan)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <strong>{{ $loop->iteration }}. {{ $pertanyaan->pertanyaan }}</strong>
                                    </div>
                                    <div class="card-body">
                                        @if ($pertanyaan->options->count() > 0)
                                            <ul>
                                                @foreach ($pertanyaan->options as $option)
                                                    <li>{{ $option->options }}
                                                        @if ($option->is_other)
                                                            <em>(Lainnya)</em>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <em>Tidak ada opsi (mungkin jawaban isian bebas)</em>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

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
