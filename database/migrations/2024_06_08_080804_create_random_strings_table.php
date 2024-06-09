<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('random_strings', function (Blueprint $table) {
            $table->id();
            $table->string('string', 50)->unique();
            $table->timestamps();
            $table->index('random_string'); // Index for random_string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('random_strings');
    }
};
