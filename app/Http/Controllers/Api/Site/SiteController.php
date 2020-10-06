<?php

namespace App\Http\Controllers\Api\Site;

use App\Advertise;
use App\Filters\AdvertiseFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertiseIndexResource;
use App\Http\Resources\GetParentWorkGroupsResource;
use App\Http\Resources\ShowAdvertiseResourceInSite;
use App\Http\Resources\SubscriptionResource;
use App\Subscription;
use App\WorkGroup;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function getWorkGroupParents()
    {
        return GetParentWorkGroupsResource::collection(WorkGroup::where('parent_id', null)->where('status', 1)
        ->get()->sortBy('priorty'));
    }

    public function show(Advertise $advertise)
    {
        return new ShowAdvertiseResourceInSite($advertise);
    }

    public function filter(Request $request, AdvertiseFilter $filters)
    {
        $searchedData = Advertise::filter($filters)->where('status', 1)->paginate(10);

        return AdvertiseIndexResource::collection($searchedData);
    }

    public function getAdvertises(Request $request, AdvertiseFilter $filters)
    {
        if ($request->get_last_five == true) {
            return AdvertiseIndexResource::collection(Advertise::where('status', 1)->take(10)
            ->get()->sortByDesc('id'));
        }
        if ($request->searchTerm != null) {
            if ($request->searchType == null) {
                $searchedData = Advertise::filter($filters)->where('status', 1)->paginate(10);
            } else {
                if ($request->searchType == 0) {
                    $searchedData = Advertise::filter($filters)->where('type', 'AUCTION')->where('status', 1)->paginate(10);
                } else {
                    $searchedData = Advertise::filter($filters)->where(function ($model) {
                        $model->where('type', 'TENDER')->orWhere('type', 'INQUIRY');
                    })->where('status', 1)->paginate(10);
                }
            }

            return AdvertiseIndexResource::collection($searchedData);
        }
    }

    public function getWorkGroupChild($workGroupId)
    {
        $parent = WorkGroup::where('id', $workGroupId)->where('parent_id', null)->first();
        if ($parent == null || $parent == []) {
            abort(422);
        }

        return response()->json([
            'data' => $parent->children->where('status', 1)
        ]);
    }

    public function showSubscriptions(Request $request)
    {
        $subscriptions = '';
        if ($request->items_per_page != null || $request->items_per_page != '') {
            $subscriptions = Subscription::latest()->orderBy('priorty')->where('status', 1)->paginate(intval($request->items_per_page));
        } else {
            if (Subscription::all() != null || Subscription::all() != []) {
                $subscriptions = Subscription::latest()->orderBy('priorty')->where('status', 1)->paginate(0);
            }
        }

        return SubscriptionResource::collection($subscriptions);
    }

    public function relatedAdvertises(Advertise $advertise)
    {
        if (auth()->user()->detail->workGroups != null && $advertise->workGroups != null) {
            $relatedWorkGroups = $advertise->workGroups->pluck('id');

            $advertises = Advertise::whereHas('workGroups', function ($model) use ($relatedWorkGroups) {
                $model->where(function ($workGroup) use ($relatedWorkGroups) {
                    $workGroup->whereIn('work_groups.id', $relatedWorkGroups);
                });
            })->get()->take(10)->filter(function ($value) use ($advertise) {
                return $value->id != $advertise->id;
            });

            return AdvertiseIndexResource::collection($advertises);
        }

        return response()->json([
            'data' => []
        ]);
    }
}
