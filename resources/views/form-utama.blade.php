<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- CSS Libraries -->

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
    <!-- /END GA -->


</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div
                        class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
                        <div class="login-brand">
                            <img src="{{ asset('img/logo_blesscon.svg') }}" alt="logo" width="300">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header" style="text-align: center;">
                                <h4>Bantu Kami Memahami Kebutuhan Anda</h4>
                            </div>

                            <div class="card-body">
                                @php
                                    $sessionData = session('form_utama', []);
                                @endphp

                                <form id="formSurvey" method="POST" action="{{ route('post-form-utama') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label>Nama Customer</label>
                                        <input type="text" class="form-control" name="nama_respondent"
                                            value="{{ $sessionData['nama_respondent'] ?? '' }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Nama Toko</label>
                                        <input type="text" class="form-control" name="nama_toko_respondent"
                                            value="{{ $sessionData['nama_toko_respondent'] ?? '' }}" required>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px;">
                                        <label>Provinsi Lokasi Toko</label>
                                        <select class="form-control selectric" name="provinsi_id" required>
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach ($provinsi as $prov)
                                                <option value="{{ $prov->id }}"
                                                    {{ ($sessionData['provinsi_id'] ?? '') == $prov->id ? 'selected' : '' }}>
                                                    {{ $prov->nama_provinsi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px;">
                                        <label>Kota/Kabupaten Lokasi Toko</label>
                                        <select class="form-control select-kota" name="kota_id" required>
                                            <option value="">-- Pilih Kota --</option>
                                            @foreach ($kota as $k)
                                                <option value="{{ $k->id }}"
                                                    {{ ($sessionData['kota_id'] ?? '') == $k->id ? 'selected' : '' }}>
                                                    {{ $k->kota }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Alamat Toko</label>
                                        <input type="text" class="form-control" name="alamat_toko_respondent"
                                            value="{{ $sessionData['alamat_toko_respondent'] ?? '' }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Nomor Telepon / WhatsApp</label>
                                        <input type="text" class="form-control" name="telepone_respondent"
                                            maxlength="15" pattern="[0-9]{10,15}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15);"
                                            value="{{ $sessionData['telepone_respondent'] ?? '' }}" required
                                            placeholder="Contoh: 081234567890">
                                    </div>
                                    @foreach ($pertanyaanFormUtama as $pertanyaan)
                                        @php
                                            $tipeId = $pertanyaan->tipePertanyaan->id ?? null;

                                            // Skip pertanyaan dengan tipe ID 5
                                            if ($tipeId == 5) {
                                                continue;
                                            }

                                            $jawabanUtama = $sessionData['pertanyaan_' . $pertanyaan->id] ?? null;
                                        @endphp

                                        <div style="margin-bottom: 20px;">
                                            <strong>{{ $pertanyaan->pertanyaan }}</strong><br>

                                            @switch($tipeId)
                                                @case(1)
                                                    {{-- Radio --}}
                                                    @foreach ($pertanyaan->options as $option)
                                                        @php
                                                            $isOther = $option->is_other;
                                                            $isChecked =
                                                                $jawabanUtama === strval($option->id) ||
                                                                $jawabanUtama === 'other_' . $option->id;
                                                        @endphp
                                                        @if ($isOther)
                                                            <label>
                                                                <input type="radio" name="pertanyaan_{{ $pertanyaan->id }}"
                                                                    value="other_{{ $option->id }}"
                                                                    {{ $loop->first ? 'required' : '' }}
                                                                    onchange="toggleOtherInput(this, '{{ $pertanyaan->id }}')"
                                                                    {{ $isChecked ? 'checked' : '' }}>
                                                                Lainnya:
                                                                <input type="text"
                                                                    name="pertanyaan_{{ $pertanyaan->id }}_other"
                                                                    id="other_input_{{ $pertanyaan->id }}"
                                                                    class="form-control mt-1"
                                                                    style="{{ $isChecked ? '' : 'display: none;' }}"
                                                                    placeholder="Tuliskan jawaban Anda"
                                                                    value="{{ $sessionData['pertanyaan_' . $pertanyaan->id . '_other'] ?? '' }}">
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="radio" name="pertanyaan_{{ $pertanyaan->id }}"
                                                                    value="{{ $option->id }}"
                                                                    {{ $loop->first ? 'required' : '' }}
                                                                    {{ $isChecked ? 'checked' : '' }}>
                                                                {{ $option->options ?? 'Opsi' }}
                                                            </label><br>
                                                        @endif
                                                    @endforeach
                                                @break

                                                @case(2)
                                                    {{-- Checkbox --}}
                                                    @php
                                                        $jawabanCheckbox = $jawabanUtama ?? [];
                                                        if (!is_array($jawabanCheckbox)) {
                                                            $jawabanCheckbox = [$jawabanCheckbox];
                                                        }
                                                    @endphp
                                                    @foreach ($pertanyaan->options as $option)
                                                        @php
                                                            $isOther = $option->is_other;
                                                            $isChecked =
                                                                in_array(strval($option->id), $jawabanCheckbox) ||
                                                                in_array('other_' . $option->id, $jawabanCheckbox);
                                                        @endphp
                                                        @if ($isOther)
                                                            <label>
                                                                <input type="checkbox"
                                                                    class="checkbox-group-{{ $pertanyaan->id }}"
                                                                    name="pertanyaan_{{ $pertanyaan->id }}[]"
                                                                    value="other_{{ $option->id }}"
                                                                    onchange="toggleOtherInput(this, '{{ $pertanyaan->id }}_{{ $option->id }}')"
                                                                    {{ $isChecked ? 'checked' : '' }}>
                                                                Lainnya:
                                                                <input type="text"
                                                                    name="pertanyaan_{{ $pertanyaan->id }}_other_{{ $option->id }}"
                                                                    id="other_input_{{ $pertanyaan->id }}_{{ $option->id }}"
                                                                    class="form-control mt-1"
                                                                    style="{{ $isChecked ? '' : 'display: none;' }}"
                                                                    placeholder="Tuliskan jawaban Anda"
                                                                    value="{{ $sessionData['pertanyaan_' . $pertanyaan->id . '_other_' . $option->id] ?? '' }}">
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox"
                                                                    class="checkbox-group-{{ $pertanyaan->id }}"
                                                                    name="pertanyaan_{{ $pertanyaan->id }}[]"
                                                                    value="{{ $option->id }}"
                                                                    {{ $isChecked ? 'checked' : '' }}>
                                                                {{ $option->options ?? 'Opsi' }}
                                                            </label><br>
                                                        @endif
                                                    @endforeach
                                                    <input type="hidden" name="required_checkbox[]"
                                                        value="{{ $pertanyaan->id }}">
                                                @break

                                                @case(3)
                                                    {{-- Text --}}
                                                    <input type="text" name="pertanyaan_{{ $pertanyaan->id }}"
                                                        class="form-control" value="{{ $jawabanUtama }}" required>
                                                @break

                                                @case(4)
                                                    {{-- Textarea --}}
                                                    <textarea name="pertanyaan_{{ $pertanyaan->id }}" class="form-control" required>{{ $jawabanUtama }}</textarea>
                                                @break

                                                @default
                                                    <p><i>Tipe pertanyaan tidak dikenali (ID: {{ $tipeId }})</i></p>
                                            @endswitch
                                        </div>
                                    @endforeach



                                    {{-- Blesscon / Superior --}}

                                    <div class="form-group" style="margin-top:15px; font-weight:600;">
                                        <label class="d-block" style="font-weight:600; font-size: 0.85rem;">
                                            Selama ini Anda membeli merek apa dari Produk Bata Ringan kami?*
                                        </label>

                                        @php
                                            $sessionData = session('form_utama', []);
                                            $selectedJenis = $sessionData['jenis_pertanyaan_id'] ?? null;
                                        @endphp

                                        @foreach ($merekBataRingan as $jenis)
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="merek_{{ $jenis->id }}"
                                                    name="jenis_pertanyaan_id" class="custom-control-input"
                                                    value="{{ $jenis->id }}"
                                                    {{ $loop->first ? 'required' : '' }}
                                                    {{ $selectedJenis == $jenis->id ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="merek_{{ $jenis->id }}"
                                                    style="font-weight:bold;">
                                                    {{ $jenis->jenis_pertanyaan }}
                                                </label>
                                            </div>
                                        @endforeach

                                    </div>


                                    <div class="form-group d-flex justify-content-between">
                                        <a href="#" class="btn btn-outline-warning"
                                            onclick="document.getElementById('formSurvey').reset(); return false;">Clear
                                            Form</a>
                                        <button type="submit" class="btn btn-primary">Next</button>
                                    </div>

                                </form>

                                {{-- <div class="form-group text-start">
                                    <a href="#" class="btn btn-outline-warning">Warning</a>
                                </div> --}}
                            </div>
                        </div>
                        {{-- <div class="simple-footer">
                            Copyright &copy; Stisla 2018
                        </div> --}}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JS Libraies -->

    <!-- Page Specific JS File -->

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
        $(document).ready(function() {
            $('.select-kota').select2({
                placeholder: "-- Pilih Kota --",
                allowClear: true
            });
        });
    </script>

    <script>
        function toggleOtherInput(input, id) {
            const textInput = document.getElementById('other_input_' + id);
            if (!textInput) return;

            if (input.type === 'radio') {
                // Sembunyikan semua input lainnya dengan nama serupa
                document.querySelectorAll(`input[id^="other_input_${id}"]`).forEach(el => el.style.display = 'none');
            }

            textInput.style.display = input.checked ? 'inline-block' : 'none';

            // Kosongkan input jika tidak dicentang
            if (!input.checked) {
                textInput.value = '';
            }
        }
    </script>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;

            // Cek setiap checkbox group wajib
            document.querySelectorAll('input[name="required_checkbox[]"]').forEach(function(hiddenInput) {
                let id = hiddenInput.value;
                let checkboxes = document.querySelectorAll('.checkbox-group-' + id);
                let checked = Array.from(checkboxes).some(cb => cb.checked);
                if (!checked) {
                    isValid = false;
                    alert('Mohon pilih minimal 1 opsi untuk pertanyaan wajib!');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>



</html>
