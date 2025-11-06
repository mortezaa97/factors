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
        Schema::create('factors', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('customer_id')->constrained('users');
            $table->decimal('total_price', 20, 0);
            $table->integer('total_count')->default(1);
            $table->smallInteger('type')->default(1);  // صورحساب نوع اول - صورتحساب نوع دوم - صورتحساب بازگشتی - صورتحساب نوع سوم - خرید
            $table->smallInteger('pattern')->default(1); // الگوی صورتحساب
            $table->smallInteger('subject')->default(1); // موضوع صورتحساب
            $table->smallInteger('settlement_method')->default(1); // روش تسویه
            $table->string('finance_year')->nullable(); // سال مالی
            $table->date('date_time')->nullable(); // تاریخ فاکتور
            $table->longText('note')->nullable();
            $table->json('files')->nullable();
            $table->decimal('credit', 20, 0)->default(0); // سهم نسیه از پرداخت
            $table->decimal('cash', 20, 0)->default(0); // سهم نقدی از پرداخت
            $table->decimal('subject_of_17', 20, 0)->default(0); // موضوع ماده 17

            // اطلاعات پرداخت
            $table->string('switch_number')->nullable(); // شماره سوییچ پرداخت
            $table->string('acceptor_number')->nullable(); // شماره پذیرنده فروشگاهی
            $table->string('terminal_number')->nullable(); // شماره پایانه فروشگاهی
            $table->string('ref_number')->nullable(); // شماره پیگیری فروشگاهی
            $table->string('cart_number')->nullable(); // شماره کارت پرداخت کننده
            $table->string('national_code')->nullable(); // شماره ملی - شناسه ملی - کد اتباع
            $table->dateTime('pay_datetime')->nullable(); // تاریخ و زمان پرداخت صورتحساب

            // اطلاعات سامانه مودیان
            $table->dateTime('sync_at')->nullable(); // زمان ارسال به سامانه مودیان
            $table->longText('tax_ref_code')->nullable(); // شماره رفرنس جهت رهگیری فاکتور
            $table->longText('tax_unique_code')->nullable(); // شناسه منحصر به فرد مالیاتی

            $table->decimal('discount', 16, 0)->nullable();
            $table->decimal('payable', 16, 0)->nullable();

            $table->dateTime('inquire_sync_at')->nullable();
            $table->string('subject_code')->nullable();
            $table->boolean('is_buy')->default(false);
            $table->boolean('is_pre')->default(0);
            $table->boolean('is_return')->default(false);

            $table->string('gateway')->nullable(); // روش پرداخت
            $table->string('pmt')->nullable(); // روش پرداخت
            $table->decimal('trn', 14, 0)->nullable(); // شماره پیگیری - شماره مرجع

            $table->decimal('consfee', 19, 0)->nullable();
            $table->decimal('spro', 19, 0)->nullable();
            $table->decimal('bros', 19, 0)->nullable();

            $table->string('crn')->nullable();

            $table->string('bank')->nullable();
            $table->boolean('is_online')->default(false);
            $table->foreignId('payment_id')->nullable()->constrained('payments');

            
            $table->decimal('total_vat', 20, 0);
            $table->decimal('total_duties', 20, 0)->default(0);

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
        Schema::dropIfExists('factors');
    }
};

