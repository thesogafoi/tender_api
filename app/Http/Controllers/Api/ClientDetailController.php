<?php

namespace App\Http\Controllers\Api;

use App\ClientDetail;
use App\Filters\ClientFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientDetailIndexAdminAreaResources;
use App\User;
use App\WorkGroup;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class ClientDetailController extends Controller
{
    public function __construct()
    {
//        $this->middleware(['auth']);
    }

    public function create(Request $request)
    {
        $this->authorize('create', ClientDetail::class);
        $request->validate([
            'phone' => 'required',
            'type' => 'required',
            'company_name' => 'required'
        ]);
        $clientDetail = new ClientDetail();
        $clientDetail->phone = $request->phone;
        $clientDetail->type = $request->type;
        $clientDetail->expired_detail = null;
        $clientDetail->allowed_selection = 0;
        $clientDetail->company_name = $request->company_name;
        $clientDetail->save();
    }

    public function index(Request $request, ClientFilter $filters)
    {
        $result = [];
        if ($request->searchTerm == null) {
            $user_details = User::whereNotNull('client_detail_id')->get();
        } else {
            $user_details = User::filter($filters)->whereNotNull('client_detail_id')->get();
        }

        return ClientDetailIndexAdminAreaResources::collection($user_details);
    }

    public function show(Request $request, ClientDetail $clientDetail)
    {
        $result['client_code'] = $clientDetail->id;
        $result['client_name'] = $clientDetail->user->name;
        $result['status'] = (string) $clientDetail->user->status;
        $result['company_name'] = $clientDetail->company_name;
        $result['mobile'] = $clientDetail->user->mobile;
        $result['register_date'] = Jalalian::fromCarbon($clientDetail->user->created_at)->format('Y-m-d');
        $result['user_type'] = $clientDetail->type;
        $result['tel'] = $clientDetail->phone;
        $result['work_groups'] = $clientDetail->workGroups->where('parent_id', '!=', null)->pluck('id');

        return $result;
    }

    public function update(Request $request, ClientDetail $clientDetail)
    {
        return response()->json([
            $request->all()
        ]);
        $detail = $clientDetail;
        // update data here
        $detail->status = $request->status;

        // update work Groups here

        if (($detail->subscription_date == null || $detail->subscription_date == '')
        && ($detail->subscription_count == 0) &&
        ($detail->subscription_title == null || $detail->subscription_title == '')
        ) {
            abort(403, 'کاربر طرح اشتراکی ندارد');
        }
        if (request()->work_groups != null) {
            if (count(request()->work_groups) > $detail->subscription_count) {
                abort(403, 'تعداد گروه های کاری انتخاب شده بیشتر از تعداد مجاز است');
                // } elseif (Jalalian::now()->format('Y-m-d') > $detail->subscription_date) {
            //     abort(403, 'مدت زمان طرح اشتراکی شما به پایان رسیده است');
            }
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
}
