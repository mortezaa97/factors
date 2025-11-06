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
        Schema::create('factor_has_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factor_id')->constrained('factors');
            $table->morphs(name: 'model');
            $table->smallInteger('count')->default(1);
            $table->decimal('unit_price', 20, 0);
            $table->decimal('discount', 20, 0)->default(0); // تخفیف کل
            $table->decimal('vat', 20, 0)->default(0); // مبلغ مالیات بر ارزش افزوده
            $table->decimal('founds', 20, 0)->default(0); // مبلغ سایر وجوه قانونی
            $table->decimal('duties', 20, 0)->default(0); // سایر مالیات و عوارض
            $table->decimal('shared_vat', 20, 0)->default(0); // سهم ارزش افزوده از پرداخت
            $table->decimal('shared_cash', 20, 0)->default(0); // سهم نقدی از پرداخت

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factor_has_items');
    }
};

