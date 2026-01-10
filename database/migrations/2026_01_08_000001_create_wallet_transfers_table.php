<?php
// database\migrations\2026_01_08_000001_create_wallet_transfers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_customer_id')->constrained('ec_customers')->cascadeOnDelete();
            $table->foreignId('to_customer_id')->constrained('ec_customers')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('reference')->unique();
            $table->string('status')->default('completed');
            $table->string('note')->nullable();
            $table->string('code_used')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['from_customer_id', 'to_customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transfers');
    }
};
