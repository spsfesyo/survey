@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Undian Doorprize</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Pemenang Doorprize</h4>
                                <a href="#" class="btn btn-icon icon-left btn-success">
                                    <i class="fas fa-download"></i> Undi Doorprize
                                </a>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table-sm table">
                                        <thead>
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Nama Pemenang Undian</th>
                                                {{-- <th scope="col">Last</th>
                                                <th scope="col">Handle</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Mark</td>
                                                {{-- <td>Otto</td>
                                                <td>@mdo</td> --}}
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                                <td>Jacob</td>
                                                {{-- <td>Thornton</td>
                                                <td>@fat</td> --}}
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                                <td>Larry</td>
                                                {{-- <td>the Bird</td>
                                                <td>@twitter</td> --}}
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
