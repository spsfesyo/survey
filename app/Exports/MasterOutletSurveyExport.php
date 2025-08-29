<?php

namespace App\Exports;

use App\Models\MasterOutletSurvey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterOutletSurveyExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MasterOutletSurvey::with([
            // 'area.provinsi'

            // 'area.provinsi',
            // 'kabupaten.provinsi',
            // 'kabupaten.area'

            'kabupaten.provinsi',
            'kabupaten.area'

        ])
            ->where('status_blast_wa', true)   // filter sesuai tabel
            ->orderBy('id', 'asc')
            ->get();
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->nama_outlet,
            $item->sps_internal_name,
            $item->telepone_outlet,
            $item->kode_unik,
            // $item->area?->provinsi?->nama_provinsi,

            // $item->area?->nama_area,

            $item->kabupaten?->provinsi?->nama_provinsi,
            $item->kabupaten?->nama_kabupaten,
            $item->kabupaten?->area?->nama_area,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Outlet',
            'Nama Outlet Console',
            'Nomor Telp',
            'Kode Unik',
            'Provinsi',
            'Kabupaten',
            'Area',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // ambil 1000 record per batch
    }
}
