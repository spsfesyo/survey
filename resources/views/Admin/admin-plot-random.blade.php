@extends('layouts.app')

@section('title', 'General Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Plotingan Random Hadiah</h1>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">

                        </div>
                        <div class="card-body text-center">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h4>Select Periode & Wilayah/Area</h4>
                                    <div class="card-header-action">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <a href="#" class="btn btn-primary" data-toggle="modal"
                                        data-target="#modalPeriode">
                                        Select Periode Survey
                                    </a>

                                    <a href="#" class="btn btn-warning" data-toggle="modal"
                                        data-target="#modalWilayah">
                                        Select Wilayah/Area
                                    </a>
                                </div>


                                <div class="col-12 col-md-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Table Plotingan</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table-bordered table-md table">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Periode Survey</th>
                                                        <th>Provinsi</th>
                                                        <th>Kabupaten</th>
                                                        <th>Outlet</th>
                                                        <th>Hadiah</th>
                                                        <th>Status Assign</th>
                                                        <th>Status Plot</th>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Irwansyah Saputra</td>
                                                        <td>2017-01-09</td>
                                                        <td>
                                                            <div class="badge badge-success">Active</div>
                                                        </td>
                                                        <td><a href="#" class="btn btn-secondary">Detail</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Hasan Basri</td>
                                                        <td>2017-01-09</td>
                                                        <td>
                                                            <div class="badge badge-success">Active</div>
                                                        </td>
                                                        <td><a href="#" class="btn btn-secondary">Detail</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Kusnadi</td>
                                                        <td>2017-01-11</td>
                                                        <td>
                                                            <div class="badge badge-danger">Not Active</div>
                                                        </td>
                                                        <td><a href="#" class="btn btn-secondary">Detail</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td>Rizal Fakhri</td>
                                                        <td>2017-01-11</td>
                                                        <td>
                                                            <div class="badge badge-success">Active</div>
                                                        </td>
                                                        <td><a href="#" class="btn btn-secondary">Detail</a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <nav class="d-inline-block">
                                                <ul class="pagination mb-0">
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" tabindex="-1"><i
                                                                class="fas fa-chevron-left"></i></a>
                                                    </li>
                                                    <li class="page-item active"><a class="page-link" href="#">1 <span
                                                                class="sr-only">(current)</span></a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#">2</a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#"><i
                                                                class="fas fa-chevron-right"></i></a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tulisan di bawah tombol -->


                        </div>
                    </div>
                </div>

            </div>





        </section>
    </div>
@endsection

{{-- modal untuk select periode --}}

<div class="modal fade" id="modalPeriode" tabindex="-1" role="dialog" aria-labelledby="modalPeriodeLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPeriodeLabel">Modal Select Periode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Nama Survey</label>
                    <input type="text" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Awal</label>
                    <input type="date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- modal untuk select wilayah/area --}}

<div class="modal fade" id="modalWilayah" tabindex="-1" role="dialog" aria-labelledby="modalWilayahLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWilayahLabel">Modal Select Wilayah/Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Input Periode Survey</label>
                    <select class="custom-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>



                <div class="form-group">
                    <label>Pilih Provinsi</label>
                    <select class="custom-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush
