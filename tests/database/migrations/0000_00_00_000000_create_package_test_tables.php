<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration{
    public function up()
    {
        Schema::create('items', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->enum('is_active', ['yes', 'no']);
            $table->timestamps();
        });
    }
};