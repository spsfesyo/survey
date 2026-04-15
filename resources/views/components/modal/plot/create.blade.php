<div class="modal fade" id="modalTambahPlotLabel" tabindex="-1" role="dialog" aria-labelledby="modalBuatPlotLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('plot-store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuatPlotLabel">Buat Plot Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pilihan Parameter -->
                    <div class="form-group">
                        <label for="filter_type">Filter Berdasarkan</label>
                        <select class="form-control" id="filter_type" name="filter_type" required>
                            <option value="">-- Pilih Parameter --</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="kabupaten">Kabupaten</option>
                            <option value="area">Area</option>
                        </select>
                    </div>

                    <!-- Provinsi -->
                    <div class="form-group filter-field" id="filter_provinsi" style="display:none;">
                        <label for="provinsi_id">Pilih Provinsi</label>
                        <select name="provinsi_id" class="form-control">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($provinsi as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_provinsi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kabupaten -->
                    <div class="form-group filter-field" id="filter_kabupaten" style="display:none;">
                        <label for="kabupaten_id">Pilih Kabupaten</label>
                        <select name="kabupaten_id" class="form-control">
                            <option value="">-- Pilih Kabupaten --</option>
                            @foreach ($kabupaten as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area -->
                    <div class="form-group filter-field" id="filter_area" style="display:none;">
                        <label for="area_id">Pilih Area</label>
                        <select name="area_id" class="form-control">
                            <option value="">-- Pilih Area --</option>
                            @foreach ($area as $a)
                                <option value="{{ $a->id }}">{{ $a->nama_area }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Periode Survey -->
                    <div class="form-group">
                        <label for="periode_survey_id">Periode Survey</label>
                        <select name="periode_survey_id" class="form-control" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach ($periodeAktif as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nama_periode }} ({{ $p->tanggal_mulai }} - {{ $p->tanggal_selesai }})
                                    {{ $p->status == 'aktif' ? '✅ Aktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <!-- Jumlah Data -->
                    <div class="form-group">
                        <label for="jumlah_outlet">Jumlah Outlet yang akan di-Plot</label>
                        <input type="number" min="1" class="form-control" name="jumlah_outlet"
                            id="jumlah_outlet" placeholder="Masukkan jumlah outlet" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Plot</button>
                </div>
            </form>
        </div>
    </div>
</div>
