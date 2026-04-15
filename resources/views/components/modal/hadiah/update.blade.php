<!-- Modal Update Hadiah -->
<div class="modal fade" id="modalUpdateHadiah" tabindex="-1" role="dialog" aria-labelledby="modalUpdateHadiahLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateHadiahLabel">
                    Update Hadiah
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formUpdateHadiah" action="{{ route('hadiah-update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="update_id" name="id">

                <div class="modal-body">

                    <!-- Dropdown Periode -->
                    <div class="form-group">
                        <label for="update_periode_survey_id">Pilih Periode</label>
                        <select class="form-control" id="update_periode_survey_id" name="periode_survey_id" required>
                            <option value="" disabled>-- Pilih Periode --</option>
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
                        <label for="update_nama_hadiah">Nama Hadiah</label>
                        <input type="text" class="form-control" id="update_nama_hadiah" name="nama_hadiah"
                            placeholder="Masukkan nama hadiah" required>
                    </div>

                    <!-- kode Hadiah -->
                    <div class="form-group">
                        <label for="update_kode_hadiah">Kode Hadiah</label>
                        <input type="text" class="form-control" id="update_kode_hadiah" name="kode_hadiah"
                            placeholder="Masukkan kode hadiah" required>
                    </div>


                    <!-- Jumlah -->
                    <div class="form-group">
                        <label for="jumlah_hadiah">Jumlah</label>
                        <input type="number" class="form-control" id="update_jumlah_hadiah" name="jumlah_hadiah"
                            placeholder="Masukkan jumlah hadiah" required>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="update_status">Status</label>
                        <select name="status" id="update_status" class="form-control" required>
                            <option value="Y">Aktif</option>
                            <option value="N">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>
