<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensivePawnSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;
    protected User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Owner Test',
            'email' => 'owner@test.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->petugas = User::create([
            'name' => 'Petugas Test',
            'email' => 'petugas@test.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        // Seed settings
        AppSetting::setValue(AppSetting::KEY_INTEREST_RATE, 10, 'Bunga');
        AppSetting::setValue(AppSetting::KEY_PENALTY_RATE_PER_DAY, 0.5, 'Denda');
        AppSetting::setValue(AppSetting::KEY_DEFAULT_TENOR_DAYS, 30, 'Tenor');
        AppSetting::setValue(AppSetting::KEY_PLAFON_PERCENTAGE, 80, 'Plafon');
    }

    public function test_login_flow_redirects_correctly_by_role(): void
    {
        $response = $this->post('/login', [
            'email' => 'petugas@test.id',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('petugas.dashboard'));

        $this->post('/logout');

        $responseAdmin = $this->post('/login', [
            'email' => 'admin@test.id',
            'password' => 'password',
        ]);
        $responseAdmin->assertRedirect(route('admin.dashboard'));
    }

    public function test_petugas_can_create_pawn_transaction_and_view_sbg(): void
    {
        $customer = Customer::create([
            'identity_number' => '1234567890123456',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.pawn.store'), [
            'customer_id' => $customer->id,
            'item_name' => 'iPhone 13 Pro Max',
            'category' => 'HP/Smartphone',
            'brand' => 'Apple',
            'estimated_value' => 10000000,
            'loan_amount' => 8000000,
            'tenor_days' => 30,
        ]);

        $pawn = PawnTransaction::latest()->first();
        $this->assertNotNull($pawn);
        $this->assertEquals(8000000, $pawn->loan_amount);
        $this->assertEquals(800000, $pawn->interest_amount); // 10% of 8M
        $this->assertEquals(PawnTransaction::STATUS_TERSIMPAN, $pawn->status);

        $response->assertRedirect(route('petugas.pawn.sbg', $pawn->id));

        $sbgView = $this->actingAs($this->petugas)->get(route('petugas.pawn.sbg', $pawn->id));
        $sbgView->assertStatus(200);
        $sbgView->assertSee('SURAT BUKTI GADAI');
    }

    public function test_petugas_can_process_pelunasan(): void
    {
        $customer = Customer::create(['identity_number' => '1111222233334444', 'name' => 'Siti']);
        $item = Item::create([
            'item_code' => 'BRG-001',
            'name' => 'Laptop Asus',
            'category' => 'Laptop',
            'status' => Item::STATUS_TERSIMPAN,
            'location' => Item::LOCATION_RAK_GUDANG,
        ]);
        $pawn = PawnTransaction::create([
            'ticket_number' => 'SBG-20260818-0001',
            'barcode_code' => 'BC-TEST1',
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'user_id' => $this->petugas->id,
            'estimated_value' => 5000000,
            'loan_amount' => 4000000,
            'interest_rate' => 10,
            'interest_amount' => 400000,
            'tenor_days' => 30,
            'total_amount' => 4400000,
            'pawn_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays(30),
            'status' => PawnTransaction::STATUS_TERSIMPAN,
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.payments.store'), [
            'pawn_transaction_id' => $pawn->id,
            'payment_type' => 'pelunasan',
        ]);

        $pawn->refresh();
        $this->assertEquals(PawnTransaction::STATUS_LUNAS, $pawn->status);

        $payment = PawnPayment::latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals(4400000, $payment->total_paid);
    }

    public function test_petugas_can_process_purchase_transaction(): void
    {
        $customer = Customer::create(['identity_number' => '9999888877776666', 'name' => 'Joko']);

        $response = $this->actingAs($this->petugas)->post(route('petugas.purchases.store'), [
            'customer_id' => $customer->id,
            'item_name' => 'TV LED 43 Inch',
            'category' => 'TV/Electronics',
            'purchase_price' => 2500000,
            'selling_price' => 3200000,
        ]);

        $purchase = PurchaseTransaction::latest()->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(2500000, $purchase->purchase_price);

        $item = Item::where('name', 'TV LED 43 Inch')->first();
        $this->assertEquals(Item::STATUS_STOK_ETALASE, $item->status);
        $this->assertEquals(Item::LOCATION_ETALASE, $item->location);
    }

    public function test_admin_can_update_item_warehouse_location(): void
    {
        $item = Item::create([
            'item_code' => 'BRG-999',
            'name' => 'Kamera Canon',
            'category' => 'Kamera',
            'status' => Item::STATUS_TERSIMPAN,
            'location' => Item::LOCATION_RAK_GUDANG,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.warehouse.update-location', $item->id), [
            'location' => 'rak_lelang',
            'notes' => 'Pindah ke rak lelang',
        ]);

        $item->refresh();
        $this->assertEquals(Item::LOCATION_RAK_LELANG, $item->location);
    }

    public function test_owner_can_update_settings(): void
    {
        $response = $this->actingAs($this->owner)->put(route('owner.settings.update'), [
            'default_interest_rate' => 12,
            'penalty_rate_per_day' => 0.6,
            'default_tenor_days' => 15,
            'plafon_percentage' => 75,
        ]);

        $response->assertRedirect(route('owner.settings.index'));

        $this->assertEquals(12, AppSetting::getValue(AppSetting::KEY_INTEREST_RATE));
    }
}
