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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الاشتراك
            $table->string('type'); // نوع الاشتراك: annual, monthly, weekly
            $table->decimal('price', 10, 2); // السعر
            $table->text('description')->nullable(); // وصف الاشتراك
            $table->boolean('is_active')->default(true); // حالة الاشتراك
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
