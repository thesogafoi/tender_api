<?php

namespace App\Http\Controllers\Api;

use App\Exports\WorkGroupExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\WorkGroupImport;
use App\WorkGroup;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class WorkGroupController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'priorty' => 'required'
        ]);
        $workGroups = new WorkGroup();
        $workGroups->title = $request->title;
        if ($request->parent_id) {
            $workGroups->parent_id = $request->parent_id;
        }
        if ($request->status) {
            $workGroups->status = $request->status;
        } else {
            $workGroups->status = 0;
        }
        $workGroups->priorty = $request->priorty;
        $workGroups->type = $request->type;
        $workGroups->save();

        return [
            'message' => 'گروه کاری با موفقیت اضافه شد'
        ];
    }

    public function createFromExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlm,xla,x`xlc,xlt,xlw,xlsx'
        ]);
        Excel::import(new WorkGroupImport, $request->file('excel_file'));
    }

    public function getAsExcel()
    {
        Excel::store(new WorkGroupExport, 'public/excel/AsanTenderWorkGroups.xlsx', 'local');
        if (Storage::exists('public/excel/AsanTenderWorkGroups.xlsx')) {
            return response()->json([
                'url' => asset('storage/excel/AsanTenderWorkGroups.xlsx')
            ]);
        }
    }

    protected function extendValidation()
    {
        $workGroups = WorkGroup::where('title', request()->title)->
        where('type', request()->type)->first();
        if ($workGroups != null) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'آگهی تکراری است'
            ]);
            throw $error;
        }
    }

    public function index()
    {
        $workGroups = WorkGroup::where('parent_id', null)->orderBy('priorty', 'asc')
            ->with('children')->get()->sortBy('priorty');

        return response()->json(
            $workGroups
        );
    }

    public function update(WorkGroup $workGroup, Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'priorty' => 'required'
        ]);
        $workGroup->title = $request->title;
        // if child
        if ($request->parent_id) {
            $workGroup->parent_id = $request->parent_id;
        }
        // if parent
        if ($request->parent_id == null && $request->type != $workGroup->type && count($workGroup->children) != 0) {
            foreach ($workGroup->children as $child) {
                if ($child->type != $request->type) {
                    abort(422, 'دسته ی کاری شامل زیر گروه است نمیتوانید نوع آن را تغییر دهید ابتدا زیر نوع زیر گروه ها را تغییر دهید');
                }
            }
        }
        if ($request->status) {
            $workGroup->status = $request->status;
        } else {
            $workGroup->status = 0;
        }
        $workGroup->priorty = $request->priorty;
        $workGroup->type = $request->type;
        $workGroup->save();
    }

    public function delete(WorkGroup $workGroup)
    {
        if ($workGroup->parent_id == null && ($workGroup->children != null || $workGroup->children != [])) {
            abort(422, 'دسته ی کاری مورد نظر شامل زیر گروه است نمیتوانید آنرا حذف کنید');
        }
        if ($workGroup->parent_id != null && (count($workGroup->advertises) > 0)) {
            abort(422, 'دسته ی کاری مورد نظر به آگهی تخصیص داده شده است نمیتوانید آن را حذف کنید');
        }
        $workGroup->destroy($workGroup->id);
    }

    public function restore($workGroupId)
    {
        WorkGroup::withTrashed()
        ->where('id', $workGroupId)->restore();
    }

    public function forceDelete($workGroupId)
    {
        WorkGroup::withTrashed()
        ->where('id', $workGroupId)->forceDelete();
    }
}
