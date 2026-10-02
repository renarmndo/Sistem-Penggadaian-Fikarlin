<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => AppSetting::KEY_INTEREST_RATE,
                'value' => '10',
                'label' => 'Persentase Bunga Gadai (%)',
                'description' => 'Persentase bunga default per periode tenor (contoh: 10%)',
            ],
            [
                'key' => AppSetting::KEY_PENALTY_RATE_PER_DAY,
                'value' => '0.5',
                'label' => 'Rate Denda Keterlambatan Per Hari (%)',
                'description' => 'Persentase denda keterlambatan per hari dari nominal pinjaman',
            ],
            [
                'key' => AppSetting::KEY_DEFAULT_TENOR_DAYS,
                'value' => '30',
                'label' => 'Tenor Baku (Hari)',
                'description' => 'Durasi tenor default jatuh tempo transaksi gadai baru dalam satuan hari',
            ],
            [
                'key' => AppSetting::KEY_PLAFON_PERCENTAGE,
                'value' => '80',
                'label' => 'Batas Plafon Pinjaman (%)',
                'description' => 'Persentase maksimal pinjaman dari nilai taksiran barang',
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
