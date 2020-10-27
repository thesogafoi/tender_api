<?php

namespace App\Imports;

use App\Advertise;
use App\WorkGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Morilog\Jalali\Jalalian;

class AdvertiseImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            $row['work_groups'] = explode(',', $row['work_groups']);
            $row['provinces'] = explode(',', $row['provinces']);

            $row['receipt_date'] = trim($row['receipt_date']);
            $row['invitation_date'] = trim($row['invitation_date']);
            $row['submit_date'] = trim($row['submit_date']);
            $row['start_date'] = trim($row['start_date']);
            $row['free_date'] = trim($row['free_date']);
            Validator::make($row->toArray(), [
                'title' => 'required',
                'invitation_date' => 'required|date_format:Y-m-d',
                'receipt_date' => 'date_format:Y-m-d',
                'submit_date' => 'date_format:Y-m-d',
                // 'start_date' => 'date_format:Y-m-d',
                'free_date' => 'date_format:Y-m-d',
                'description' => 'required',
                'is_nerve_center' => 'required',
                'type' => 'required',
                'adinviter_title' => 'required',
                'work_groups.*' => 'exists:App\WorkGroup,id|numeric',
                'provinces.*' => 'exists:App\Province,id|numeric',
            ])->validate();
            $this->extendValidation($rows, $row, $key);

            $advertise = new Advertise();
            $advertise->title = $row['title'];
            $advertise->resource = $row['resource'];
            $advertise->adinviter_title = $row['adinviter_title'];
            $advertise->type = $row['type'];
            $advertise->invitation_code = $row['invitation_code'];
            $advertise->receipt_date = $row['receipt_date'] != null ? Jalalian::fromFormat('Y-m-d', $row['receipt_date'])->toCarbon() : null;
            $advertise->invitation_date = $row['invitation_date'] != null ? Jalalian::fromFormat('Y-m-d', $row['invitation_date'])->toCarbon() : null;
            $advertise->submit_date = $row['submit_date'] != null ? Jalalian::fromFormat('Y-m-d', $row['submit_date'])->toCarbon() : null;
            $advertise->start_date = $row['start_date'] != null ? Jalalian::fromFormat('Y-m-d', $row['start_date'])->toCarbon() : null;
            $advertise->free_date = $row['free_date'] != null ? Jalalian::fromFormat('Y-m-d', $row['free_date'])->toCarbon() : null;

            $advertise->description = $row['description'];
            $advertise->is_nerve_center = $row['is_nerve_center'];

            $advertise->image = $row['image'] ?? null;
            $advertise->link = $row['link'] ?? null;
            $advertise->status = $row['status'] ?? 0;

            $advertise->adinviter_id = $row['adinviter_id'] ?? null;

            $advertise->save();

            foreach ($row['work_groups'] as $workGroupId) {
                $newkey = $key + 2;
                $workgroup = WorkGroup::where('id', $workGroupId)->first();
                if ($workgroup != null) {
                    if ($workgroup->type != $row['type']) {
                        $error = \Illuminate\Validation\ValidationException::withMessages([
                            "نوع گروه کاری وارد شده با نوع آگهی شماره {$newkey} همخوانی ندارد"
                        ]);
                        throw $error;
                    } else {
                        if ($workgroup->parent_id != null) {
                            $advertise->workGroups()->attach($workgroup->id);
                        }
                        if (!$advertise->workGroups->pluck('id')->contains($workgroup->parent_id)) {
                            $advertise->workGroups()->attach($workgroup->parent_id);
                        }
                    }
                }
            }
            $advertise->provinces()->sync($row['provinces']);
            $advertise->tender_code = $advertise->id . Jalalian::fromCarbon($advertise->created_at)->format('m')
            . Jalalian::fromCarbon($advertise->created_at)->format('d');
            $advertise->save();
        }
    }

    protected function extendValidation($rows, $row, $key)
    {
        $newkey = $key + 1;
        $advertise = Advertise::where('type', $row['type'])->
            where('invitation_code', $row['invitation_code'])->first();
        if ($advertise != null) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                "آگهی شماره {$newkey} دوبار وارد شده است"
            ]);
            throw $error;
        }
        if ((!is_numeric($row['work_groups'][0])) && $row['status'] == 1) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                "آگهی انتشار یافته نمیتواند فاقد دسته ی کاری باشد (شماره  {$newkey}  )"
            ]);
            throw $error;
        }

        // $rows
            // if ($advertise != null) {
            //     $error = \Illuminate\Validation\ValidationException::withMessages([
            //         "آگهی شماره {$key} دوبار وارد شده است"
            //     ]);
            //     throw $error;
            // }
    }
}
