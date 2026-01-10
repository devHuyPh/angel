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
        Schema::table('kyc_logs', function (Blueprint $table) {
            $table->string('admin_name')->nullable()->after('admin_id');
            $table->string('admin_email')->nullable()->after('admin_name');
            $table->string('customer_status')->nullable()->after('customer_phone');
            $table->string('affected_entity')->nullable()->after('kyc_status');
            $table->unsignedBigInteger('affected_entity_id')->nullable()->after('affected_entity');
            $table->boolean('system_notification')->default(false)->after('affected_entity_id');
            $table->json('data_before')->nullable()->after('system_notification');
            $table->json('data_after')->nullable()->after('data_before');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_logs', function (Blueprint $table) {
            //
        });
    }
};
