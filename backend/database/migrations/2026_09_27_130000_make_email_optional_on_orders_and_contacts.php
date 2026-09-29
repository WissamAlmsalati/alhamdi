<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The site asks for a phone number on inquiries and contact messages; email becomes optional.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
