<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('configurable_id', 64)->change();
        });
    }

    public function down(): void
    {
        // Narrowing the column would discard UUID and ULID references created after the upgrade.
    }
};
