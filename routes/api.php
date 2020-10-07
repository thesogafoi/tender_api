<?php

use Illuminate\Support\Facades\Route;

// Site Auth Controllr

Route::get('site/user', 'Api\Site\SiteAuthController@user');

Route::post('site/initial/register', 'Api\Site\SiteAuthController@initialRegister');
Route::post('site/register', 'Api\Site\SiteAuthController@register');
Route::post('site/forget/password', 'Api\Site\SiteAuthController@forgetPassword');
Route::post('site/login', 'Api\Site\SiteAuthController@login');
Route::post('site/logout', 'Api\Site\SiteAuthController@logout');
/*************************************************************************************************** */

// Auth Controller
Route::get('user', 'Api\AuthController@user');
Route::post('login', 'Api\AuthController@login');
Route::post('logout', 'Api\AuthController@logout');

/*************************************************************************************************** */

// AdInviter Controller
Route::get('adinviter', 'Api\AdInviterController@index');

Route::post('adinviter/create', 'Api\AdInviterController@create');
Route::post('adinviter/excel/create', 'Api\AdInviterController@createFromExcel');

/*************************************************************************************************** */

// WorkGroup Controller
Route::get('workgroup/component/index', 'Api\WorkGroupController@index');
Route::get('workgroups/as/excel', 'Api\WorkGroupController@getAsExcel');

Route::post('workgroup/excel/create', 'Api\WorkGroupController@createFromExcel');
Route::post('workgroup/create', 'Api\WorkGroupController@create');

Route::put('workgroup/restore/{workGroupId}', 'Api\WorkGroupController@restore');
Route::put('workgroup/{workGroup}', 'Api\WorkGroupController@update');

Route::delete('workgroup/force/delete/{workGroupId}', 'Api\WorkGroupController@forceDelete');
Route::delete('workgroup/delete/{workGroup}', 'Api\WorkGroupController@delete');

/*************************************************************************************************** */

// Privince Controller
Route::post('province/create', 'Api\ProvinceController@create');
Route::post('province/update/{province}', 'Api\ProvinceController@update');

/*************************************************************************************************** */

// Advertise Controller
Route::get('advertise/show/{advertise}', 'Api\AdvertiseController@show');
Route::get('advertise/search', 'Api\AdvertiseController@searchAdvertise');
Route::get('advertise/filter', 'Api\AdvertiseController@filterAdvertise');
Route::get('get/advertise/types', 'Api\AdvertiseController@types');
Route::get('get/advertise/valuetypes', 'Api\AdvertiseController@valuetypes');

Route::post('advertises/action', 'Api\AdvertiseController@advertisesAction');
Route::post('advertise/create', 'Api\AdvertiseController@create');
Route::post('advertise/save/image/{advertise}', 'Api\AdvertiseController@saveImage');
Route::post('advertise/page/get/searchable/advertises', 'Api\AdvertiseController@advertisePageGetSearchableAdvertises');
Route::post('advertise/excel/create', 'Api\AdvertiseController@createFromExcel');
Route::post('advertise/publish/{advertise}', 'Api\AdvertiseController@publish');
Route::post('advertise/unpublish/{advertise}', 'Api\AdvertiseController@unpublish');

Route::put('advertise/update/{advertise}', 'Api\AdvertiseController@update');

Route::delete('advertise/{advertise}', 'Api\AdvertiseController@delete');

/*************************************************************************************************** */

// Subscription Controller

Route::get('subscription', 'Api\SubscriptionController@index');

Route::post('subscription', 'Api\SubscriptionController@create');
Route::post('subscription/unpublish/{subscription}', 'Api\SubscriptionController@unpublish');
Route::post('subscription/publish/{subscription}', 'Api\SubscriptionController@publish');

Route::put('subscription/{subscription}', 'Api\SubscriptionController@update');

Route::delete('subscription/{subscription}', 'Api\SubscriptionController@delete');

/*************************************************************************************************** */

// Client Dashboard Controller

Route::post('client-detail/create', 'Api\ClientDetailController@create');
Route::get('client-detail/index', 'Api\ClientDetailController@index');
Route::get('client-detail/show', 'Api\ClientDetailController@show');

/*************************************************************************************************** */

// Banner Controller
Route::post('banner/create', 'Api\BannerController@create');
Route::get('banner/index-app', 'Api\BannerController@index_app');
Route::get('banner/index-back-office', 'Api\BannerController@index_back_office');
Route::post('banner/click', 'Api\BannerController@click_banner');
Route::post('banner/save/image/{id}', 'Api\BannerController@saveImage');


/*************************************************************************************************** */

// Site Controller
Route::get('site/advertise', 'Api\Site\SiteController@getAdvertises');
Route::get('site/parent/workgroups', 'Api\Site\SiteController@getWorkGroupParents');
Route::get('site/child/workgroups/{workGroupId}', 'Api\Site\SiteController@getWorkGroupChild');
Route::get('site/advertise/show/{advertise}', 'Api\Site\SiteController@show');
Route::get('site/subscriptions', 'Api\Site\SiteController@showSubscriptions');
Route::get('site/user/related/advertises/{advertise}', 'Api\Site\SiteController@relatedAdvertises');

Route::post('site/advertise/filter', 'Api\Site\SiteController@filter');

/*************************************************************************************************** */

// User Profile Controller
Route::get('site/user/workGroups', 'Api\Site\UserProfileController@userWorkGroups');
Route::get('site/all/workGroups', 'Api\Site\UserProfileController@getAllWorkGroups');
Route::get('site/user/parent/workgroups', 'Api\Site\UserProfileController@getWorkGroupParents');
Route::get('site/user/child/workgroups/{workGroupId}', 'Api\Site\UserProfileController@getWorkGroupChild');
Route::get('site/user/advertise', 'Api\Site\UserProfileController@getAdvertises');
Route::get('site/user/favorite/advertises', 'Api\Site\UserProfileController@getFavoritesAdvertises');

Route::post('site/user/favorite/{advertise}', 'Api\Site\UserProfileController@toggleFavorite');
Route::post('site/user/advertise/filter', 'Api\Site\UserProfileController@filter');
Route::post('site/change/password', 'Api\Site\UserProfileController@changePassword');
Route::post('site/user/take/subscription/{subscription}', 'Api\Site\UserProfileController@takePlane');
Route::post('site/user/take/workgroups', 'Api\Site\UserProfileController@takeWorkGroups');
Route::post('site/profile', 'Api\Site\UserProfileController@updateProfileInfo');
