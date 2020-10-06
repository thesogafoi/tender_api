<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AdvertiseFilter extends Filter
{
    public $filters = ['type', 'range_invitation_date', 'invitation_code', 'tender_code',
        'work_groups', 'provinces', 'is_nerve_center', 'title', 'status', 'resource', 'adinviter_title', 'range_submit_date',
        'range_receipt_date', 'range_start_date', 'range_free_date', 'range_created_at',
        'invitation_date', 'submit_date',
        'receipt_date', 'start_date', 'free_date', 'description', 'searchTerm'];

    public function type()
    {
        return $this->builder->where('type', $this->request->type);
    }

    public function description()
    {
        return $this->builder->where('description', 'LIKE', '%' . $this->request->description . '%');
    }

    public function tender_code()
    {
        return $this->builder->where('tender_code', $this->request->tender_code);
    }

    public function provinces()
    {
        return $this->builder->whereHas('provinces', function (Builder $model) {
            $model->where(function ($workGroup) {
                $workGroup->whereIn('provinces.id', $this->request->provinces);
            });
        });

        return $this->builder;
    }

    public function work_groups()
    {
        return $this->builder->whereHas('workGroups', function (Builder $model) {
            $model->where(function ($workGroup) {
                $workGroup->whereIn('work_groups.id', $this->request->work_groups);
            });
        });
    }

    public function is_nerve_center()
    {
        return $this->builder->where('is_nerve_center', $this->request->is_nerve_center);
    }

    public function title()
    {
        return $this->builder->where('title', 'LIKE', '%' . $this->request->title . '%');
    }

    public function status()
    {
        return $this->builder->where('status', $this->request->status);
    }

    public function invitation_code()
    {
        return $this->builder->where('invitation_code', $this->request->invitation_code);
    }

    public function resource()
    {
        return $this->builder->where('resource', 'LIKE', '%' . $this->request->resource . '%');
    }

    public function adinviter_title()
    {
        return $this->builder->where('adinviter_title', 'LIKE', '%' . $this->request->adinviter_title . '%');
    }

    public function range_submit_date()
    {
        // empty range date
        if (empty($this->request->range_submit_date)) {
            return $this->builder;
        } elseif (empty($this->request->range_submit_date['first']) && !empty($this->request->range_submit_date['second'])) { // empty range first date
            return $this->builder->where('submit_date', '<=', $this->request->range_submit_date['second']);
        } elseif (empty($this->request->range_submit_date['second']) && !empty($this->request->range_submit_date['first'])) { // empty range second date
            return $this->builder->where('submit_date', '>=', $this->request->range_submit_date['first']);
        } elseif (!empty($this->request->range_submit_date['first']) &&
        !empty($this->request->range_submit_date['first'])) { // none of two range is empty
            return $this->builder->where('submit_date', '>=', $this->request->range_submit_date['first'])
        ->where('submit_date', '<=', $this->request->range_submit_date['second']);
        } else {
            abort(500);
        }
    }

    public function range_receipt_date()
    {
        // empty range date
        if (empty($this->request->range_receipt_date)) {
            return $this->builder;
        } elseif (empty($this->request->range_receipt_date['first']) && !empty($this->request->range_receipt_date['second'])) { // empty range first date
            return $this->builder->where('receipt_date', '<=', $this->request->range_receipt_date['second']);
        } elseif (empty($this->request->range_receipt_date['second']) && !empty($this->request->range_receipt_date['first'])) { // empty range second date
            return $this->builder->where('receipt_date', '>=', $this->request->range_receipt_date['first']);
        } elseif (!empty($this->request->range_receipt_date['first']) &&
        !empty($this->request->range_receipt_date['first'])) { // none of two range is empty
            return $this->builder->where('receipt_date', '>=', $this->request->range_receipt_date['first'])
        ->where('receipt_date', '<=', $this->request->range_receipt_date['second']);
        } else {
            abort(500);
        }
    }

    public function range_start_date()
    {
        // empty range date
        if (empty($this->request->range_start_date)) {
            return $this->builder;
        } elseif (empty($this->request->range_start_date['first']) && !empty($this->request->range_start_date['second'])) { // empty range first date
            return $this->builder->where('start_date', '<=', $this->request->range_start_date['second']);
        } elseif (empty($this->request->range_start_date['second']) && !empty($this->request->range_start_date['first'])) { // empty range second date
            return $this->builder->where('start_date', '>=', $this->request->range_start_date['first']);
        } elseif (!empty($this->request->range_start_date['first']) &&
        !empty($this->request->range_start_date['first'])) { // none of two range is empty
            return $this->builder->where('start_date', '>=', $this->request->range_start_date['first'])
        ->where('start_date', '<=', $this->request->range_start_date['second']);
        } else {
            abort(500);
        }
    }

    public function range_free_date()
    {
        // empty range date
        if (empty($this->request->range_free_date)) {
            return $this->builder;
        } elseif (empty($this->request->range_free_date['first']) && !empty($this->request->range_free_date['second'])) { // empty range first date
            return $this->builder->where('free_date', '<=', $this->request->range_free_date['second']);
        } elseif (empty($this->request->range_free_date['second']) && !empty($this->request->range_free_date['first'])) { // empty range second date
            return $this->builder->where('free_date', '>=', $this->request->range_free_date['first']);
        } elseif (!empty($this->request->range_free_date['first']) &&
        !empty($this->request->range_free_date['first'])) { // none of two range is empty
            return $this->builder->where('free_date', '>=', $this->request->range_free_date['first'])
        ->where('free_date', '<=', $this->request->range_free_date['second']);
        } else {
            abort(500);
        }
    }

    public function range_invitation_date()
    {
        // empty range date
        if (empty($this->request->range_invitation_date)) {
            return $this->builder;
        } elseif (empty($this->request->range_invitation_date['first']) && !empty($this->request->range_invitation_date['second'])) { // empty range first date
            return $this->builder->where('invitation_date', '<=', $this->request->range_invitation_date['second']);
        } elseif (empty($this->request->range_invitation_date['second']) && !empty($this->request->range_invitation_date['first'])) { // empty range second date
            return $this->builder->where('invitation_date', '>=', $this->request->range_invitation_date['first']);
        } elseif (!empty($this->request->range_invitation_date['first']) &&
        !empty($this->request->range_invitation_date['first'])) { // none of two range is empty
            return $this->builder->where('invitation_date', '>=', $this->request->range_invitation_date['first'])
        ->where('invitation_date', '<=', $this->request->range_invitation_date['second']);
        } else {
            abort(500);
        }
    }

    public function range_created_at()
    {
        // empty range date
        if (empty($this->request->range_created_at)) {
            return $this->builder;
        } elseif (empty($this->request->range_created_at['first']) && !empty($this->request->range_created_at['second'])) { // empty range first date
            return $this->builder->where('created_at', '<=', $this->request->range_created_at['second']);
        } elseif (empty($this->request->range_created_at['second']) && !empty($this->request->range_created_at['first'])) { // empty range second date
            return $this->builder->where('created_at', '>=', $this->request->range_created_at['first']);
        } elseif (!empty($this->request->range_created_at['first']) &&
        !empty($this->request->range_created_at['first'])) { // none of two range is empty
            return $this->builder->where('created_at', '>=', $this->request->range_created_at['first'])
        ->where('created_at', '<=', $this->request->range_created_at['second']);
        } else {
            abort(500);
        }
    }

    public function invitation_date()
    {
        return $this->builder->where('invitation_date', '=', $this->request->invitation_date);
    }

    public function submit_date()
    {
        return $this->builder->where('submit_date', '=', $this->request->submit_date);
    }

    public function receipt_date()
    {
        return $this->builder->where('receipt_date', '=', $this->request->receipt_date);
    }

    public function start_date()
    {
        return $this->builder->where('start_date', '=', $this->request->start_date);
    }

    public function free_date()
    {
        return $this->builder->where('free_date', '=', $this->request->free_date);
    }

    public function searchTerm()
    {
        return $this->builder->where('invitation_code', 'LIKE', $this->request->searchTerm)
        ->orWhere('tender_code', '=', $this->request->searchTerm)
        ->orWhere('title', 'LIKE', '%' . $this->request->searchTerm . '%')
        ->orWhere('resource', 'LIKE', '%' . $this->request->searchTerm . '%')
        ->orWhere('adinviter_title', 'LIKE', '%' . $this->request->searchTerm . '%')
        ->orWhere('invitation_date', 'LIKE', '%' . $this->request->searchTerm . '%')
        ->orWhere('description', 'LIKE', '%' . $this->request->searchTerm . '%')
        ->orWhere(function ($model) {
            if (strpos($this->request->searchTerm, 'استعلام') > -1) {
                $model->where('type', 'INQUIRY');
            } elseif (strpos($this->request->searchTerm, 'مناقصه') > -1) {
                $model->where('type', 'TENDER');
            } elseif (strpos($this->request->searchTerm, 'مزایده') > -1) {
                $model->where('type', 'AUCTION');
            }
        })
        ->orWhere('description', 'LIKE', '%' . $this->request->searchTerm . '%')
        ->orWhereHas('provinces', function ($query) {
            $query->where('provinces.name', 'LIKE', '%' . $this->request->searchTerm . '%');
        })
        ->orWhereHas('workGroups', function ($query) {
            $query->where('work_groups.title', 'LIKE', '%' . $this->request->searchTerm . '%');
        })
        ->orWhere(function ($model) {
            if (strpos($this->request->searchTerm, 'ستاد') > -1) {
                $model->where('is_nerve_center', 1);
            }
        });
    }
}
