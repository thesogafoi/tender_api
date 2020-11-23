<?php

namespace App\Http\Controllers\Api\Site;

use App\Advertise;
use App\Banner;
use App\Filters\AdvertiseFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertiseIndexResource;
use App\Http\Resources\GetParentWorkGroupsResource;
use App\Http\Resources\ShowAdvertiseResourceInSite;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\UserRecourses;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Morilog\Jalali\Jalalian;

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
        $searchedData = Advertise::filter($filters)->where('status', 1)->orderBy('id', 'desc')->paginate(10);

        return AdvertiseIndexResource::collection($searchedData);
    }

    public function getAdvertises(Request $request, AdvertiseFilter $filters)
    {
        if ($request->get_last_five == true) {
            return AdvertiseIndexResource::collection(Advertise::where('status', 1)->orderBy('id', 'desc')->take(10)
            ->get());
        }
        if ($request->searchTerm != null) {
            request()->searchTerm = str_replace(' ', '%', request()->searchTerm);
            if ($request->searchType == null) {
                $searchedData = Advertise::filter($filters)->where('status', 1)->orderBy('id', 'desc')->paginate(10);
            } else {
                if ($request->searchType == 0) {
                    $searchedData = Advertise::filter($filters)->where('type', 'AUCTION')->where('status', 1)
                    ->orderBy('id', 'desc')->paginate(10);
                } else {
                    $searchedData = Advertise::filter($filters)->where(function ($model) {
                        $model->where('type', 'TENDER')->orWhere('type', 'INQUIRY');
                    })->where('status', 1)
                    ->orderBy('id', 'desc')->paginate(10);
                }
            }

            return AdvertiseIndexResource::collection($searchedData);
        }

        if ($request->searchTerm == null && $request->searchType != null) {
            if ($request->searchType == 0) {
                $searchedData = Advertise::where('type', 'AUCTION')->where('status', 1)
                ->orderBy('id', 'desc')->paginate(10);
            } else {
                $searchedData = Advertise::where(function ($model) {
                    $model->where('type', 'TENDER')->orWhere('type', 'INQUIRY');
                })->where('status', 1)
                ->orderBy('id', 'desc')->paginate(10);
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

    public function index_app()
    {
        $banners = Banner::query()
            ->whereDate('start_date', '<=', Carbon::now()->toDateString())
            ->whereDate('expire_date', '>', Carbon::now()->toDateString())->get();
        if (count($banners) != 0) {
            foreach ($banners as $banner) {
                $banner->expire_date = Jalalian::forge($banner->expire_date)->format('Y-m-d');
                $banner->start_date = Jalalian::forge($banner->start_date)->format('Y-m-d');
            }

            return new JsonResponse($banners);
        }

        return new JsonResponse(['message' => 'There is no banner']);
    }

    public function relatedAdvertises(Advertise $advertise)
    {
        if (Gate::allows('client-can-see-advertise', $advertise)) {
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
        }

        return response()->json([
            'data' => []
        ]);
    }

    public function workGroupAdvertises(Request $request, WorkGroup $workGroup)
    {
        if ($request->items_per_page != '-1') {
            $workGroupAdvertises = $workGroup->advertises()->latest()->paginate(intval(10));
        } elseif ($request->items_per_page == '-1') {
            $workGroupAdvertises = $workGroup->advertises()->latest()->paginate($workGroup->advertises()->latest()->first()->id);
        }

        return AdvertiseIndexResource::collection($workGroupAdvertises);
    }

    // Forget Password

    public function checkMobile(Request $request)
    {
        // check if mobile request exists
        $request->validate([
            'mobile' => 'required'
        ]);
        // check if user exists with this mobile
        $user = User::where('mobile', $request->mobile)->first();
        if ($user == null) {
            abort(401);
        }
        // check client detail exists
        if ($user->detail == null) {
            abort(401);
        }
        // if all validation done
        //   we should generate a code
        $randomNumber = rand(10000, 99999);
        Cache::put(
            'verify_code_' . $request->mobile, // key
            $randomNumber, //value
                    90//in second
        );

        // we should send a sms to this number
        // return 200
        $data = [
            'receptor' => $request->mobile,
            'message' => 'به آسان تندر خوش آمدید . رمز عبور شما ' . $randomNumber . ' میباشد ',
        ];
        $response = Http::post("https://api.kavenegar.com/v1/https:/api.kavenegar.com/v1/78466B41486D46737338324E53344B3334696974306B48794F7637747970323775325A45635561667A52453D/verify/lookup.json?receptor=$request->mobile&token=$randomNumber&template=asantender", $data);

        return response()->json([
            'data' => $response
        ]);
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'mobile' => 'required|exists:users|max:11',
            'password' => 'required',
        ]);

        if (!Cache::has('verify_code_' . $request->mobile) || ($request->password != Cache::get('verify_code_' . $request->mobile))) {
            abort(401, 'Not Founded');
        }
        $user = User::where('mobile', $request->mobile)->first();
        if ($user == null) {
            abort(401, 'incomed mobile not true');
        }
        if ($user->detail == null) {
            abort(401, 'Client Detail not founded');
        }
        $user->password = Hash::make($request->password);
        $user->save();
        $credential = $request->only(['mobile', 'password']);
        if ($token = Auth::attempt($credential)) {
            if (auth()->user()->detail == null) {
                abort(401);
            }

            return (new UserRecourses(auth()->user()))->additional([
                'token' => $token
            ]);
        } else {
            return abort(401);
        }
    }
}
