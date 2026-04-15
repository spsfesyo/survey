<!-- Modal Tambah Periode -->
<div class="modal fade" id="modalTambahPeriode" tabindex="-1" role="dialog" aria-labelledby="modalTambahPeriodeLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahPeriodeLabel">Tambah Periode Survey</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('periode-create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_periode">Nama Periode</label>
                        <input type="text" name="nama_periode" class="form-control" id="nama_periode"
                            placeholder="Masukkan nama periode" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control datepicker" id="tanggal_mulai"
                            placeholder="Pilih tanggal mulai" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control datepicker"
                            id="tanggal_selesai" placeholder="Pilih tanggal selesai" autocomplete="off" required>
                    </div>

                    {{-- <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div> --}}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
