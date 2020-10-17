<?php

namespace App\Exports;

use App\WorkGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParentWorkGroupExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return WorkGroup::where('parent_id', null)->get();
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
