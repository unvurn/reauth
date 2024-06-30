<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_open_id_connections', function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->string('issuer');
            $table->string('subject');
            $table->unique(['issuer', 'subject']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_open_id_connections');
    }
};
