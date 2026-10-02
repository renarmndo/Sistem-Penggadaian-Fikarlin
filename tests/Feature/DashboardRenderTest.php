<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        AppSetting::setValue(AppSetting::KEY_INTEREST_RATE, 10);
        AppSetting::setValue(AppSetting::KEY_PENALTY_RATE_PER_DAY, 0.5);
        AppSetting::setValue(AppSetting::KEY_DEFAULT_TENOR_DAYS, 30);
        AppSetting::setValue(AppSetting::KEY_PLAFON_PERCENTAGE, 80);
    }

    public function test_petugas_dashboard_and_views_render_successfully(): void
    {
        $petugas = User::create([
            'name' => 'Petugas',
            'email' => 'petugas@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        $this->actingAs($petugas)->get(route('petugas.dashboard'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.customers.index'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.customers.create'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.pawn.index'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.pawn.create'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.payments.search'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.purchases.index'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.purchases.create'))->assertStatus(200);
        $this->actingAs($petugas)->get(route('petugas.auction.index'))->assertStatus(200);
    }

    public function test_admin_dashboard_and_views_render_successfully(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.warehouse.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.duedate.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.auction.index'))->assertStatus(200);
    }

    public function test_owner_dashboard_and_views_render_successfully(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]);

        $this->actingAs($owner)->get(route('owner.dashboard'))->assertStatus(200);
        $this->actingAs($owner)->get(route('owner.settings.index'))->assertStatus(200);
        $this->actingAs($owner)->get(route('owner.users.index'))->assertStatus(200);
        $this->actingAs($owner)->get(route('owner.reports.index'))->assertStatus(200);
    }
}
