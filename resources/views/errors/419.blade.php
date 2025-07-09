@extends('layouts.error')

@section('title', '419')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="page-error">
        <div class="page-inner">
            <h1>419</h1>
            <div class="page-description">
                Session has expired or the page has expired due to inactivity.
            </div>
            <div class="page-search">
                <div class="mt-3">
                    <a href="{{ route('home') }}">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->

    <!-- Page Specific JS File -->
@endpush
