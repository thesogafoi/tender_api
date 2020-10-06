<?php

namespace App\Imports;

use App\AdInviter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdInviterImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Validator::make($row->toArray(), [
                'name' => 'required',
            ])->validate();
            $adInviter = new AdInviter([
                'name' => $row['name']
            ]);
            $adInviter->save();
        }
    }
}
