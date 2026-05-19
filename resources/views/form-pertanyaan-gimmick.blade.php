<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/icon-prima-no-bg.png') }}">
    <title>Form Pertanyaan Gimmick</title>

    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-image: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%) !important;
        }

        .section {
            background-color: transparent !important;
        }
    </style>

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <!-- Start GA -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">

                        <div class="login-brand">
                            <img src="{{ asset('img/logo-superior-prima-sukses-no-bg.png') }}" width="300">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header text-center">
                                <h4>Konfirmasi Gimmick</h4>
                                <small>Langkah terakhir sebelum submit</small>
                            </div>

                            <div class="card-body">

                                <form method="POST" action="{{ route('submit-final') }}">
                                    @csrf
                                    {{-- dibawah ini untuk hidden menyimpan session sps_name untuk dikembalikan dalam bentuk token ke dalam visit sales --}}
                                    <input type="hidden" name="token" value="{{ session('raw_token') }}">
                                    {{-- <input type="hidden" name="visit_sales_form_id"
                                        value="{{ session('visit_sales_form_id') }}"> --}}

                                    <div class="form-group">
                                        <label style="font-weight:600;">
                                            Apakah Anda memberikan gimmick ke customer?
                                        </label>

                                        @php
                                            $gimmick = session('form_gimmick.is_gimmick');
                                        @endphp

                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="gimmick_yes" name="is_gimmick"
                                                class="custom-control-input" value="1"
                                                {{ $gimmick === 1 ? 'checked' : '' }} required>

                                            <label class="custom-control-label" for="gimmick_yes">
                                                Ya
                                            </label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="gimmick_no" name="is_gimmick"
                                                class="custom-control-input" value="0"
                                                {{ $gimmick === 0 ? 'checked' : '' }}>

                                            <label class="custom-control-label" for="gimmick_no">
                                                Tidak
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        {{-- <a href="#" class="btn btn-outline-warning"
                                            onclick="document.getElementById('formSurveyPelayanan').reset(); return false;">Clear
                                            Form</a> --}}

                                        <div class="d-flex" style="gap: 10px;">
                                            <a href="{{ route('form-pertanyaan-pelayanan') }}"
                                                class="btn btn-light">Back</a>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- JS -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>

</html>
