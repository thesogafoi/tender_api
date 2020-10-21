<?php

namespace App\Http\Controllers\Api;

use App\Advertise;
use App\Filters\AdvertiseFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdvertisesRequest;
use App\Http\Resources\AdvertiseAdminResource;
use App\Http\Resources\ShowAdvertiseResource;
use App\Imports\AdvertiseImport;
use App\WorkGroup;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdvertiseController extends Controller
{
    public function __construct()
    {
        //  $this->middleware(['auth', 'superadmin']);
    }

    public function create(AdvertisesRequest $request)
    {
        return response()->json([
            $request->newPersist()
        ]);
    }

    public function saveImage(Request $request, Advertise $advertise)
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $request->validate([
                'image_file' => 'required|mimes:jpeg,jpg,png,gif'
            ]);
            $filename = $advertise->tender_code . '.' . $request->image_file->getClientOriginalExtension();

            $path = $file->storeAs('public/images', $filename);
            $advertise->image = url('storage/images/' . $filename);

            $advertise->save();

            return response()->json([
                $advertise->image
            ]);
        }
    }

    public function update(Advertise $advertise, AdvertisesRequest $request)
    {
        return response()->json([
            $request->updatePersist($advertise)
        ]);
    }

    public function createFromExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlm,xla,xlc,xlt,xlw,xlsx'
        ]);
        Excel::import(new AdvertiseImport, $request->file('excel_file'));
    }

    public function searchAdvertise(AdvertiseFilter $filters)
    {
        $results = Advertise::filter($filters);
        if ($results !== '') {
            return $results->get();
        }
    }

    public function filterAdvertise(AdvertiseFilter $filters)
    {
        $results = Advertise::filter($filters);
        if ($results !== '') {
            return $results->get();
        }

        return response()->json([
            'message' => 'پیدا نشد'
        ]);
    }

    public function unpublish(Advertise $advertise)
    {
        $advertise->deactive();
    }

    public function publish(Advertise $advertise)
    {
        $advertise->active();
    }

    public function types()
    {
        return Advertise::types();
    }

    public function valuetypes()
    {
        // return Advertise::getValuesType(Advertise::types());
    }

    public function advertisePageGetSearchableAdvertises(Request $request, AdvertiseFilter $filters)
    {
        if ($request->items_per_page != '-1') {
            $searchedData = Advertise::filter($filters)->latest()->paginate(intval($request->items_per_page));
        } elseif ($request->items_per_page == '-1') {
            $searchedData = Advertise::filter($filters)->latest()->paginate(Advertise::latest()->first()->id);
        }

        return AdvertiseAdminResource::collection($searchedData);
    }

    public function show(Advertise $advertise)
    {
        return new ShowAdvertiseResource($advertise);
    }

    public function delete(Advertise $advertise)
    {
        if ($advertise->image) {
            if (file_exists($advertise->image)) {
                unlink($advertise->image);
            }
        }
        if ($advertise->workGroups) {
            $advertise->workGroups()->detach();
        }
        $advertise->destroy($advertise->id);
    }

    public function advertisesAction(Request $request)
    {
        $request->validate([
            'action' => 'required',
            'advertises_action' => 'required',
        ]);

        $advertisesId = $request->advertises_action;

        switch ($request->action) {
                case 0:
                    foreach ($advertisesId as $advertiseId) {
                        $advertise = Advertise::where('id', $advertiseId)->first();
                        if ($request->work_groups_action == null) {
                            abort(422, 'لطفا دسته ی کاری مورد نظر را وارد کنید');
                        }
                        $workGroupsRequest = [];

                        $workGroupsRequest = $request->work_groups_action;

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
                    }
                break;
                case 1:
                    foreach ($advertisesId as $advertiseId) {
                        $advertise = Advertise::where('id', $advertiseId)->first();
                        $advertise->deactive();
                    }
                break;
                case 2:
                    foreach ($advertisesId as $advertiseId) {
                        $advertise = Advertise::where('id', $advertiseId)->first();
                        if (count($advertise->workGroups->where('parent_id', '!=', null)) == 0) {
                            abort(422, 'آکهی انتشار یافته نمیتواند فاقد دسته کاری باشد');
                        }
                        $advertise->active();
                    }
                break;
                case 3:
                    foreach ($advertisesId as $advertiseId) {
                        $advertise = Advertise::where('id', $advertiseId)->first();
                        if ($advertise->workGroups) {
                            $advertise->workGroups()->detach();
                        }
                        if ($advertise->image) {
                            if (file_exists($advertise->image)) {
                                unlink($advertise->image);
                            }
                        }
                        $advertise->destroy($advertise->id);
                    }
                break;
                default:
                    abort(500, 'لطفا دوباره تلاش کنید مشکلی برای سیستم بوجود آمده است');
                    break;
            }
    }
}
