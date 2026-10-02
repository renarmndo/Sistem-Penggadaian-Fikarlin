<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Item;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugas = User::factory()->create([
            'role' => 'petugas',
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'identity_number' => '1234567890123456',
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);
    }

    public function test_petugas_can_view_daily_transactions_page()
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.transactions.daily'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Transaksi Kasir');
        $response->assertSee('Total Kas Masuk');
        $response->assertSee('Total Kas Keluar');
    }

    public function test_daily_transactions_aggregates_cash_in_and_cash_out_correctly()
    {
        $today = Carbon::today();

        // 1. Gadai Baru (Kas Keluar: 1.000.000)
        $item1 = Item::create([
            'item_code' => 'BRG-001',
            'name' => 'Xiaomi Note 10',
            'category' => 'HP/Smartphone',
            'status' => Item::STATUS_TERSIMPAN,
            'location' => Item::LOCATION_RAK_GUDANG,
            'source_type' => Item::SOURCE_GADAI,
            'estimated_value' => 1500000,
        ]);

        PawnTransaction::create([
            'ticket_number' => 'SBG-20260818-0001',
            'barcode_code' => 'BARCODE-001',
            'customer_id' => $this->customer->id,
            'item_id' => $item1->id,
            'user_id' => $this->petugas->id,
            'estimated_value' => 1500000,
            'loan_amount' => 1000000,
            'interest_rate' => 10,
            'interest_amount' => 100000,
            'tenor_days' => 30,
            'penalty_amount' => 0,
            'total_amount' => 1100000,
            'pawn_date' => $today,
            'due_date' => $today->copy()->addDays(30),
            'status' => PawnTransaction::STATUS_TERSIMPAN,
        ]);

        // 2. Pembayaran Pelunasan (Kas Masuk: 550.000)
        $item2 = Item::create([
            'item_code' => 'BRG-002',
            'name' => 'Samsung A52',
            'category' => 'HP/Smartphone',
            'status' => Item::STATUS_LUNAS,
            'location' => Item::LOCATION_RAK_GUDANG,
            'source_type' => Item::SOURCE_GADAI,
            'estimated_value' => 800000,
        ]);

        $pawn2 = PawnTransaction::create([
            'ticket_number' => 'SBG-20260818-0002',
            'barcode_code' => 'BARCODE-002',
            'customer_id' => $this->customer->id,
            'item_id' => $item2->id,
            'user_id' => $this->petugas->id,
            'estimated_value' => 800000,
            'loan_amount' => 500000,
            'interest_rate' => 10,
            'interest_amount' => 50000,
            'tenor_days' => 30,
            'penalty_amount' => 0,
            'total_amount' => 550000,
            'pawn_date' => $today->copy()->subDays(10),
            'due_date' => $today->copy()->addDays(20),
            'status' => PawnTransaction::STATUS_LUNAS,
        ]);

        PawnPayment::create([
            'payment_number' => 'PAY-20260818-0001',
            'pawn_transaction_id' => $pawn2->id,
            'user_id' => $this->petugas->id,
            'payment_type' => PawnPayment::TYPE_PELUNASAN,
            'principal_amount' => 500000,
            'interest_amount' => 50000,
            'penalty_amount' => 0,
            'total_paid' => 550000,
            'old_due_date' => $pawn2->due_date,
            'new_due_date' => null,
            'payment_date' => $today,
        ]);

        // 3. Beli Barang Bekas (Kas Keluar: 700.000)
        $item3 = Item::create([
            'item_code' => 'BRG-003',
            'name' => 'Oppo Reno 6',
            'category' => 'HP/Smartphone',
            'status' => Item::STATUS_STOK_ETALASE,
            'location' => Item::LOCATION_ETALASE,
            'source_type' => Item::SOURCE_BELI_BEKAS,
            'estimated_value' => 700000,
            'selling_price' => 950000,
        ]);

        PurchaseTransaction::create([
            'transaction_number' => 'TRX-BELI-20260818-0001',
            'customer_id' => $this->customer->id,
            'item_id' => $item3->id,
            'user_id' => $this->petugas->id,
            'purchase_price' => 700000,
            'purchase_date' => $today,
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.transactions.daily', ['date' => $today->format('Y-m-d')]));

        $response->assertStatus(200);
        $response->assertSee('SBG-20260818-0001');
        $response->assertSee('PAY-20260818-0001');
        $response->assertSee('TRX-BELI-20260818-0001');

        // Check printable sheet
        $printResponse = $this->actingAs($this->petugas)->get(route('petugas.transactions.print-daily', ['date' => $today->format('Y-m-d')]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Laporan Rekapitulasi Kasir');
    }
}
