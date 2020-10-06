<?php

namespace App\Imports;

use App\Advertise;
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
            Validator::make($row->toArray(), [
                'title' => 'required',
                'invitation_date' => 'required',
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
            $advertise->adinviter_id = $row['adinviter_id'];
            $advertise->adinviter_title = $row['adinviter_title'];
            $advertise->type = $row['type'];
            $advertise->invitation_code = $row['invitation_code'];

            $advertise->receipt_date = Jalalian::fromFormat('Y-m-d', $row['receipt_date'])->toCarbon() ?? null;
            $advertise->invitation_date = Jalalian::fromFormat('Y-m-d', $row['invitation_date'])->toCarbon() ?? null;
            $advertise->submit_date = Jalalian::fromFormat('Y-m-d', $row['submit_date'])->toCarbon() ?? null;
            $advertise->start_date = Jalalian::fromFormat('Y-m-d', $row['start_date'])->toCarbon() ?? null;
            $advertise->free_date = Jalalian::fromFormat('Y-m-d', $row['free_date'])->toCarbon() ?? null;

            $advertise->description = $row['description'];
            $advertise->is_nerve_center = $row['is_nerve_center'];

            $advertise->image = $row['image'] ?? null;
            $advertise->link = $row['link'] ?? null;
            $advertise->status = $row['status'] ?? 0;

            $advertise->adinviter_id = $row['adinviter_id'] ?? null;
            $advertise->save();
            $advertise->workGroups()->sync($row['work_groups']);
            $advertise->provinces()->sync($row['provinces']);
            $advertise->tender_code = $advertise->id . Jalalian::fromCarbon($advertise->created_at)->format('m')
            . Jalalian::fromCarbon($advertise->created_at)->format('d');
            $advertise->save();
        }
    }

    protected function extendValidation($rows, $row, $key)
    {
        $advertise = Advertise::where('type', $row['type'])->
            where('invitation_code', $row['invitation_code'])->first();
        if ($advertise != null) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                "آگهی شماره {$key} دوبار وارد شده است"
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
