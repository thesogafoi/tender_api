<?php

namespace App\Http\Controllers\Api\Site;

use App\Advertise;
use App\ClientDetail;
use App\Filters\AdvertiseFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertiseIndexResource;
use App\Http\Resources\GetParentWorkGroupsResource;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Morilog\Jalali\Jalalian;

class UserProfileController extends Controller
{
    public function __constructor()
    {
    }

    public function takePlane(Subscription $subscription)
    {
        $user = auth()->user();
        $clientDetail = $user->detail;
        if ($subscription->cost == 0) {
            $clientDetail->subscription_date = Carbon::parse($subscription->period);
            $clientDetail->subscription_title = $subscription->title;
            $clientDetail->subscription_count = $subscription->allowed_selection;
            $clientDetail->save();
        }
    }

    public function takeWorkGroups(Request $request)
    {
        $detail = ClientDetail::where('user_id', auth()->user()->id)->first();
        if (($detail->subscription_date == null || $detail->subscription_date == '')
        && ($detail->subscription_count == 0) &&
        ($detail->subscription_title == null || $detail->subscription_title == '')
        ) {
            abort(403, 'لطفا ابتدا طرح اشتراکی را خریداری کنید');
        }
        if (request()->work_groups != null) {
            if (count(request()->work_groups) > $detail->subscription_count) {
                abort(403, 'تعداد گروه های کاری انتخاب شده بیشتر از تعداد مجاز است');
                // } elseif (Jalalian::now()->format('Y-m-d') > $detail->subscription_date) {
            //     abort(403, 'مدت زمان طرح اشتراکی شما به پایان رسیده است');
            }
        }
        if ($detail->work_groups_changes >= 3) {
            abort(403, 'شما بیشتر از ۳ بار گروه کاری خود را تغییر داده اید برای تغییرات بیشتر با پشتیبانی تماس حاصل کنید');
        }
        $newData = [];
        foreach ($request['work_groups'] as $key => $value) {
            array_push($newData, $value);
        }
        foreach ($request['work_groups'] as $key => $workGroupId) {
            $workGroup = WorkGroup::where('id', $workGroupId)->first();
            if ($workGroup->parent_id != null) {
                array_push($newData, (int) $workGroup->parent_id);
            }
            if ($workGroup->parent_id == null && ($workGroup->children != null || $workGroup->children != [])) {
                $childrenId = $workGroup->children->pluck('id');
                if (!$this->isChildExistsInArray($childrenId, $request['work_groups']->toArray())) {
                    unset($newData[array_search($workGroupId, $request['work_groups']->toArray())]);
                }
            }
        }

        $newData = array_unique($newData);

        $detail->work_groups_changes++;
        $detail->save();
        $detail->workGroups()->sync($newData);
    }

    public function updateProfileInfo(Request $request)
    {
        $user = $request->user();

        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->save();

        $user->detail->phone = $request->phone;
        $user->detail->company_name = $request->company_name;
        $user->detail->type = $request->type;

        $user->detail->save();
    }

    public function userWorkGroups(Request $request)
    {
        $user = $request->user();
        $workGroups = $user->detail->workGroups()->where('parent_id', '!=', 'null')->where('status', '1')->with('parent')->get();

        return response()->json(
            $workGroups
        );
    }

    public function getAllWorkGroups(Request $request)
    {
        $workGroups = WorkGroup::where('parent_id', null)->where('status', 1)->orderBy('priorty', 'asc')
            ->with('children')->get()->sortBy('priorty');

        return response()->json($workGroups);
    }

    public function changePassword(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        $user->password = Hash::make($request->password);

        $user->save();
    }

    public function getWorkGroupChild($workGroupId)
    {
        $userDetail = User::where('id', auth()->id())->first()->detail;

        $parent = $userDetail->workGroups->where('id', $workGroupId)->first();

        if ($parent == null || $parent == []) {
            abort(422);
        }

        return response()->json([
            'data' => $userDetail->workgroups->where('parent_id', $workGroupId)->where('status', 1)
        ]);
    }

    public function getWorkGroupParents()
    {
        $userDetail = User::where('id', auth()->id())->first()->detail;

        return GetParentWorkGroupsResource::collection($userDetail->workGroups()->where('parent_id', null)->where('status', 1)
        ->get()->sortBy('priorty'));
    }

    public function filter(Request $request, AdvertiseFilter $filters)
    {
        $user = User::findOrFail(auth()->user()->id);
        $searchedData = Advertise::filter($filters)->whereHas('workGroups', function ($model) use ($user) {
            $model->where(function ($workGroup) use ($user) {
                $workGroup->whereIn('work_groups.id', $user->detail->workGroups->pluck('id'));
            });
        })
        ->where('status', 1)->paginate(10);

        return AdvertiseIndexResource::collection($searchedData);
    }

    public function getAdvertises(Request $request, AdvertiseFilter $filters)
    {
        if ($request->searchTerm != null) {
            $user = User::findOrFail(auth()->user()->id);

            $searchedData = Advertise::filter($filters)->whereHas('workGroups', function ($model) use ($user) {
                $model->where(function ($workGroup) use ($user) {
                    $workGroup->whereIn('work_groups.id', $user->detail->workGroups->pluck('id'));
                });
            })->where('status', 1)->paginate(10);

            return AdvertiseIndexResource::collection($searchedData);
        }
    }

    public function toggleFavorite(Advertise $advertise)
    {
        $detail = auth()->user()->detail;
        if (!$detail->favorites()->where('advertise_id', $advertise->id)->exists()) {
            $detail->favorites()->attach($advertise->id);
        } else {
            $detail->favorites()->detach($advertise->id);
        }
    }

    public function getFavoritesAdvertises()
    {
        $detail = User::find(auth()->user()->id)->detail;

        $searchedData = $detail->favorites()->where('status', 1)->paginate(10);

        return AdvertiseIndexResource::collection($searchedData);
    }

    protected function isChildExistsInArray($childrenId, $work_groups)
    {
        $isExists = false;
        foreach ($childrenId as $id) {
            if (in_array($id, $work_groups)) {
                $isExists = true;
            }
        }

        return $isExists;
    }
}
