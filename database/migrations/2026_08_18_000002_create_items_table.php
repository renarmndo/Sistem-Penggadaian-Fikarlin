<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique();
            $table->string('name', 255)->index();
            $table->string('category', 100)->index();
            $table->string('brand', 100)->nullable();
            $table->string('model_type', 100)->nullable();
            $table->text('condition_notes')->nullable();
            $table->string('photo_path')->nullable();
            
            $table->enum('status', [
                'tersimpan',
                'diperpanjang',
                'siap_lelang',
                'terjual',
                'lunas',
                'stok_etalase'
            ])->default('tersimpan')->index();

            $table->enum('location', [
                'rak_gudang',
                'rak_lelang',
                'etalase'
            ])->default('rak_gudang')->index();

            $table->enum('source_type', [
                'gadai',
                'beli_barang_bekas'
            ])->default('gadai')->index();

            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->nullable()->comment('Harga Jual Lelang / Etalase');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
