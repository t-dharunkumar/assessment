<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up()
{
    DB::statement("
        ALTER TABLE assessment_attempts
        MODIFY status ENUM(
            'pending',
            'in_progress',
            'completed'
        ) NOT NULL DEFAULT 'pending'
    ");
}


    public function down(): void
    {
        DB::statement("
            ALTER TABLE assessment_attempts 
            MODIFY status 
            ENUM('in_progress','submitted','timed_out','cancelled') 
            NOT NULL DEFAULT 'in_progress'
        ");
    }
};

