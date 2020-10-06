<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AdInviter;
use App\Advertise;
use App\ClientDetail;
use App\Province;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'mobile' => function () {
            return '0912' . rand(1000000, 9999999);
        },
        'status' => 1,
        'password' => Hash::make('021051'),
        'remember_token' => Str::random(10),
    ];
});
$factory->state(User::class, 'verified_mobile', function (Faker $faker) {
    return [
        'name' => $faker->name,
        'mobile' => function () {
            return '0912' . rand(1000000, 9999999);
        },
        'mobile_verified_at' => Carbon::now()->toDateTimeString(),
        'password' => Hash::make('021051'),
        'remember_token' => Str::random(10),
        'type' => 'CLIENT'
    ];
});

$factory->state(User::class, 'client', function (Faker $faker) {
    return [
        'name' => $faker->name,
        'mobile' => function () {
            return '0912' . rand(1000000, 9999999);
        },
        'password' => Hash::make('021051'),
        'remember_token' => Str::random(10),
        'type' => 'CLIENT'
    ];
});

$factory->state(User::class, 'superadmin', function (Faker $faker) {
    return [
        'name' => $faker->name,
        'mobile' => function () {
            return '0912' . rand(1000000, 9999999);
        },
        'type' => 'SUPERADMIN',
        'password' => Hash::make('021051'),
        'remember_token' => Str::random(10),
    ];
});
$factory->state(User::class, 'admin', function (Faker $faker) {
    return [
        'name' => $faker->name,
        'mobile' => function () {
            return '0912' . rand(1000000, 9999999);
        },
        'type' => 'ADMIN',
        'password' => Hash::make('021051'),
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'staff', function (Faker $faker) {
    return [
        'name' => $faker->name,
        'mobile' => function () {
            return '0912' . rand(1000000, 9999999);
        },
        'type' => 'STAFF',
        'password' => Hash::make('021051'),
        'remember_token' => Str::random(10),
    ];
});

$factory->define(ClientDetail::class, function (Faker $faker) {
    return [
        'phone' => '02166552233',
        'type' => 'LEGAL',
        'company_name' => 'some where',
        'user_id' => function () {
            return factory(User::class)->state('client')->create()->first()->id;
        }
    ];
});

$factory->state(ClientDetail::class, 'verified_mobile', function (Faker $faker) {
    return [
        'phone' => '02166552233',
        'type' => 'LEGAL',
        'company_name' => 'some where',
        'user_id' => function () {
            return factory(User::class)->state('verified_mobile')->create()->first()->id;
        }
    ];
});

$factory->define(Advertise::class, function (Faker $faker) {
    return [
        'type' => Advertise::types()[0],
        'tender_code' => rand(1000, 20000),
        'title' => $faker->text,
        'invitation_date' => $faker->date,
        'adinviter_title' => $faker->name,
        'invitation_code' => rand(1000000, 9000000),
        'receipt_date' => $faker->date,
        'status' => 0,
        'submit_date' => $faker->date,
        'start_date' => $faker->date,
        'description' => $faker->text(),
        'resource' => $faker->text,
        'is_nerve_center' => false,
        'image' => $faker->text,
        'link' => $faker->text,
        'free_date' => $faker->date,
    ];
});
$factory->define(AdInviter::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->define(WorkGroup::class, function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'status' => 1,
        'type' => WorkGroup::types()[0]
    ];
});

$factory->state(WorkGroup::class, 'auction', function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'type' => 'AUCTION'
    ];
});
$factory->state(WorkGroup::class, 'tender', function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'type' => 'TENDER'
    ];
});
$factory->state(WorkGroup::class, 'hasParent', function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'parent_id' => function () {
            return factory(WorkGroup::class)->create()->first()->id;
        },
    ];
});

$factory->state(WorkGroup::class, 'onePriorty', function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'priorty' => 1
    ];
});
$factory->state(WorkGroup::class, 'twoPriorty', function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'priorty' => 2
    ];
});

$factory->define(Province::class, function (Faker $faker) {
    return [
        'name' => $faker->city,
    ];
});

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'allowed_selection' => 20,
        'cost' => 20000,
        'period' => Carbon::now()->addMonths(1),
        'status' => 0,
        'priorty' => 1,
        'title' => $faker->name,
    ];
});

$factory->state(Subscription::class, 'free', function (Faker $faker) {
    return [
        'allowed_selection' => 200,
        'cost' => 0,
        'period' => Carbon::now()->addMonths(1),
        'status' => 0,
        'priorty' => 1,
        'title' => $faker->name,
    ];
});
