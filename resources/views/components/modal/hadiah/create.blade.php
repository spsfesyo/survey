<!-- Modal Tambah Hadiah -->

<style>
    /* Hilangkan spinner di input number */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>


<div class="modal fade" id="modalTambahHadiah" tabindex="-1" role="dialog" aria-labelledby="modalTambahHadiahLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header ">
                <h5 class="modal-title" id="modalTambahHadiahLabel">
                    Tambah Hadiah
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('hadiah-create') }}" method="POST">
                @csrf
                <div class="modal-body">


                    <!-- Dropdown Periode -->
                    <div class="form-group">
                        <label for="periode_survey_id">Pilih Periode</label>
                        <select class="form-control" id="periode_survey_id" name="periode_survey_id" required>
                            <option value="" disabled selected>-- Pilih Periode --</option>
                            @foreach ($periode as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nama_periode }}
                                    ({{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y') }}
                                    - {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <!-- Nama Hadiah -->
                    <div class="form-group">
                        <label for="nama_hadiah">Nama Hadiah</label>
                        <input type="text" class="form-control" id="nama_hadiah" name="nama_hadiah"
                            placeholder="Masukkan nama hadiah" required>
                    </div>

                    <!-- Kode Hadiah -->
                    <div class="form-group">
                        <label for="kode_hadiah">Kode Hadiah</label>
                        <input type="text" class="form-control" id="kode_hadiah" name="kode_hadiah"
                            placeholder="Masukkan kode hadiah" required>
                    </div>

                    <!-- Jumlah -->
                    <div class="form-group">
                        <label for="jumlah_hadiah">Jumlah</label>
                        <input type="number" class="form-control no-spinner" id="jumlah_hadiah" name="jumlah_hadiah"
                            placeholder="Masukkan jumlah hadiah" required>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Y">Aktif</option>
                            <option value="N">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
    document.getElementById('jumlah_hadiah').addEventListener('keydown', function(e) {
        // Blokir e, E, +, - dan .
        if (['e', 'E', '+', '-', '.'].includes(e.key)) {
            e.preventDefault();
        }
    });
</script>
