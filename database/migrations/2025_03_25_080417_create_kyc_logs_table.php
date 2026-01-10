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
        Schema::create('kyc_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kyc_pending_id')->nullable();
            $table->string('kyc_pending_name')->nullable();
            $table->string('kyc_verification_type')->nullable();
            $table->string('kyc_status')->nullable(); // Trạng thái của kyc_pending tại thời điểm log
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('admin_name')->nullable();
            $table->string('admin_email')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('action'); // Hành động: submitted, approved, rejected
            $table->string('affected_entity')->nullable(); // Đối tượng bị ảnh hưởng (customer, account,...)
            $table->unsignedBigInteger('affected_entity_id')->nullable(); // ID của đối tượng bị ảnh hưởng
            $table->boolean('system_notification')->default(false); // Có gửi thông báo hệ thống không
            $table->json('data_before')->nullable(); // Dữ liệu trước khi thay đổi
            $table->json('data_after')->nullable(); // Dữ liệu sau khi thay đổi
            $table->text('note')->nullable(); // Ghi chú
            $table->text('reason')->nullable(); // Lý do (nếu rejected)
            $table->timestamp('action_at')->nullable(); // Thời gian thực hiện hành động
            $table->timestamps();

            // Foreign keys
            $table->foreign('kyc_pending_id')->references('id')->on('kyc_pending')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('ec_customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_logs');
    }
};
