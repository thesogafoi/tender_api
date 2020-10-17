<?php

namespace App\Exports;

use App\WorkGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorkGroupExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return WorkGroup::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'parent_id',
            'type',
            'title',
            'image',
            'status',
            'priorty',
        ];
    }
}
