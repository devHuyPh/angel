<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('rankings', function (Blueprint $table) {
      $table->id();
      $table->string('ranking_name')->default(null)->nullable();
      $table->string('ranking_name')->default(null)->nullable();
      $table->string('rank_lavel_unq')->default(null)->nullable();
      $table->decimal('rank_lavel_unq')->default(0);
      $table->decimal('min_deposit')->default(0);
      $table->decimal('min_earning')->default(0);
      $table->string('description')->default(null)->nullable();
      $table->text('rank_icon')->default(null)->nullable();
      $table->integer('sort_by')->default(null)->nullable();
      $table->tinyInteger('status')->default(null)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('rankings');
  }
};
