@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
@endpush

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Masukkan Kode Unik Anda</h4>
        </div>

        <div class="card-body">
            {{-- <form method="POST" action="{{ route('submit-unique-code') }}" class="needs-validation" novalidate> --}}
                 <form method="POST" action="#" class="needs-validation" novalidate>
                @csrf

                <div class="form-group">
                    <label for="kode_unik">Kode Unik</label>
                    <input id="kode_unik" type="text" class="form-control @error('kode_unik') is-invalid @enderror"
                        name="kode_unik" maxlength="10" minlength="10" pattern="[A-Za-z0-9]{10}" tabindex="1" required
                        autofocus value="{{ old('kode_unik') }}">
                    <div class="invalid-feedback">
                        Kode Unik harus 10 karakter alfanumerik.
                    </div>
                    @error('kode_unik')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="2">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        </script>
    @endif

    <script>
        // Validasi form bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    </script>
@endpush
