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
        Schema::create('participation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participation_id')->constrained()->onDelete('cascade');
            $table->json('words_scores'); 
            $table->timestamps();
            
            // Adding indexes
            $table->index('participation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participation_details');
    }
};
