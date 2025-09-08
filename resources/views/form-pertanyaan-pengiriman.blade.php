<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Form Pertanyaan</title>
    <link rel="icon" type="image/png" href="{{ asset('img/icon-prima-no-bg.png') }}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                            <img src="{{ asset('img/logo-superior-prima-sukses-no-bg.png') }}" alt="logo"
                                width="300">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Pengiriman Produk Bata Ringan (Merek Blesscon atau Superior)</h4>
                            </div>

                            <div class="card-body">

                                <div class="form-group">
                                    {{-- pertanyaan pertama --}}
                                    @php
                                        $sessionData = session('form_pengiriman', []);
                                    @endphp
                                    <form method="POST" id="formSurveyPengiriman"
                                        action="{{ route('post-form-pertanyaan-pengiriman') }}">
                                        @csrf
                                        @foreach ($pertanyaanFormPengiriman as $pertanyaan)
                                            <div style="margin-bottom: 20px;">
                                                <strong>{{ $pertanyaan->pertanyaan }}</strong><br>

                                                @php
                                                    $tipeId = $pertanyaan->tipePertanyaan->id ?? null;
                                                    $jawabanUtama =
                                                        $sessionData['pertanyaan_' . $pertanyaan->id] ?? null;
                                                @endphp

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
                                                                    <input type="radio"
                                                                        name="pertanyaan_{{ $pertanyaan->id }}"
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
                                                                    <input type="radio"
                                                                        name="pertanyaan_{{ $pertanyaan->id }}"
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
                                                                        in_array(
                                                                            strval($option->id),
                                                                            $jawabanCheckbox,
                                                                        ) ||
                                                                        in_array(
                                                                            'other_' . $option->id,
                                                                            $jawabanCheckbox,
                                                                        );
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

                                        <div class="form-group d-flex justify-content-between">
                                            <a href="#" class="btn btn-outline-warning"
                                                onclick="document.getElementById('formSurveyPengiriman').reset(); return false;">Clear
                                                Form</a>

                                            <div class="d-flex" style="gap: 10px;">
                                                <a href="{{ route('form-pertanyaan-harga') }}"
                                                    class="btn btn-light">Back</a>
                                                <button type="submit" class="btn btn-primary">Next</button>
                                            </div>
                                        </div>
                                    </form>

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

    <!-- JS Libraies -->

    {{-- <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;

            document.querySelectorAll('input[name="required_checkbox[]"]').forEach(function(hiddenInput) {
                let id = hiddenInput.value;
                let checkboxes = document.querySelectorAll('.checkbox-group-' + id);
                let checked = Array.from(checkboxes).some(cb => cb.checked);

                if (!checked) {
                    isValid = false;
                    alert('Mohon pilih minimal satu opsi untuk pertanyaan wajib.');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>

    <!-- Validasi Batas Pilihan -->
    <script>
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

                // ✅ Validasi radio wajib
                document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                    const name = radio.name;
                    const radios = document.getElementsByName(name);
                    if ([...radios].every(r => !r.checked)) {
                        isValid = false;
                        messages.push(
                            `Pertanyaan ${name.replace('pertanyaan_', '')} wajib dipilih salah satu.`
                        );
                    }
                });

                // ✅ Validasi text & textarea wajib
                document.querySelectorAll('input[type="text"], textarea').forEach(function(field) {
                    if (field.hasAttribute('required') && field.value.trim() === '') {
                        isValid = false;
                        messages.push(
                            `Pertanyaan ${field.name.replace('pertanyaan_', '')} wajib diisi.`);
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert(messages.join("\n"));
                }
            });
        });
    </script> --}}

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

            document.querySelectorAll('input[name="required_checkbox[]"]').forEach(function(hiddenInput) {
                let id = hiddenInput.value;
                let checkboxes = document.querySelectorAll('.checkbox-group-' + id);
                let checked = Array.from(checkboxes).some(cb => cb.checked);

                if (!checked) {
                    isValid = false;
                    alert('Mohon pilih minimal satu opsi untuk pertanyaan wajib.');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>

    <!-- Validasi Batas Pilihan -->
    <script>
        $(document).ready(function() {
            $('.checkbox-wrapper').each(function() {
                const $wrapper = $(this);
                const batas = parseInt($wrapper.data('batas-pilihan'));
                const questionId = $wrapper.data('question-id');
                const $checkboxes = $wrapper.find('.checkbox-group-' + questionId);

                if (!batas) return;

                function enforceLimit() {
                    const checkedCount = $checkboxes.filter(':checked').length;

                    if (checkedCount >= batas) {
                        // Disable checkbox yang belum dicentang
                        $checkboxes.each(function() {
                            if (!$(this).is(':checked')) {
                                $(this).prop('disabled', true);
                            }
                        });
                    } else {
                        // Enable semua kalau belum sampai batas
                        $checkboxes.prop('disabled', false);
                    }
                }

                // Jalankan saat load & setiap kali ada perubahan
                enforceLimit();
                $checkboxes.on('change', enforceLimit);
            });

            // Validasi saat form disubmit
            $('form').on('submit', function(e) {
                let isValid = true;

                $('input[name="required_checkbox[]"]').each(function() {
                    const id = $(this).val();
                    const $checkboxes = $('.checkbox-group-' + id);
                    const batas = parseInt($('.checkbox-wrapper[data-question-id="' + id + '"]')
                        .data('batas-pilihan'));
                    const checkedCount = $checkboxes.filter(':checked').length;

                    if (checkedCount < batas) {
                        isValid = false;
                        alert(
                            `Pertanyaan dengan pilihan checkbox membutuhkan minimal ${batas} pilihan. Anda baru memilih ${checkedCount}.`
                        );
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>


    <!-- Page Specific JS File -->

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>




</html>
