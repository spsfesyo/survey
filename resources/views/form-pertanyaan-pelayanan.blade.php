<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Form Pertanyaan</title>

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            <div class="card-header">
                                <h4>Pelayanan/Service Produk Bata Ringan (Merek Blesscon atau Superior)</h4>
                            </div>

                            <div class="card-body">
                                @php
                                    $sessionData = session('form_pelayanan', []);
                                @endphp
                                <form id="formSurveyPelayanan" method="POST"
                                    action="{{ route('post-form-pertanyaan-pelayanan') }}">
                                    @csrf

                                    @foreach ($pertanyaanFormPelayanan as $pertanyaan)
                                        <div style="margin-bottom: 20px;">
                                            <strong>{{ $pertanyaan->pertanyaan }}</strong><br>

                                            @php
                                                $tipeId = (int) ($pertanyaan->tipePertanyaan->id ?? null);
                                                $jawabanUtama = $sessionData['pertanyaan_' . $pertanyaan->id] ?? null;
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
                                                                        value="{{ session('form_pelayanan')['pertanyaan_' . $pertanyaan->id . '_other_' . $option->id] ?? '' }}">
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
                                                    {{-- Text Input --}}
                                                    <input type="text" name="pertanyaan_{{ $pertanyaan->id }}"
                                                        class="form-control"
                                                        value="{{ old('pertanyaan_' . $pertanyaan->id, $jawabanUtama) }}"
                                                        required>
                                                @break

                                                @case(4)
                                                    {{-- Textarea --}}
                                                    <textarea name="pertanyaan_{{ $pertanyaan->id }}" class="form-control" rows="3" required>{{ old('pertanyaan_' . $pertanyaan->id, $jawabanUtama) }}</textarea>
                                                @break

                                                @default
                                                    <p class="text-danger">Tipe pertanyaan tidak dikenali.</p>
                                            @endswitch
                                        </div>
                                    @endforeach

                                    <div class="form-group d-flex justify-content-between">
                                        <a href="#" class="btn btn-outline-warning"
                                            onclick="document.getElementById('formSurveyPelayanan').reset(); return false;">Clear
                                            Form</a>

                                        <div class="d-flex" style="gap: 10px;">
                                            <a href="{{ route('form-pertanyaan-pengiriman') }}"
                                                class="btn btn-light">Back</a>
                                            <button type="submit" class="btn btn-primary">Submit</button>
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

    <!-- Validasi & Logika Form -->
    {{-- <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script> --}}

    <!-- Validasi & Logika Form -->
    <script>
        function toggleOtherInput(input, id) {
            const el = document.getElementById('other_input_' + id);
            if (!el) return;

            if (input.checked) {
                el.style.display = 'inline-block';
            } else {
                el.style.display = 'none';
                el.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.checkbox-wrapper').forEach(function(wrapper) {
                const batas = parseInt(wrapper.dataset.batasPilihan);
                const id = wrapper.dataset.questionId;
                const checkboxes = wrapper.querySelectorAll('.checkbox-group-' + id);

                function enforceLimit() {
                    const checked = Array.from(checkboxes).filter(cb => cb.checked);
                    checkboxes.forEach(cb => cb.disabled = checked.length >= batas && !cb.checked);
                }

                checkboxes.forEach(cb => cb.addEventListener('change', enforceLimit));
                enforceLimit();
            });

            document.querySelector('form')?.addEventListener('submit', function(e) {
                let isValid = true;
                let messages = [];

                document.querySelectorAll('input[name="required_checkbox[]"]').forEach(function(input) {
                    const id = input.value;
                    const wrapper = document.querySelector('.checkbox-wrapper[data-question-id="' +
                        id + '"]');
                    const batas = parseInt(wrapper?.dataset.batasPilihan || 0);
                    const checkboxes = wrapper?.querySelectorAll('.checkbox-group-' + id);
                    const checkedCount = Array.from(checkboxes ?? []).filter(cb => cb.checked)
                        .length;

                    if (batas && checkedCount < batas) {
                        isValid = false;
                        messages.push(
                            `Pertanyaan ${id} minimal memilih ${batas} jawaban. Anda memilih ${checkedCount}.`
                            );
                    }

                    if (!batas && checkedCount === 0) {
                        isValid = false;
                        messages.push(`Pertanyaan ${id} wajib memilih minimal 1 opsi.`);
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        html: messages.join('<br>')
                    });
                }
            });
        });
    </script>

</html>
