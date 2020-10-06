<?php

namespace Tests\Feature;

use App\Advertise;
use App\Province;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Morilog\Jalali\Jalalian;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_user_can_use_advance_search_by_work_group()
    {
        $workGroups = create(WorkGroup::class, 2);
        $workGroupsId = $workGroups->pluck('id');
        $data = [
            'title' => 'qwd',
            'type' => 'INQUIRY',
            'work_groups' => $workGroupsId,
            'receipt_date' => Jalalian::now()->addMonths(1)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->addDays(-1)->format('Y-m-d'),
            'invitation_code' => '55959899599',
            'image' => '',
            'link' => '',
            'start_date' => Jalalian::now()->addYears(1)->format('Y-m-d'),
            'free_date' => Jalalian::now()->addYears(1)->format('Y-m-d'),
            'status' => 1,
            'adinviter_title' => 'ad inviter title',
        ];
        $this->call('POST', 'api/advertise/create', $data)->assertStatus(200);
        $advertise = Advertise::where('id', 1)->first();
        create(Advertise::class, 20, ['status' => 1]);
        $searchTerm = $advertise->title;
        dd($results = $this->call('GET', 'api/site/advertise?searchTerm=' . $searchTerm . '&page=1&searchType=1')->json());
        $this->assertCount(1, $results);
    }

    /** @test */
    public function an_user_can_search_data_by_provinces()
    {
        // create prvinces
        // create advertise
        // assign provinces with advertise
        // test for provinces and how its handled
        $provinces = create(Province::class, 2);
        $provincesId = $provinces->pluck('id');
        $data = [
            'title' => 'تایتل شماره ۱',
            'type' => 'INQUIRY',
            'provinces' => $provincesId,
            'receipt_date' => Jalalian::now()->addMonths(1)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->addDays(-1)->format('Y-m-d'),
            'invitation_code' => '55959899599',
            'image' => '',
            'link' => '',
            'start_date' => Jalalian::now()->addYears(1)->format('Y-m-d'),
            'free_date' => Jalalian::now()->addYears(1)->format('Y-m-d'),
            'status' => 0,
            'adinviter_title' => 'ad inviter title',
        ];
        $this->call('POST', 'api/advertise/create', $data)->assertStatus(200);
        create(Advertise::class, 20);
        $searchTerm = [
            'provinces' => [1, 2]
        ];
        $results = $this->call('GET', 'api/advertise/filter', $searchTerm)->json();
        $this->assertCount(1, $results);
    }

    /** @test */
    public function an_user_can_filter_by_date()
    {
        $provinces = create(Province::class, 2);
        $provincesId = $provinces->pluck('id');
        $data = [
            'title' => 'تایتل شماره ۱',
            'type' => 'INQUIRY',
            'provinces' => $provincesId,
            'receipt_date' => '1399-06-04',
            'submit_date' => '1399-06-04',
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => '1399-06-04',
            'invitation_code' => '55959899599',
            'image' => '',
            'link' => '',
            'start_date' => '1399-06-04',
            'free_date' => '1399-06-04',
            'status' => 0,
            'resource' => 'something',
            'adinviter_title' => 'ad inviter title',
        ];
        $this->call('POST', 'api/advertise/create', $data)->assertStatus(200);
        $searchTerm = [
            'searchTerm' => Advertise::all()->first()->tender_code
        ];
        $results = $this->call('POST', 'api/advertise/page/get/searchable/advertises', $searchTerm)->json();
        $this->assertCount(1, $results['data']);
    }

    /** @test */
    public function a_user_can_search_in_whole_of_columns()
    {
        $provinces = create(Province::class, 2);
        $workGroups = create(WorkGroup::class, 2);
        create(Advertise::class, 5);
        $provincesId = $provinces->pluck('id');
        $workGroupsId = $workGroups->pluck('id');
        $firstProvinces = Province::find(1);
        $data = [
            'title' => 'تایتل شماره ۱',
            'type' => 'INQUIRY',
            'provinces' => $provincesId,
            'receipt_date' => '1399-06-04 ',
            'submit_date' => '1399-06-04 ',
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 1,
            'invitation_date' => '1399-06-04',
            'invitation_code' => '55959899599',
            'image' => '',
            'work_groups' => $workGroupsId,
            'link' => '',
            'start_date' => '1399-06-04 ',
            'free_date' => '1399-06-04',
            'status' => 0,
            'resource' => 'something',
            'adinviter_title' => 'ad inviter title',
        ];
        $this->call('POST', 'api/advertise/create', $data)->assertStatus(200);
        $searchTerm = [
            'searchTerm' => 'ستاد'
        ];
        $results = $this->call('POST', 'api/advertise/page/get/searchable/advertises', $searchTerm)->json();
        $this->assertEquals(1, count($results['data']));
    }

    /** @test */
    public function test_for_different_date()
    {
        $provinces = create(Province::class, 2);
        $workGroups = create(WorkGroup::class, 2);
        $provincesId = $provinces->pluck('id');
        $workGroupsId = $workGroups->pluck('id');
        $firstProvinces = Province::find(1);
        $data = [
            'title' => 'تایتل شماره ۱',
            'type' => 'INQUIRY',
            'provinces' => $provincesId,
            'receipt_date' => '1399-06-04 ',
            'submit_date' => '1399-06-04 ',
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 1,
            'invitation_date' => '1399-06-04',
            'invitation_code' => '55959899599',
            'image' => '',
            'work_groups' => [1, 2],
            'link' => '',
            'start_date' => '1399-06-04',
            'free_date' => '1399-06-04',
            'status' => 1,
            'resource' => 'something',
            'adinviter_title' => 'ad inviter title',
        ];
        $this->call('POST', 'api/advertise/create', $data);
        $data = [
            'work_groups' => [1, 3]
        ];
        $results = $this->call('POST', 'api/site/advertise/filter', $data)->json();
        $this->assertEquals(1, count($results['data']));
    }

    /** @test */
    public function doSomething()
    {
        $data = [
            'title' => 'تایتل شماره ۱',
            'type' => 'INQUIRY',
            'receipt_date' => '1399-06-04',
            'submit_date' => '1399-06-04',
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => '1399-06-04',
            'invitation_code' => '55959899599',
            'image' => '',
            'link' => '',
            'start_date' => '1399-06-04',
            'free_date' => '1399-06-23',
            'status' => 0,
            'resource' => 'something',
            'adinviter_title' => 'ad inviter title',
        ];
        $this->call('POST', 'api/advertise/create', $data);

        $data = [
            'range_created_at' => [
                'first' => '1399-06-02',
            ]
        ];
        $this->assertCount(1, $this->call('POST', '/api/advertise/page/get/searchable/advertises?page=1', $data)->json()['data']);
    }
}
