<?php

namespace App\Imports;

use App\WorkGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WorkGroupImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($row['id'] == null) {
                Validator::make($row->toArray(), [
                    'title' => 'required',
                    'parent_id' => 'nullable|numeric',
                    'priorty' => 'required',
                    'type' => 'required'
                ])->validate();
                $this->extendValidation($row, $key);
                $workGroup = new WorkGroup();
                $workGroup->title = $row['title'];
                $workGroup->parent_id = $row['parent_id'];
                $workGroup->priorty = $row['priorty'];
                $workGroup->type = $row['type'];
                $workGroup->image = $row['image'];

                $workGroup->save();
            }
        }
    }

    protected function extendValidation($row, $key)
    {
        $workgroups = WorkGroup::where('type', $row['type'])->
            where('title', $row['title'])->first();
        if ($workgroups != null) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                "گروه کاری شماره {$key} دوبار وارد شده است"
            ]);
            throw $error;
        }
    }
}
