<?php

namespace App\Services;

use Illuminate\Support\Str;

class BarcodeGeneratorService
{
    /**
     * Generate unique ticket number for Pawn Transaction (format: SBG-YYYYMMDD-XXXX).
     */
    public function generateTicketNumber(): string
    {
        $datePrefix = date('Ymd');
        $randomSuffix = strtoupper(Str::random(4));
        return "SBG-{$datePrefix}-{$randomSuffix}";
    }

    /**
     * Generate barcode hash/code string.
     */
    public function generateBarcodeCode(string $ticketNumber): string
    {
        return "BC-" . strtoupper(md5($ticketNumber . time()));
    }

    /**
     * Generate transaction number for Purchase (format: BELI-YYYYMMDD-XXXX).
     */
    public function generatePurchaseNumber(): string
    {
        $datePrefix = date('Ymd');
        $randomSuffix = strtoupper(Str::random(4));
        return "BELI-{$datePrefix}-{$randomSuffix}";
    }

    /**
     * Generate payment number for Pelunasan/Perpanjangan (format: BAYAR-YYYYMMDD-XXXX).
     */
    public function generatePaymentNumber(): string
    {
        $datePrefix = date('Ymd');
        $randomSuffix = strtoupper(Str::random(4));
        return "BAYAR-{$datePrefix}-{$randomSuffix}";
    }

    /**
     * Generate item code (format: BRG-XXXXXX).
     */
    public function generateItemCode(): string
    {
        return "BRG-" . strtoupper(Str::random(6));
    }
}
