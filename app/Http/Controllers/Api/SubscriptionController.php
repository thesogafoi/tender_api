<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:subscriptions',
            'cost' => 'required',
            'period' => 'required',
            'priorty' => 'required',
            'status' => 'required',
        ]);
        $subscription = new Subscription();
        $subscription->allowed_selection = $request->allowed_selection;
        $subscription->cost = $request->cost;
        $subscription->period = $request->period;
        $subscription->priorty = $request->priorty;
        if ($request->status) {
            $subscription->status = $request->status;
        } else {
            $subscription->status = 0;
        }
        $subscription->title = $request->title;
        $subscription->save();
    }

    public function unpublish(Subscription $subscription)
    {
        $subscription->deactive();
    }

    public function publish(Subscription $subscription)
    {
        $subscription->active();
    }

    public function index(Request $request)
    {
        $subscriptions = Subscription::latest()->orderBy('priorty')->paginate(intval($request->items_per_page));

        return SubscriptionResource::collection($subscriptions);
    }

    public function update(Subscription $subscription, Request $request)
    {
        $request->validate([
            'title' => 'required',
            'cost' => 'required',
            'period' => 'required',
            'priorty' => 'required',
            'status' => 'required',
        ]);
        $subscription->allowed_selection = $request->allowed_selection;
        $subscription->cost = $request->cost;
        $subscription->period = $request->period;
        $subscription->priorty = $request->priorty;
        if ($request->status) {
            $subscription->status = $request->status;
        } else {
            $subscription->status = 0;
        }
        $subscription->title = $request->title;
        $subscription->save();
    }

    public function delete(Subscription $subscription)
    {
        $subscription->destroy($subscription->id);
    }
}
