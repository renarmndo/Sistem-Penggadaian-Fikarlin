<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\InventoryMutation;
use App\Models\Item;
use App\Models\PawnPayment;
use App\Models\PawnTransaction;
use App\Models\PurchaseTransaction;
use App\Models\User;
use App\Services\BarcodeGeneratorService;
use App\Services\InventoryMutationService;
use App\Services\PawnCalculationService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder dummy data yang menelusuri seluruh alur bisnis penggadaian:
 * Gadai Baru -> Perpanjangan / Pelunasan / Jatuh Tempo (Siap Lelang) / Penjualan,
 * serta Beli Barang Bekas -> Stok Etalase -> Penjualan.
 */
class BusinessFlowSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $calcService = new PawnCalculationService();
        $barcodeService = new BarcodeGeneratorService();
        $mutationService = new InventoryMutationService();

        $petugas = User::where('role', User::ROLE_PETUGAS)->where('is_active', true)->firstOrFail();
        $admin = User::where('role', User::ROLE_ADMIN)->where('is_active', true)->firstOrFail();
        $petugasId = $petugas->id;
        $adminId = $admin->id;

        $interestRate = (float) AppSetting::getValue(AppSetting::KEY_INTEREST_RATE, 10);

        DB::transaction(function () use (
            $calcService,
            $barcodeService,
            $mutationService,
            $petugasId,
            $adminId,
            $interestRate
        ) {
            $this->seedCustomers();

            $this->seedPurchaseFlow($barcodeService, $mutationService, $petugasId);
            $this->seedActivePawns($calcService, $barcodeService, $mutationService, $petugasId, $interestRate);
            $this->seedExtendedPawns($calcService, $barcodeService, $mutationService, $petugasId, $interestRate);
            $this->seedPaidOffPawns($calcService, $barcodeService, $mutationService, $petugasId, $interestRate);
            $this->seedOverduePawns($calcService, $barcodeService, $mutationService, $petugasId, $adminId, $interestRate);

            $this->command?->info('Seeder alur bisnis penggadaian berhasil dijalankan.');
        });
    }

    private function seedCustomers(): void
    {
        $customers = [
            ['identity_number' => '3175012301900001', 'name' => 'Budi Santoso', 'phone' => '081234567001', 'address' => 'Jl. Melati No. 1, Jakarta'],
            ['identity_number' => '3175024507950002', 'name' => 'Siti Aminah', 'phone' => '081234567002', 'address' => 'Jl. Anggrek No. 12, Depok'],
            ['identity_number' => '3273011111900003', 'name' => 'Joko Prasetyo', 'phone' => '081234567003', 'address' => 'Jl. Mawar No. 5, Bekasi'],
            ['identity_number' => '3175036508800004', 'name' => 'Rina Marlina', 'phone' => '081234567004', 'address' => 'Jl. Kenanga No. 8, Bogor'],
            ['identity_number' => '3276031708900005', 'name' => 'Ahmad Fauzi', 'phone' => '081234567005', 'address' => 'Jl. Cemara No. 22, Tangerang'],
            ['identity_number' => '3175046105000006', 'name' => 'Dewi Lestari', 'phone' => '081234567006', 'address' => 'Jl. Flamboyan No. 3, Jakarta'],
            ['identity_number' => '3175052301020007', 'name' => 'Hendra Gunawan', 'phone' => '081234567007', 'address' => 'Jl. Damai No. 17, Depok'],
            ['identity_number' => '3175065206030008', 'name' => 'Maya Sari', 'phone' => '081234567008', 'address' => 'Jl. Bunga Raya No. 9, Bekasi'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }

    private function seedPurchaseFlow(
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        int $petugasId
    ): void {
        $purchases = [
            [
                'customer' => 'Budi Santoso',
                'name' => 'iPhone 11 64GB',
                'category' => 'HP/Smartphone',
                'brand' => 'Apple',
                'model_type' => 'A2221',
                'condition_notes' => 'Mulus, baterai 89%',
                'purchase_price' => 2500000,
                'selling_price' => 3200000,
                'days_ago' => 35,
                'sold' => false,
            ],
            [
                'customer' => 'Siti Aminah',
                'name' => 'TV LED 32 Inch',
                'category' => 'TV/Electronics',
                'brand' => 'Samsung',
                'model_type' => 'UA32N4000',
                'condition_notes' => 'Layar normal, remote lengkap',
                'purchase_price' => 1200000,
                'selling_price' => 1600000,
                'days_ago' => 25,
                'sold' => true,
                'sold_price' => 1550000,
                'buyer' => 'Pembeli Toko',
            ],
            [
                'customer' => 'Rina Marlina',
                'name' => 'Samsung Galaxy A52',
                'category' => 'HP/Smartphone',
                'brand' => 'Samsung',
                'model_type' => 'SM-A525F',
                'condition_notes' => 'Minus di pojok layar',
                'purchase_price' => 1800000,
                'selling_price' => 2400000,
                'days_ago' => 15,
                'sold' => false,
            ],
        ];

        foreach ($purchases as $index => $data) {
            $customer = Customer::where('name', $data['customer'])->firstOrFail();
            $itemCode = $barcodeService->generateItemCode();
            $purchaseDate = Carbon::today()->subDays($data['days_ago']);

            $item = Item::create([
                'item_code' => $itemCode,
                'name' => $data['name'],
                'category' => $data['category'],
                'brand' => $data['brand'],
                'model_type' => $data['model_type'],
                'condition_notes' => $data['condition_notes'],
                'status' => Item::STATUS_STOK_ETALASE,
                'location' => Item::LOCATION_ETALASE,
                'source_type' => Item::SOURCE_BELI_BEKAS,
                'estimated_value' => $data['purchase_price'],
                'selling_price' => $data['selling_price'],
                'created_at' => $purchaseDate,
                'updated_at' => $purchaseDate,
            ]);

            $transactionNumber = $barcodeService->generatePurchaseNumber();
            $purchase = PurchaseTransaction::create([
                'transaction_number' => $transactionNumber,
                'customer_id' => $customer->id,
                'item_id' => $item->id,
                'user_id' => $petugasId,
                'purchase_price' => $data['purchase_price'],
                'purchase_date' => $purchaseDate,
                'notes' => "Pembelian barang bekas dari {$customer->name}",
                'created_at' => $purchaseDate,
                'updated_at' => $purchaseDate,
            ]);

            $mutationService->recordMutation(
                $item,
                InventoryMutation::TYPE_MASUK_ETALASE,
                $petugasId,
                "Pembelian barang bekas {$transactionNumber} (Stok Etalase Toko)"
            );

            if (! empty($data['sold'])) {
                $this->sellItem($item, $data['sold_price'], $data['buyer'], $petugasId, $mutationService, $purchaseDate->addDays(3));
            }
        }
    }

    private function seedActivePawns(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        int $petugasId,
        float $interestRate
    ): void {
        $pawns = [
            [
                'customer' => 'Joko Prasetyo',
                'name' => 'Laptop Asus Vivobook 14',
                'category' => 'Laptop',
                'brand' => 'Asus',
                'model_type' => 'X1404',
                'condition_notes' => 'Normal, charger original',
                'estimated_value' => 6000000,
                'loan_amount' => 4800000,
                'tenor_days' => 30,
                'days_ago' => 10,
            ],
            [
                'customer' => 'Ahmad Fauzi',
                'name' => 'OPPO Reno 8',
                'category' => 'HP/Smartphone',
                'brand' => 'OPPO',
                'model_type' => 'CPH2359',
                'condition_notes' => 'Mulus fullset',
                'estimated_value' => 3500000,
                'loan_amount' => 2800000,
                'tenor_days' => 30,
                'days_ago' => 5,
            ],
        ];

        foreach ($pawns as $data) {
            $this->createPawn(
                $calcService,
                $barcodeService,
                $mutationService,
                $petugasId,
                $interestRate,
                $data['customer'],
                $data['name'],
                $data['category'],
                $data['brand'],
                $data['model_type'],
                $data['condition_notes'],
                $data['estimated_value'],
                $data['loan_amount'],
                $data['tenor_days'],
                $data['days_ago']
            );
        }
    }

    private function seedExtendedPawns(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        int $petugasId,
        float $interestRate
    ): void {
        $pawns = [
            [
                'customer' => 'Rina Marlina',
                'name' => 'Xiaomi Redmi Note 12',
                'category' => 'HP/Smartphone',
                'brand' => 'Xiaomi',
                'model_type' => '23021RAAEG',
                'condition_notes' => 'Baret kecil di frame',
                'estimated_value' => 2000000,
                'loan_amount' => 1600000,
                'tenor_days' => 30,
                'days_ago' => 45,
                'extension_days_ago' => 15,
                'extension_tenor' => 30,
            ],
            [
                'customer' => 'Dewi Lestari',
                'name' => 'Kamera Canon EOS M50',
                'category' => 'Kamera',
                'brand' => 'Canon',
                'model_type' => 'M50 Kit 15-45mm',
                'condition_notes' => 'Normal, lensa bersih',
                'estimated_value' => 5000000,
                'loan_amount' => 4000000,
                'tenor_days' => 30,
                'days_ago' => 70,
                'extension_days_ago' => 15,
                'extension_tenor' => 30,
            ],
        ];

        foreach ($pawns as $data) {
            $pawn = $this->createPawn(
                $calcService,
                $barcodeService,
                $mutationService,
                $petugasId,
                $interestRate,
                $data['customer'],
                $data['name'],
                $data['category'],
                $data['brand'],
                $data['model_type'],
                $data['condition_notes'],
                $data['estimated_value'],
                $data['loan_amount'],
                $data['tenor_days'],
                $data['days_ago']
            );

            $this->extendPawn(
                $calcService,
                $barcodeService,
                $mutationService,
                $pawn,
                $petugasId,
                $data['extension_days_ago'],
                $data['extension_tenor']
            );
        }
    }

    private function seedPaidOffPawns(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        int $petugasId,
        float $interestRate
    ): void {
        $pawns = [
            [
                'customer' => 'Budi Santoso',
                'name' => 'Speaker JBL Charge 5',
                'category' => 'Audio',
                'brand' => 'JBL',
                'model_type' => 'Charge 5',
                'condition_notes' => 'Normal, body mulus',
                'estimated_value' => 1500000,
                'loan_amount' => 1200000,
                'tenor_days' => 30,
                'days_ago' => 60,
                'paid_days_ago' => 30,
            ],
            [
                'customer' => 'Maya Sari',
                'name' => 'iPhone XR 64GB',
                'category' => 'HP/Smartphone',
                'brand' => 'Apple',
                'model_type' => 'A1980',
                'condition_notes' => 'Mulus, baterai 85%',
                'estimated_value' => 3000000,
                'loan_amount' => 2400000,
                'tenor_days' => 30,
                'days_ago' => 40,
                'paid_days_ago' => 40,
            ],
        ];

        foreach ($pawns as $data) {
            $pawn = $this->createPawn(
                $calcService,
                $barcodeService,
                $mutationService,
                $petugasId,
                $interestRate,
                $data['customer'],
                $data['name'],
                $data['category'],
                $data['brand'],
                $data['model_type'],
                $data['condition_notes'],
                $data['estimated_value'],
                $data['loan_amount'],
                $data['tenor_days'],
                $data['days_ago']
            );

            $this->settlePawn(
                $calcService,
                $barcodeService,
                $mutationService,
                $pawn,
                $petugasId,
                $data['paid_days_ago']
            );
        }
    }

    private function seedOverduePawns(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        int $petugasId,
        int $adminId,
        float $interestRate
    ): void {
        $overdueItems = [
            [
                'customer' => 'Hendra Gunawan',
                'name' => 'Samsung Galaxy S21 FE',
                'category' => 'HP/Smartphone',
                'brand' => 'Samsung',
                'model_type' => 'SM-G990E',
                'condition_notes' => 'Mulus, fullset',
                'estimated_value' => 7000000,
                'loan_amount' => 5600000,
                'tenor_days' => 30,
                'days_ago' => 80,
                'sold' => false,
            ],
            [
                'customer' => 'Ahmad Fauzi',
                'name' => 'iPad Air 4',
                'category' => 'Tablet',
                'brand' => 'Apple',
                'model_type' => 'A2316',
                'condition_notes' => 'Normal, body mulus',
                'estimated_value' => 8000000,
                'loan_amount' => 6400000,
                'tenor_days' => 30,
                'days_ago' => 90,
                'sold' => true,
                'sold_price' => 5000000,
                'buyer' => 'Pembeli Lelang',
                'sold_days_ago' => 20,
            ],
            [
                'customer' => 'Joko Prasetyo',
                'name' => 'Vivo V23e',
                'category' => 'HP/Smartphone',
                'brand' => 'Vivo',
                'model_type' => 'V2130',
                'condition_notes' => 'Minus di layar bagian bawah',
                'estimated_value' => 2200000,
                'loan_amount' => 1760000,
                'tenor_days' => 30,
                'days_ago' => 75,
                'sold' => true,
                'sold_price' => 1300000,
                'buyer' => 'Pembeli Lelang',
                'sold_days_ago' => 10,
            ],
        ];

        foreach ($overdueItems as $data) {
            $pawn = $this->createPawn(
                $calcService,
                $barcodeService,
                $mutationService,
                $petugasId,
                $interestRate,
                $data['customer'],
                $data['name'],
                $data['category'],
                $data['brand'],
                $data['model_type'],
                $data['condition_notes'],
                $data['estimated_value'],
                $data['loan_amount'],
                $data['tenor_days'],
                $data['days_ago']
            );

            $pawn->update([
                'status' => PawnTransaction::STATUS_SIAP_LELANG,
            ]);

            $pawn->item->update([
                'status' => Item::STATUS_SIAP_LELANG,
                'location' => Item::LOCATION_RAK_LELANG,
            ]);

            $mutationService->recordMutation(
                $pawn->item,
                InventoryMutation::TYPE_PINDAH_RAK_LELANG,
                $adminId,
                'Auto-detect jatuh tempo server: barang wanprestasi dipindah ke Rak Lelang'
            );

            if (! empty($data['sold'])) {
                $this->sellItem(
                    $pawn->item,
                    $data['sold_price'],
                    $data['buyer'],
                    $petugasId,
                    $mutationService,
                    Carbon::today()->subDays($data['sold_days_ago'])
                );
            }
        }
    }

    private function createPawn(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        int $petugasId,
        float $interestRate,
        string $customerName,
        string $itemName,
        string $category,
        string $brand,
        string $modelType,
        string $conditionNotes,
        float $estimatedValue,
        float $loanAmount,
        int $tenorDays,
        int $daysAgo
    ): PawnTransaction {
        $customer = Customer::where('name', $customerName)->firstOrFail();
        $pawnDate = Carbon::today()->subDays($daysAgo);

        $item = Item::create([
            'item_code' => $barcodeService->generateItemCode(),
            'name' => $itemName,
            'category' => $category,
            'brand' => $brand,
            'model_type' => $modelType,
            'condition_notes' => $conditionNotes,
            'status' => Item::STATUS_TERSIMPAN,
            'location' => Item::LOCATION_RAK_GUDANG,
            'source_type' => Item::SOURCE_GADAI,
            'estimated_value' => $estimatedValue,
            'created_at' => $pawnDate,
            'updated_at' => $pawnDate,
        ]);

        $interestAmount = $calcService->calculateInterest($loanAmount, $interestRate);
        $totalAmount = $loanAmount + $interestAmount;
        $dueDate = $calcService->calculateDueDate($pawnDate, $tenorDays);

        $ticketNumber = $barcodeService->generateTicketNumber();
        $pawn = PawnTransaction::create([
            'ticket_number' => $ticketNumber,
            'barcode_code' => $barcodeService->generateBarcodeCode($ticketNumber),
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'user_id' => $petugasId,
            'estimated_value' => $estimatedValue,
            'loan_amount' => $loanAmount,
            'interest_rate' => $interestRate,
            'interest_amount' => $interestAmount,
            'tenor_days' => $tenorDays,
            'penalty_amount' => 0,
            'total_amount' => $totalAmount,
            'pawn_date' => $pawnDate,
            'due_date' => $dueDate,
            'status' => PawnTransaction::STATUS_TERSIMPAN,
            'notes' => "Transaksi gadai {$itemName} oleh {$customer->name}",
            'created_at' => $pawnDate,
            'updated_at' => $pawnDate,
        ]);

        $mutationService->recordMutation(
            $item,
            InventoryMutation::TYPE_MASUK_RAK_GUDANG,
            $petugasId,
            "Barang baru dari transaksi gadai {$ticketNumber}"
        );

        return $pawn;
    }

    private function extendPawn(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        PawnTransaction $pawn,
        int $petugasId,
        int $extensionDaysAgo,
        int $extensionTenor
    ): void {
        $paymentDate = Carbon::today()->subDays($extensionDaysAgo);
        $baseDate = Carbon::parse($pawn->due_date);
        if ($paymentDate->gt($baseDate)) {
            $baseDate = $paymentDate;
        }
        $newDueDate = $calcService->calculateDueDate($baseDate, $extensionTenor);

        $penaltyData = $calcService->calculatePenalty(
            (float) $pawn->loan_amount,
            Carbon::parse($pawn->due_date),
            $paymentDate
        );

        $payment = PawnPayment::create([
            'payment_number' => $barcodeService->generatePaymentNumber(),
            'pawn_transaction_id' => $pawn->id,
            'user_id' => $petugasId,
            'payment_type' => PawnPayment::TYPE_PERPANJANGAN,
            'principal_amount' => 0,
            'interest_amount' => (float) $pawn->interest_amount,
            'penalty_amount' => $penaltyData['penalty_amount'],
            'total_paid' => (float) $pawn->interest_amount + $penaltyData['penalty_amount'],
            'old_due_date' => $pawn->due_date,
            'new_due_date' => $newDueDate,
            'payment_date' => $paymentDate,
            'notes' => "Perpanjangan tenor {$extensionTenor} hari",
            'created_at' => $paymentDate,
            'updated_at' => $paymentDate,
        ]);

        $pawn->update([
            'due_date' => $newDueDate,
            'tenor_days' => $extensionTenor,
            'status' => PawnTransaction::STATUS_DIPERPANJANG,
            'penalty_amount' => $payment->penalty_amount,
            'updated_at' => $paymentDate,
        ]);

        $pawn->item->update([
            'status' => Item::STATUS_DIPERPANJANG,
            'updated_at' => $paymentDate,
        ]);
    }

    private function settlePawn(
        PawnCalculationService $calcService,
        BarcodeGeneratorService $barcodeService,
        InventoryMutationService $mutationService,
        PawnTransaction $pawn,
        int $petugasId,
        int $paidDaysAgo
    ): void {
        $paymentDate = Carbon::today()->subDays($paidDaysAgo);

        $penaltyData = $calcService->calculatePenalty(
            (float) $pawn->loan_amount,
            Carbon::parse($pawn->due_date),
            $paymentDate
        );

        $totalPaid = (float) $pawn->loan_amount + (float) $pawn->interest_amount + $penaltyData['penalty_amount'];

        $payment = PawnPayment::create([
            'payment_number' => $barcodeService->generatePaymentNumber(),
            'pawn_transaction_id' => $pawn->id,
            'user_id' => $petugasId,
            'payment_type' => PawnPayment::TYPE_PELUNASAN,
            'principal_amount' => (float) $pawn->loan_amount,
            'interest_amount' => (float) $pawn->interest_amount,
            'penalty_amount' => $penaltyData['penalty_amount'],
            'total_paid' => $totalPaid,
            'old_due_date' => $pawn->due_date,
            'new_due_date' => null,
            'payment_date' => $paymentDate,
            'notes' => 'Pelunasan penuh gadai',
            'created_at' => $paymentDate,
            'updated_at' => $paymentDate,
        ]);

        $pawn->update([
            'status' => PawnTransaction::STATUS_LUNAS,
            'settled_at' => $paymentDate,
            'penalty_amount' => $penaltyData['penalty_amount'],
            'updated_at' => $paymentDate,
        ]);

        $mutationService->recordMutation(
            $pawn->item,
            InventoryMutation::TYPE_KELUAR_DITEBUS,
            $petugasId,
            "Pelunasan gadai {$pawn->ticket_number} (Status LUNAS)"
        );
    }

    private function sellItem(
        Item $item,
        float $sellingPrice,
        string $buyerName,
        int $userId,
        InventoryMutationService $mutationService,
        Carbon $soldAt
    ): void {
        $item->update([
            'selling_price' => $sellingPrice,
            'status' => Item::STATUS_TERJUAL,
            'updated_at' => $soldAt,
        ]);

        if ($item->pawnTransaction) {
            $item->pawnTransaction->update([
                'status' => PawnTransaction::STATUS_TERJUAL,
                'updated_at' => $soldAt,
            ]);
        }

        $mutationService->recordMutation(
            $item,
            InventoryMutation::TYPE_KELUAR_TERJUAL,
            $userId,
            "Penjualan barang lelang/etalase kepada {$buyerName} seharga Rp " . number_format($sellingPrice, 0, ',', '.')
        );
    }
}