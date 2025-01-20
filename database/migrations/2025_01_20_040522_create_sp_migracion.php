<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS poblacion.migracion_datos;

            CREATE PROCEDURE poblacion.migracion_datos()
            BEGIN
                -- Declarar las variables necesarias
                DECLARE CONTINUAR INT DEFAULT 0;
                DECLARE v_id INT;
                DECLARE v_nombre VARCHAR(250);
                DECLARE v_paterno VARCHAR(250);
                DECLARE v_materno VARCHAR(250);
                DECLARE v_telefono VARCHAR(250);
                DECLARE v_calle VARCHAR(250);
                DECLARE v_numero_exterior VARCHAR(250);
                DECLARE v_numero_interior VARCHAR(250);
                DECLARE v_colonia VARCHAR(250);
                DECLARE v_cp INT;
                DECLARE id_persona INT;

                -- Declarar el cursor
                DECLARE arregloPersonas CURSOR FOR
                    SELECT id, nombre, paterno, materno, telefono, calle, numero_exterior, numero_interior, colonia, cp
                    FROM poblacion.poblacion_temporal;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET CONTINUAR = 1;

                OPEN arregloPersonas;

                read_loop: LOOP
                    FETCH arregloPersonas INTO v_id, v_nombre, v_paterno, v_materno, v_telefono, v_calle, v_numero_exterior, v_numero_interior, v_colonia, v_cp;

                    IF CONTINUAR THEN
                        LEAVE read_loop;
                    END IF;

                    -- Persona
                    IF NOT EXISTS (
                        SELECT 1
                        FROM personas
                        WHERE nombre = v_nombre COLLATE utf8mb4_unicode_ci
                            AND paterno = v_paterno COLLATE utf8mb4_unicode_ci
                            AND materno = v_materno COLLATE utf8mb4_unicode_ci
                    ) THEN
                        INSERT INTO personas (nombre, paterno, materno)
                        VALUES (v_nombre, v_paterno, v_materno);

                        SET id_persona = LAST_INSERT_ID();
                    ELSE
                        SET id_persona = (SELECT id
                        FROM personas
                        WHERE nombre = v_nombre COLLATE utf8mb4_unicode_ci
                            AND paterno = v_paterno COLLATE utf8mb4_unicode_ci
                            AND materno = v_materno COLLATE utf8mb4_unicode_ci
                        LIMIT 1);
                    END IF;

                    -- Telefonos
                    IF NOT EXISTS (
                        SELECT 1
                        FROM telefonos
                        WHERE id_persona = id_persona
                            AND telefono = v_telefono COLLATE utf8mb4_unicode_ci
                    ) THEN
                        INSERT INTO telefonos (id_persona, telefono)
                        VALUES (id_persona, v_telefono);
                    END IF;

                    -- Direcciones
                    IF NOT EXISTS (
                        SELECT 1
                        FROM direcciones
                        WHERE id_persona = id_persona
                            AND cp = v_cp COLLATE utf8mb4_unicode_ci
                            AND colonia = v_colonia COLLATE utf8mb4_unicode_ci
                            AND numero_exterior = v_numero_exterior COLLATE utf8mb4_unicode_ci
                            AND numero_interior = v_numero_interior COLLATE utf8mb4_unicode_ci
                            AND calle = v_calle COLLATE utf8mb4_unicode_ci
                    ) THEN
                        INSERT INTO direcciones (id_persona, cp, colonia, numero_exterior, numero_interior, calle)
                        VALUES (id_persona, v_cp, v_colonia, v_numero_exterior, v_numero_interior, v_calle);
                    END IF;

                END LOOP;
                CLOSE arregloPersonas;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS poblacion.migracion_datos');
    }
};
