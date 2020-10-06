<?php

use App\Advertise;
use App\Province;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create multiple type of work_groups
        // assign first half advertises to first half work_groups
        // assign second half advertises to second half work_groups

        // factory(Advertise::class, 10)->create();
        // factory(WorkGroup::class, 10)->create();
        // factory(Subscription::class, 5)->create();

        // foreach (WorkGroup::all()->slice(1, 50) as $workgroup) {
        //     $workgroup->parent_id = 1;
        //     $workgroup->save();
        // }
        // $firstWorkGroups = WorkGroup::all()->slice(1, 5);
        // $secondWorkGroups = WorkGroup::all()->slice(5, 10);
        // $firstWorkGroupsId = $firstWorkGroups->pluck('id');
        // $secondWorkGroupsId = $secondWorkGroups->pluck('id');
        // foreach (Advertise::all()->slice(1, 5) as $advertise) {
        //     $advertise->workGroups()->sync($firstWorkGroupsId);
        // }
        // foreach (Advertise::all()->slice(5, 10) as $advertise) {
        //     $advertise->workGroups()->sync($secondWorkGroupsId);
        // }

        $provinces = [
            'آذربایجان شرقی',

            'آذربایجان غربی',

            'اردبیل',

            'اصفهان',

            'بوشهر',

            'تهران',

            'چهارمحال و بختیاری',

            'خراسان جنوبی',

            'خراسان رضوی',

            'خراسان شمالی',

            'خوزستان',

            'زنجان',

            'سمنان',

            'سیستان و بلوچستان',

            'فارس',

            'قم',

            'کرمانشاه',

            'کرمان',

            'کهکیلویه و بویراحمد',

            'گلستان',

            'گیلان',

            'کرمان',

            'لرستان',

            'مازندران',

            'هرمزگان',

            'مرکزی',

            'همدان',

            'یزد',

            'البرز',

            'ایلام',

            'قزوین',
        ];
        foreach ($provinces as $province) {
            $newProvince = new Province();
            $newProvince->name = $province;
            $newProvince->save();
        }

        factory(User::class)->create([
            'name' => 'کهشیدی',
            'mobile' => '09120751179',
            'type' => 'SUPERADMIN',
            'mobile_verified_at' => Carbon::now(),
            'password' => Hash::make('021051'),
            'status' => 1,
        ]);
    }
}
