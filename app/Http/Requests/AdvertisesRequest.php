<?php

namespace App\Http\Requests;

use App\Advertise;
use App\WorkGroup;
use Illuminate\Foundation\Http\FormRequest;
use Morilog\Jalali\Jalalian;

class AdvertisesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:1000',
            'invitation_date' => 'required',
            'description' => 'required',
            'is_nerve_center' => 'required',
            'adinviter_title' => 'required',
            'invitation_code' => 'required',
            'type' => 'required',
            'status' => 'required'
        ];
    }

    public function newPersist()
    {
        $this->extendValidation();
        try {
            $advertise = new Advertise();
            if ($this->hasFile('image_file')) {
            }
            $advertise->title = $this->title;
            $advertise->invitation_date = $this->invitation_date == null || $this->invitation_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->invitation_date)->toCarbon();
            $advertise->receipt_date = $this->receipt_date == null || $this->receipt_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->receipt_date)->toCarbon();
            $advertise->submit_date = $this->submit_date == null || $this->submit_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->submit_date)->toCarbon();
            $advertise->start_date = $this->start_date == null || $this->start_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->start_date)->toCarbon();
            $advertise->free_date = $this->free_date == null || $this->free_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->free_date)->toCarbon();
            $advertise->adinviter_title = $this->adinviter_title;
            $advertise->invitation_code = $this->invitation_code;
            $advertise->description = $this->description;
            $advertise->resource = $this->resource;
            $advertise->is_nerve_center = $this->is_nerve_center;
            $advertise->type = $this->type;
            $advertise->link = $this->link;
            $advertise->status = $this->status == 1 ? 1 : 0;
            $advertise->save();
            $workGroupsRequest = [];

            $workGroupsRequest = $this->work_groups;

            if ($workGroupsRequest != null) {
                foreach ($workGroupsRequest as $id) {
                    $workGroup = WorkGroup::where('id', $id)->first();
                    if ($workGroup->parent_id != null) {
                        array_push($workGroupsRequest, (int) $workGroup->parent_id);
                    }
                }
                $workGroupsRequest = array_unique($workGroupsRequest);
                $advertise->workGroups()->sync($workGroupsRequest);
            }
            $advertise->provinces()->sync($this->provinces);

            $advertise->tender_code = $advertise->id . Jalalian::fromCarbon($advertise->created_at)->format('m')
            . Jalalian::fromCarbon($advertise->created_at)->format('d');
            $advertise->save();
            if ($advertise->tender_code == null || $advertise->tender_code == '') {
                abort(500);
            }

            return $advertise->id;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updatePersist($advertise)
    {
        $this->extendValidation();
        $advertise->title = $this->title;
        $advertise->invitation_date = $this->invitation_date == null || $this->invitation_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->invitation_date)->toCarbon();
        $advertise->receipt_date = $this->receipt_date == null || $this->receipt_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->receipt_date)->toCarbon();
        $advertise->submit_date = $this->submit_date == null || $this->submit_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->submit_date)->toCarbon();
        $advertise->start_date = $this->start_date == null || $this->start_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->start_date)->toCarbon();
        $advertise->free_date = $this->free_date == null || $this->free_date == '' ? null : Jalalian::fromFormat('Y-m-d', $this->free_date)->toCarbon();
        $advertise->adinviter_title = $this->adinviter_title;
        $advertise->invitation_code = $this->invitation_code;
        $advertise->description = $this->description;
        $advertise->status = $this->status;
        $advertise->resource = $this->resource;
        $advertise->is_nerve_center = $this->is_nerve_center;
        $advertise->image = $this->image;
        $advertise->type = $this->type;
        $advertise->link = $this->link;
        $advertise->save();

        $workGroupsRequest = [];
        $workGroupsRequest = $this->work_groups;
        foreach ($workGroupsRequest as $id) {
            $workGroup = WorkGroup::where('id', $id)->first();
            if ($workGroup->parent_id != null) {
                array_push($workGroupsRequest, (int) $workGroup->parent_id);
            }
        }
        $workGroupsRequest = array_unique($workGroupsRequest);
        $advertise->workGroups()->sync($workGroupsRequest);
        $advertise->provinces()->sync($this->provinces);

        $advertise->save();

        return $advertise->id;
    }

    protected function extendValidation()
    {
        if ($this->method() == 'POST') {
            $advertise = Advertise::where('invitation_code', $this->invitation_code)->
            where('type', $this->type)->first();
            if ($advertise != null) {
                $error = \Illuminate\Validation\ValidationException::withMessages([
                    'آگهی تکراری است'
                ]);
                throw $error;
            }
        }
        if (($this->work_groups == [] || empty($this->work_groups)) && $this->status == 1) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'آگهی انتشار یافته نمیتواند فاقد دسته ی کاری باشد'
            ]);
            throw $error;
        }
    }
}
