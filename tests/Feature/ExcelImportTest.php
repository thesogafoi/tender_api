<?php

namespace Tests\Feature;

use App\AdInviter;
use App\Advertise;
use App\Province;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_advertises_by_an_excel_file()
    {
        $file = Storage::get('public/advertise.xlsx');
        $file = UploadedFile::fake()->createWithContent('advertise.xlsx', $file);
        create(WorkGroup::class, 10);
        create(Province::class, 10);
        $this->call('POST', 'api/advertise/excel/create', [], [], ['excel_file' => $file], [])
        ->assertStatus(200);
        $advertiseCreated = Advertise::all()->first();

        $expectedCodeNumber = $advertiseCreated->id . Jalalian::now()->format('m')
        . Jalalian::now()->format('d');
        $this->assertEquals($expectedCodeNumber, $advertiseCreated->tender_code);

        $this->assertCount(6, Advertise::all());
    }

    /** @test */
    public function an_admin_can_create_work_groups_by_an_excel_file()
    {
        $file = Storage::get('public/workgroup.xlsx');
        $file = UploadedFile::fake()->createWithContent('workgroup.xlsx', $file);
        $this->call('POST', 'api/workgroup/excel/create', [], [], ['excel_file' => $file], [])
        ->assertStatus(200);
        $this->assertCount(13, WorkGroup::all());
    }

    /** @test */
    public function an_admin_can_create_multiple_ad_inviters_by_an_excel_file()
    {
        $file = Storage::get('public/adinviter.xlsx');
        $file = UploadedFile::fake()->createWithContent('adinviter.xlsx', $file);
        $this->call('POST', 'api/adinviter/excel/create', [], [], ['excel_file' => $file], [])
        ->assertStatus(200);
        $this->assertCount(6, AdInviter::all());
    }
}
