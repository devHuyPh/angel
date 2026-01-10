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
        Schema::create('kyc_pending', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Liên kết với bảng users
            $table->integer('kyc_form_id')->nullable(); // Liên kết với bảng kyc_forms (nếu có)
            $table->string('name'); // Tên người dùng gửi KYC
            $table->string('avatar')->nullable(); // Đường dẫn ảnh đại diện (nếu yêu cầu)
            $table->string('verification_type'); // Loại xác minh (Identity, Address, v.v.)
            $table->json('data'); // Dữ liệu KYC chi tiết (JSON)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trạng thái
            $table->timestamps(); // created_at, updated_at

    // Foreign keys
    $table->foreign('customer_id')->references('id')->on('ec_customers')->onDelete('cascade');
    $table->foreign('kyc_form_id')->references('id')->on('kyc_forms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_pending');
    }
};
