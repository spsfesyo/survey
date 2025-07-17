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
                                        <select id="provinsi" name="provinsi" class="form-control">
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach ($provinsi as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama_provinsi }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px;">
                                        <label>Kabupaten/Kota Lokasi Toko</label>
                                        <select class="form-control" name="kabupaten" id="kabupaten" required>
                                            <option value="">-- Pilih Kabupaten --</option>
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
                                                    {{-- Multiple Select (Checkbox) --}}
                                                    @php
                                                        $jawabanCheckbox = $jawabanUtama ?? [];
                                                        if (!is_array($jawabanCheckbox)) {
                                                            $jawabanCheckbox = [$jawabanCheckbox];
                                                        }
                                                        $batas = $pertanyaan->batas_pilihan;
                                                    @endphp

                                                    <div class="checkbox-wrapper" data-question-id="{{ $pertanyaan->id }}"
                                                        data-batas-pilihan="{{ $batas }}">
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
                                                                    {{ $option->options }}
                                                                </label><br>
                                                            @endif
                                                        @endforeach
                                                    </div>

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

                                    <div class="form-group" style="margin-top:15px; font-weight:600;">
                                        <label class="d-block" style="font-weight:600; font-size: 0.85rem;">
                                            Selama ini Anda membeli merek apa dari Produk Bata Ringan kami?*
                                        </label>

                                        @php
                                            $selectedJenis = $sessionData['jenis_pertanyaan_id'] ?? null;
                                        @endphp

                                        @foreach ($merekBataRingan as $jenis)
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="merek_{{ $jenis->id }}"
                                                    name="jenis_pertanyaan_id" class="custom-control-input"
                                                    value="{{ $jenis->id }}" {{ $loop->first ? 'required' : '' }}
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts - URUTAN PENTING -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- HAPUS DUPLIKAT JQUERY -->
     {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- Custom Scripts -->
    <script>
        $('#provinsi').on('change', function() {
            let provinsiId = $(this).val();
            $('#kabupaten').html('<option>Loading...</option>');

            if (provinsiId) {
                $.ajax({
                    url: '/get-kabupaten/' + provinsiId,
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#kabupaten').empty().append(
                                '<option value="">-- Pilih Kabupaten --</option>');
                            $.each(response.data, function(i, kab) {
                                $('#kabupaten').append(
                                    `<option value="${kab.id}">${kab.nama_kabupaten}</option>`
                                    );
                            });
                        } else {
                            alert('Gagal mengambil data kabupaten');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengambil data kabupaten.');
                    }
                });
            } else {
                $('#kabupaten').html('<option value="">-- Pilih Kabupaten --</option>');
            }
        });
        // Checkbox limit functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.checkbox-wrapper').forEach(function(wrapper) {
                const batas = parseInt(wrapper.dataset.batasPilihan);
                const id = wrapper.dataset.questionId;
                const checkboxes = wrapper.querySelectorAll('.checkbox-group-' + id);

                if (!batas || checkboxes.length === 0) return;

                function enforceLimit() {
                    const checked = Array.from(checkboxes).filter(cb => cb.checked);
                    if (checked.length >= batas) {
                        checkboxes.forEach(cb => {
                            if (!cb.checked) cb.disabled = true;
                        });
                    } else {
                        checkboxes.forEach(cb => cb.disabled = false);
                    }
                }

                checkboxes.forEach(cb => cb.addEventListener('change', enforceLimit));
                enforceLimit();
            });

            // Form validation
            document.querySelector('form')?.addEventListener('submit', function(e) {
                let isValid = true;
                document.querySelectorAll('input[name="required_checkbox[]"]').forEach(function(input) {
                    const id = input.value;
                    const wrapper = document.querySelector(
                        `.checkbox-wrapper[data-question-id="${id}"]`);
                    const batas = parseInt(wrapper?.dataset.batasPilihan || 0);
                    const checkboxes = wrapper?.querySelectorAll(`.checkbox-group-${id}`);
                    const checkedCount = Array.from(checkboxes ?? []).filter(cb => cb.checked)
                        .length;

                    if (batas && checkedCount < batas) {
                        isValid = false;
                        alert(
                            `Pertanyaan membutuhkan minimal ${batas} pilihan. Anda baru memilih ${checkedCount}.`
                        );
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });

        // Toggle other input
        function toggleOtherInput(input, id) {
            const textInput = document.getElementById('other_input_' + id);
            if (!textInput) return;

            if (input.type === 'radio') {
                document.querySelectorAll(`input[id^="other_input_${id}"]`).forEach(el => el.style.display = 'none');
            }

            textInput.style.display = input.checked ? 'inline-block' : 'none';

            if (!input.checked) {
                textInput.value = '';
            }
        }

        // Form validation for required checkboxes
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;

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

    <!-- Load scripts.js TERAKHIR untuk menghindari tooltip error -->
    {{-- <script>
        // Override tooltip initialization to prevent errors
        $(document).ready(function() {
            // Only initialize tooltip if bootstrap is loaded
            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    </script> --}}
    <script src="{{ asset('js/scripts.js') }}"></script>

</body>

</html>
