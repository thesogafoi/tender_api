<?php

namespace Tests\Unit;

use App\Advertise;
use App\User;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_value_types_must_return_true_value()
    {
        $this->assertEquals(WorkGroup::getValuesType('AUCTION'), 'مزایده');
        $this->assertEquals(WorkGroup::getValuesType('TENDER'), 'مناقصه');
        $this->assertEquals(WorkGroup::getValuesType('INQUIRY'), 'استعلام');
    }

    /** @test */
    // public function just_super_admin_can_add_adinviter()
    // {
    //     $user = create(User::class, 1, [], 'admin');
    //     $this->signIn($user);
    //     $data = [
    //         'title' => 'برق',
    //         'type' => 'TENDER',
    //         'priorty' => 0
    //     ];
    //     $this->json('POST', 'api/workgroup/create', $data)->assertStatus(403);
    //     $this->json('POST', 'api/logout');
    //     $this->json('POST', 'api/workgroup/create', $data)->assertStatus(403);
    //     $user = create(User::class, 1, [], 'superadmin');
    //     $this->signIn($user);
    //     $this->json('POST', 'api/workgroup/create', $data)->assertStatus(200);
    //     $this->assertDatabaseHas('work_groups', [
    //         'title' => $data['title']
    //     ]);
    // }

    /** @test */
    public function work_group_can_created()
    {
        $user = create(User::class, 1, [], 'superadmin');
        $this->signIn($user);
        $data = [
            'title' => 'برق',
            'type' => 'TENDER',
            'priorty' => 0
        ];
        $this->json('POST', 'api/workgroup/create', $data)->assertStatus(200);
        $this->assertDatabaseHas('work_groups', [
            'title' => $data['title']
        ]);
        $this->assertNull(WorkGroup::latest()->first()->parent_id);
        $data = [
            'title' => 'برق',
            'type' => 'TENDER',
            'priorty' => 0,
            'parent_id' => WorkGroup::all()->first()
        ];
        $this->json('POST', 'api/workgroup/create', $data)->assertStatus(200);
        $this->assertEquals(1, WorkGroup::all()->sortByDesc('id')->first()->parent_id);
    }

    /** @test */
    public function a_workgroups_can_active_and_deactive()
    {
        $workgroup = create(WorkGroup::class)->first();
        $this->assertEquals(1, $workgroup->status);
        $workgroup->deactive();
        $this->assertEquals(0, $workgroup->status);
        $workgroup->active();
        $this->assertEquals(1, $workgroup->status);
    }

    /** @test */
    public function give_all_work_groups_with_parent_and_child_array()
    {
        factory(Advertise::class, 10)->create();
        factory(WorkGroup::class, 10)->create();

        foreach (WorkGroup::all()->slice(1, 10) as $workgroup) {
            $workgroup->parent_id = 1;
            $workgroup->save();
        }
        $firstWorkGroups = WorkGroup::all()->slice(1, 9);
        $secondWorkGroups = WorkGroup::all()->slice(5, 9);
        $firstWorkGroupsId = $firstWorkGroups->pluck('id');
        $secondWorkGroupsId = $secondWorkGroups->pluck('id');
        foreach (Advertise::all()->slice(1, 5) as $advertise) {
            $advertise->workGroups()->sync($firstWorkGroupsId);
        }
        foreach (Advertise::all()->slice(5, 9) as $advertise) {
            $advertise->workGroups()->sync($secondWorkGroupsId);
        }
        $jsonWorkGroups = $this->call('GET', '/api/workgroup/component/index')->json();
        $this->assertEquals(11, count($jsonWorkGroups[0]));
    }

    /** @test */
    public function if_request_has_children_add_children_for_parent_work_group()
    {
        create(WorkGroup::class, 5);
        $data = [
            'title' => 'برق',
            'type' => 'TENDER',
            'priorty' => 0
        ];

        $this->json('POST', 'api/workgroup/create', $data)->assertStatus(200);
        for ($i = 1; $i <= 5; $i++) {
            $workgroup = WorkGroup::whereId($i)->first();
            $workgroup->parent_id = 6;
            $workgroup->save();
        }
        $this->assertEquals(5, count(WorkGroup::all()->last()->children));
    }

    /** @test */
    public function a_work_group_can_be_update()
    {
        create(WorkGroup::class, 5);
        $data = [
            'title' => 'برق',
            'type' => 'TENDER',
            'priorty' => 3,
            'children' => [1, 2]
        ];
        $workgroup = create(WorkGroup::class)->first();
        $this->json('PUT', '/api/workgroup/' . $workgroup->id, $data)
        ->assertStatus(200);

        $this->assertDatabaseHas('work_groups', [
            'title' => 'برق',
            'type' => 'TENDER',
            'priorty' => 3,
        ]);
    }

    /** @test */
    public function a_children_work_group_can_retrived_by_parent_work_group()
    {
        // create a parent
        $parent = create(WorkGroup::class)->first();

        // make assign this work groups to it
        create(WorkGroup::class, 10);
        for ($i = 2; $i < 12; ++$i) {
            $workgroup = WorkGroup::where('id', $i)->first();
            $workgroup->parent_id = $parent->id;
            $workgroup->save();
        }
        // make sure the parent have children
        $result = $this->call('GET', 'api/site/child/workgroups/' . $parent->id)->assertStatus(200);
        $this->assertEquals(10, count($result['data']));
    }
}
