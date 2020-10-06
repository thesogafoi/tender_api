<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

abstract class Filter
{
    public $request;
    public $filters = [];
    public $builder;

    public function apply($query)
    {
        $this->request = request();
        $this->changeDatesToCarbon();
        $this->builder = $query;
        foreach ($this->filters as $key => $filter) {
            if ($this->hasFilter($filter)) {
                $this->builder = $this->$filter();
            }
        }

        return $this->builder;
    }

    public function hasFilter($filter)
    {
        return !!($this->request->has($filter) && method_exists($this, $filter));
    }

    public function searchInRelations($relationFunction, $databaseId, $id)
    {
        return  $this->builder->whereHas($relationFunction, function (Builder $builder) use ($databaseId , $id) {
            $builder->where($databaseId, $id);
        });
    }

    public function changeDatesToCarbon()
    {
//         range_invitation_date
        // range_submit_date
        // range_receipt_date
        // range_start_date
        // range_free_date
        // invitation_date
        // submit_date
        // receipt_date
        // start_date
        // free_date
        foreach ($this->request->all() as $key => $value) {
            if (str_contains($key, '_date') && !str_contains($key, 'range_')) {
                $this->request[$key] = Jalalian::fromFormat('Y-m-d', $value)->toCarbon();
            }

            if (str_contains($key, '_date') && str_contains($key, 'range_')) {
                if (isset($this->request[$key]['first']) && !isset($this->request[$key]['second'])) {
                    $this->request[$key] = ['first' => Jalalian::fromFormat('Y-m-d', $value['first'])->toCarbon()];
                } elseif (isset($this->request[$key]['second']) && !isset($this->request[$key]['first'])) {
                    $this->request[$key] = ['second' => Jalalian::fromFormat('Y-m-d', $value['second'])->toCarbon()->addDays(1)];
                } elseif (isset($this->request[$key]['second']) && isset($this->request[$key]['first'])) {
                    $this->request[$key] = ['second' => Jalalian::fromFormat('Y-m-d', $value['second'])->toCarbon()->addDays(1),
                        'first' => Jalalian::fromFormat('Y-m-d', $value['first'])->toCarbon()];
                }
            }

            if (str_contains($key, 'created_at') && !str_contains($key, 'range_')) {
                $this->request[$key] = Jalalian::fromFormat('Y-m-d', $value)->toCarbon();
            }
            if (str_contains($key, 'created_at') && str_contains($key, 'range_')) {
                if (isset($this->request[$key]['first']) && !isset($this->request[$key]['second'])) {
                    $this->request[$key] = ['first' => Jalalian::fromFormat('Y-m-d', $value['first'])->toCarbon()];
                } elseif (isset($this->request[$key]['second']) && !isset($this->request[$key]['first'])) {
                    $this->request[$key] = ['second' => Jalalian::fromFormat('Y-m-d', $value['second'])->toCarbon()->addDays(1)];
                } elseif (isset($this->request[$key]['second']) && isset($this->request[$key]['first'])) {
                    $this->request[$key] = ['second' => Jalalian::fromFormat('Y-m-d', $value['second'])->toCarbon()->addDays(1),
                        'first' => Jalalian::fromFormat('Y-m-d', $value['first'])->toCarbon()];
                }
            }
        }
    }
}
