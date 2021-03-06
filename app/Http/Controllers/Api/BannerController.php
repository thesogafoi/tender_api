<?php

namespace App\Http\Controllers\Api;

use App\Banner;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Morilog\Jalali\Jalalian;

class BannerController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth', 'superadmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index_app(Request $request)
    {
        $banners = Banner::query()
            ->whereDate('start_date', '<=', Carbon::now()->toDateString())
            ->whereDate('expire_date', '>', Carbon::now()->toDateString())->paginate(intval($request->items_per_page));
        if (count($banners) != 0) {
            foreach ($banners as $banner) {
                $banner->expire_date = Jalalian::fromCarbon(Carbon::parse($banner->expire_date))->format('Y-m-d');
                $banner->start_date = Jalalian::fromCarbon(Carbon::parse($banner->start_date))->format('Y-m-d');
            }

            return new JsonResponse($banners);
        }

        return new JsonResponse(['message' => Lang::get('messages.no_banner')]);
    }

    public function index_back_office(Request $request)
    {
        $banners = Banner::latest()->paginate(intval($request->items_per_page));
        if (count($banners) != 0) {
            foreach ($banners as $banner) {
                if (Carbon::now()->toDateString() > $banner->expire_date) {
                    $banner['isExpire'] = true;
                } else {
                    $banner['isExpire'] = false;
                }
                if (Carbon::now()->toDateString() > $banner->start_date) {
                    $banner['isStart'] = true;
                } else {
                    $banner['isStart'] = false;
                }
                if ($banner->hasButton) {
                    $banner['hasButton'] = 1;
                } else {
                    $banner['hasButton'] = 0;
                }

                $banner->expire_date = Jalalian::forge(Carbon::parse($banner->expire_date))->format('Y-m-d');
                $banner->start_date = Jalalian::forge(Carbon::parse($banner->start_date))->format('Y-m-d');
            }

            return new JsonResponse($banners);
        }

        return new JsonResponse(['message' => 'There is no banner']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'link' => 'required',
            'start_date' => 'required',
            'expire_date' => 'required',
        ]);
        $request['start_date'] = Jalalian::fromFormat('Y-m-d', $request['start_date'])->toCarbon();
        $request['expire_date'] = Jalalian::fromFormat('Y-m-d', $request['expire_date'])->toCarbon();

        $banner = Banner::query()->create([
            'title' => $request['title'],
            'description' => $request['description'],
            'link' => $request['link'],
            'start_date' => $request['start_date'],
            'expire_date' => $request['expire_date'],
            'hasButton' => $request['hasButton'] == null ? 0 : $request['hasButton']
        ]);

        return new JsonResponse(['banner_id' => $banner->id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function saveImage(Request $request, $id)
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $request->validate([
                'image_file' => 'required|mimes:jpeg,jpg,png,gif'
            ]);
            $filename = $id . '_banner_image.' . $request->image_file->getClientOriginalExtension();

            $path = $file->storeAs('public/banners', $filename);
            Banner::query()->where('id', '=', $id)->update([
                'image_file' => url('storage/banners/' . $filename)
            ]);

            return new JsonResponse(['image_file' => url('storage/banners/' . $filename)]);
        }

        return new JsonResponse(['message' => 'something error '], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Banner $banner
     * @return JsonResponse
     */
    public function click_banner(Request $request)
    {
        Banner::query()->where('id', '=', $request['id'])->increment('click_count');

        return new JsonResponse(['message' => 'plus count']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Banner $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Banner $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required',
            'expire_date' => 'required',
        ]);
        $banner->title = $request['title'];
        $banner->description = $request['description'];
        $banner->link = $request['link'];
        $banner->hasButton = $request['hasButton'];

        $banner->expire_date = Jalalian::fromFormat('Y-m-d', $request->expire_date)->toCarbon();
        $banner->start_date = Jalalian::fromFormat('Y-m-d', $request->start_date)->toCarbon();
        $banner->save();

        return response()->json([
            $banner
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Banner $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
        //
    }

    public function show(Banner $banner)
    {
        $banner->expire_date = Jalalian::forge(Carbon::parse($banner->expire_date))->format('Y-m-d');
        $banner->start_date = Jalalian::forge(Carbon::parse($banner->start_date))->format('Y-m-d');

        return response()->json([
            $banner
        ]);
    }
}
