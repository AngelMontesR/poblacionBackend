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
        DB::statement('
            CREATE TEMPORARY TABLE poblacion.temp_poblacion (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(250),
                paterno VARCHAR(250),
                materno VARCHAR(250),
                telefono VARCHAR(250),
                calle VARCHAR(250),
                numero_exterior VARCHAR(250),
                numero_interior VARCHAR(250),
                colonia VARCHAR(250),
                cp INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ');
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('DROP TEMPORARY TABLE IF EXISTS poblacion.temp_poblacion;');
    }
};
