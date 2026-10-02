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
        Schema::create('pawn_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->string('barcode_code', 100)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('estimated_value', 15, 2);
            $table->decimal('loan_amount', 15, 2)->comment('Nilai Plafon Pinjaman');
            $table->decimal('interest_rate', 5, 2)->comment('Persentase Bunga %');
            $table->decimal('interest_amount', 15, 2)->comment('Nominal Bunga');
            $table->integer('tenor_days')->default(30);
            $table->decimal('penalty_amount', 15, 2)->default(0)->comment('Denda Keterlambatan');
            $table->decimal('total_amount', 15, 2);

            $table->date('pawn_date');
            $table->date('due_date')->index();
            $table->timestamp('settled_at')->nullable();

            $table->enum('status', [
                'tersimpan',
                'diperpanjang',
                'lunas',
                'siap_lelang',
                'terjual'
            ])->default('tersimpan')->index();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pawn_transactions');
    }
};
