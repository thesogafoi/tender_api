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
                $newKey = 2 + $key;
                $workGroup = new WorkGroup();
                $workGroup->title = $row['title'];
                $workGroup->parent_id = $row['parent_id'];
                $parentWorkGroup = WorkGroup::where('id', $row['parent_id'])->first();
                if ($parentWorkGroup == null && $row['parent_id'] != null) {
                    $error = \Illuminate\Validation\ValidationException::withMessages([
                        "سرگروه انتخاب شده اشتباه است. خط {$newKey} اکسل خود را مجددا بررسی کنید "
                    ]);
                    throw $error;
                } else {
                    if ($parentWorkGroup != null && $parentWorkGroup->type != $row['type']) {
                        $error = \Illuminate\Validation\ValidationException::withMessages([
                            "نوع سرگروه و نوع دسته کاری با هم مطابقت ندارد . خط {$newKey} اکسل خود را مجددا بررسی کنید"
                        ]);
                        throw $error;
                    }

                    if ($parentWorkGroup != null && $parentWorkGroup->parent_id != null) {
                        $error = \Illuminate\Validation\ValidationException::withMessages([
                            "سرگروه انتخاب شده قبلا در سیستم به عنوان زیرگروه ثبت شده است. خط {$newKey} اکسل خود را مجددا بررسی کنید "
                        ]);
                        throw $error;
                    }
                }
                $workGroup->priorty = $row['priorty'];
                $workGroup->type = $row['type'];
                $workGroup->image = $row['image'];
                $workGroup->save();
            }
        }
    }

    protected function extendValidation($row, $key)
    {
        $newKey = $key + 2;
        $workgroups = WorkGroup::where('type', $row['type'])->
            where('title', $row['title'])->first();
        if ($workgroups != null) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                "گروه کاری در خط {$newKey} قبلا در سیستم ثبت شده است."
            ]);
            throw $error;
        }
    }
}
