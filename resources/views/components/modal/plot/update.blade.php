<div class="modal fade" id="modalEditPlot" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditPlot" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Plot Hadiah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="edit_id">

                    <div class="form-group">
                        <label>Periode (Aktif)</label>
                        <select id="edit_periode_id" class="form-control" name="periode_survey_id">
                            @foreach ($periodeAktif as $p)
                                <option value="{{ $p->id }}">
                                    {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Hadiah</label>
                        <select id="edit_hadiah_id" class="form-control" name="hadiah_id">
                            @foreach ($hadiahAktif as $h)
                                <option value="{{ $h->id }}">{{ $h->nama_hadiah }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </div>
        </form>
    </div>
</div>
