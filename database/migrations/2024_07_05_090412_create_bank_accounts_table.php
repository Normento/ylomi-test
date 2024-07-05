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
        Schema::create('bank_accounts', function (Blueprint $table) {
        $table->id();
        $table->string('account_number')->unique();
        $table->string('name');
        $table->string('surname');
        $table->enum('status', ['active', 'disabled'])->default('active');
        $table->decimal('balance', 15, 2)->default(0);
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
