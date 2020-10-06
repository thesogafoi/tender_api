<?php

namespace Tests\Unit;

use App\AdInviter;
use App\Advertise;
use App\Province;
use App\User;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Morilog\Jalali\Jalalian;
use Tests\TestCase;

class AdvertiseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_admin_or_super_admin_can_create_advertises_but_not_published_and_must_assigned_to_work_group()
    {
        $adInviter = create(AdInviter::class)->first();
        create(WorkGroup::class);
        $workGroups = create(WorkGroup::class, 10, ['parent_id' => 1]);
        $workGroupsId = $workGroups->pluck('id');
        $provinces = create(Province::class, 10);
        $provincesId = $provinces->pluck('id');

        $user = create(User::class, 1, [], 'superadmin')->first();
        $this->actingAs($user);
        $data = [
            'title' => 'یک تایتل',
            'invitation_code' => '55959899599',
            'work_groups' => $workGroupsId,
            'receipt_date' => Jalalian::now()->addDays(7)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->subDays(1)->format('Y-m-d'),
            'image' => '',
            'link' => '',
            'status' => 0,
            'start_date' => Jalalian::now()->addMonths(4)->format('Y-m-d'),
            'provinces' => $provincesId,
            'free_date' => Jalalian::now()->addYears(2)->format('Y-m-d'),
            'type' => 'INQUIRY',
            'adinviter_title' => 'یک تست برا آگهی',
        ];
        $this->json('POST', '/api/advertise/create', $data)->assertStatus(200);
        $this->assertEquals(10, Advertise::all()->first()->workGroups->fresh()->count());
        $this->assertDatabaseHas('advertises', [
            'title' => $data['title'],
        ]);
        $this->assertFalse(Advertise::latest()->first()->isPublished());
        $this->assertEquals(10, Advertise::first()->provinces->count());
    }

    /** @test */
    public function an_admin_can_update_advertise()
    {
        $adInviter = create(AdInviter::class)->first();
        $workGroups = create(WorkGroup::class, 10);
        $workGroupsId = $workGroups->pluck('id');
        $provinces = create(Province::class, 10);
        $provincesId = $provinces->pluck('id');
        $user = create(User::class, 1, [], 'superadmin')->first();
        $this->actingAs($user);
        $data = [
            'title' => 'یعپپغ فثطف هس اثقث شدی صث زشد صقهفث ش یعپپغ فثطف اثقث بخق سخپث فاهدل',
            'invitation_code' => '55959899599',
            'type' => 'INQUIRY',
            'provinces' => $provincesId,
            'work_groups' => $workGroupsId,
            'adinviter_id' => $adInviter->id,
            'receipt_date' => Jalalian::now()->addDays(7)->format('Y-m-d'),
            'status' => 0,
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->subDays(1)->format('Y-m-d'),
            'image' => '',
            'link' => '',
            'start_date' => Jalalian::now()->format('Y-m-d'),
            'free_date' => Jalalian::now()->format('Y-m-d'),
            'adinviter_title' => 'ad inviter title',
            'status' => 0
        ];
        $workGroups = create(WorkGroup::class, 10);
        $workGroupsId = $workGroups->pluck('id');
        $this->json('POST', 'api/advertise/create', $data)->assertStatus(200);
        $advertise = Advertise::all()->first();
        $data = [
            'tender_code' => '0016927702',
            'title' => 'new Title',
            'status' => 0,
            'adinviter_title' => 'ad inviter title',
            'invitation_code' => '55959899599',
            'adinviter_id' => $adInviter->id,
            'receipt_date' => Jalalian::now()->addDays(7)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->subDays(1)->format('Y-m-d'),
            'image' => '',
            'link' => '',
            'work_groups' => '',
            'provinces' => '',
            'start_date' => '1396-02-02',
            'free_date' => '1396-02-02', 'type' => 'INQUIRY',
        ];
        $this->json('PUT', 'api/advertise/update/' . $advertise->id, $data)
        ->assertStatus(200);
        $this->assertEquals(0, count(Advertise::all()->first()->workGroups));
        $this->assertEquals(0, count(Advertise::all()->first()->provinces));

        $this->assertDatabaseHas('advertises', [
            'title' => $data['title'],
        ]);
        $this->assertFalse(Advertise::latest()->first()->isPublished());
    }

    /** @test */
    public function make_advertise_publish_and_unpublish()
    {
        $advertise = create(Advertise::class)->first();
        $this->call('POST', 'api/advertise/publish/' . $advertise->id)->assertStatus(200);
        $this->assertEquals(1, $advertise->fresh()->status);
        $this->call('POST', 'api/advertise/unpublish/' . $advertise->id)->assertStatus(200);
        $this->assertEquals(0, $advertise->fresh()->status);
    }

    /** @test */
    public function an_advertise_can_has_different_types()
    {
        $this->assertEquals(WorkGroup::getValuesType('AUCTION'), 'مزایده');
        $this->assertEquals(WorkGroup::getValuesType('TENDER'), 'مناقصه');
        $this->assertEquals(WorkGroup::getValuesType('INQUIRY'), 'استعلام');
    }

    /** @test */
    public function if_advertise_was_same_in_one_type_make_sure_abort()
    {
        // Create an Auction type advertise with same tender_code
        $AUCTIONAdvertise = create(Advertise::class, ['type' => 'AUCTION', 'invitation_code' => 1235555]);
        // Create an INQUIRY type advertise with same tender_code
        $data = [
            'tender_code' => '0016927702',
            'title' => 'new Title',
            'adinviter_title' => 'ad inviter title',
            'invitation_code' => '1235555',
            'receipt_date' => Jalalian::now()->addDays(7)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->subDays(1)->format('Y-m-d'),
            'image' => '',
            'link' => '',
            'work_groups' => '',
            'provinces' => '',
            'start_date' => '1396-02-02',
            'free_date' => '1396-02-02', 'type' => 'INQUIRY',
            'status' => 0
        ];
        $this->json('POST', 'api/advertise/create', $data);
        // assert not get error
        // create an advertise with Auction type and same tender_code
        // assert get error 422 and error message
        $this->json('POST', 'api/advertise/create', $data)
        ->assertStatus(422);
    }

    /** @test */
    public function show_me_data_with_advertise_id()
    {
        $data = [
            'tender_code' => '0016927702',
            'title' => 'new Title',
            'adinviter_title' => 'ad inviter title',
            'invitation_code' => '1235555',
            'receipt_date' => Jalalian::now()->addDays(7)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => Jalalian::now()->subDays(1)->format('Y-m-d'),
            'image' => '',
            'link' => '',
            'work_groups' => '',
            'provinces' => '',
            'start_date' => '1396-02-02',
            'free_date' => '1396-02-02', 'type' => 'INQUIRY',
            'status' => 0,
            'work_groups' => [1, 2, 3, 4]
        ];
        create(WorkGroup::class, 3, ['priorty' => '1']);
        create(WorkGroup::class, 1, ['priorty' => '0']);
        for ($i = 1;$i < 5; $i++) {
            $newWorkGroup = WorkGroup::where('id', $i)->first();
            $newWorkGroup->parent_id = 5;
            $newWorkGroup->save();
        }
        create(WorkGroup::class, 1);
        dd($this->json('POST', 'api/advertise/create', $data));
        $advertise = $this->json('GET', 'api/advertise/show/1')->json()['data'];
        $this->assertEquals($advertise['title'], 'new Title');
    }

    /** @test */
    public function an_work_groups_can_be_soft_delete()
    {
        // create a workgroup
        $newWorkGrouop = create(WorkGroup::class)->first();

        // use api for delete this
        $this->call('DELETE', '/api/workgroup/delete/' . $newWorkGrouop->id)
        ->assertStatus(200);
        // check its not with work groups
        $this->assertCount(0, WorkGroup::all());
        // check its exists in soft delete area
        $this->assertCount(1, WorkGroup::onlyTrashed()->get());
        // use api for restore it
        $this->call('PUT', '/api/workgroup/restore/' . $newWorkGrouop->id)
        ->assertStatus(200);
        // check its with work groups
        $this->assertCount(1, WorkGroup::all());
        // use api for soft delete
        $this->call('DELETE', '/api/workgroup/delete/' . $newWorkGrouop->id)
        ->assertStatus(200);
        $this->assertCount(0, WorkGroup::all());
        // use api for force delete
        $this->call('DELETE', '/api/workgroup/force/delete/' . $newWorkGrouop->id)
        ->assertStatus(200);

        // check its not exists in trashed area and workgroups area
        $this->assertCount(0, WorkGroup::all());
        $this->assertCount(0, WorkGroup::onlyTrashed()->get());
    }

    /** @test */
    public function if_advertise_has_one_status_and_work_groups_was_empty_return_422()
    {
        $data = [
            'title' => 'یک تایتل',
            'invitation_code' => '55959899599',
            'work_groups' => [],
            'adinviter_id' => 'dummy content',
            'receipt_date' => Jalalian::now()->addDays(7)->format('Y-m-d'),
            'submit_date' => Jalalian::now()->addMonths(2)->format('Y-m-d'),
            'description' => 'ضصمینضصخینصضخ ینصضحخ ینصضحخنی حخضصن یخحصضنیخحصضنی خحصضنیخح نصضحخین ضصحخنیصضنیصضخحینصضخحینخحصضحی',
            'is_nerve_center' => 0,
            'invitation_date' => '',
            'image' => '',
            'link' => '',
            'status' => 1,
            'start_date' => Jalalian::now()->addYears(1)->format('Y-m-d'),
            'provinces' => '',
            'free_date' => Jalalian::now()->addYears(2)->format('Y-m-d'),
            'type' => 'INQUIRY',
            'adinviter_title' => 'یک تست برا آگهی',
            'work_groups' => [1]
        ];
        dd($this->json('POST', '/api/advertise/create', $data));
    }

    /** @test */
    public function an_advertise_can_be_deleted()
    {
        $advertise = create(Advertise::class)->first();
        $this->call('DELETE', 'api/advertise/' . $advertise->id)
        ->assertStatus(200);

        $this->assertDatabaseMissing('advertises', [
            'title' => $advertise
        ]);
    }
}
