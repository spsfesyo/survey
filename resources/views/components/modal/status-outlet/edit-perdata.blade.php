<div class="modal fade" id="modalEditPerdataOutlet" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('outlet-status-kode-update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5>Edit Status</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <select name="status" id="edit_status" class="form-control">
                        <option value="Y">Aktif</option>
                        <option value="N">Nonaktif</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
