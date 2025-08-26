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
        return MasterOutletSurvey::with(['area.provinsi'])
            ->where('status_blast_wa', true)   // filter sesuai tabel
            ->orderBy('id', 'asc')
            ->get();
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->nama_outlet,
            $item->telepone_outlet,
            $item->kode_unik,
            $item->area?->provinsi?->nama_provinsi,
            $item->area?->nama_area,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Outlet',
            'Nomor Telp',
            'Kode Unik',
            'Provinsi',
            'Area',
        ];
    }
}

