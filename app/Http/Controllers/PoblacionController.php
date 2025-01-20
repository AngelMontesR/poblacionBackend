<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargaPoblacionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PoblacionController extends Controller
{
    /*
    * Funcion que permite cargar el archivo a una tabla temporal
    */
    public function cargaArchivo(CargaPoblacionRequest $request)
    {
        try {
            //Creacion de tabla temporal
            $nombreTabla = $this->tablaTemporal();

            // Creación de rutas para almacenamiento de archivo
            $carpeta = 'public' . DIRECTORY_SEPARATOR . 'carga-temporal';
            $nombreArchivo = $request->file('archivo')->getClientOriginalName();
            $rutaFinal = $carpeta . DIRECTORY_SEPARATOR . $nombreArchivo;

            // Verificar si la carpeta existe, si no, crearla
            if (!Storage::exists($carpeta)) {
                Storage::makeDirectory($carpeta);
            }

            // Carga de archivo a ubicación en el servidor
            Storage::put($rutaFinal, file_get_contents($request->file('archivo')->getRealPath()));

            //Verifica si existe el archivo antes de realizar el load data
            if(!Storage::exists($rutaFinal))
            {
                return response()->json(["error" => "No existe el archivo en storage servidor"], 500);
            }

            //Obtenemos la ruta completa para indicar ubicacion del archivo a cargar
            $rutaCompleta = "/var/www/storage/app/public/carga-temporal" . DIRECTORY_SEPARATOR . $nombreArchivo;

            DB::statement("LOAD DATA LOCAL INFILE '$rutaCompleta' INTO TABLE $nombreTabla
                            FIELDS TERMINATED BY ','
                            LINES TERMINATED BY '\n'
                            IGNORE 1 LINES
                            (nombre, paterno, materno, telefono, calle, numero_exterior, numero_interior, colonia, cp)");

            $registros = DB::select("select * from $nombreTabla");
            $this->ejecutarStoreProcedure();
            return response()->json(['message' => 'Carga Exitosa'], 200);
        } catch (\Exception $e) {
            Log::info("Error al importar los datos: ".$e->getMessage());
            return response()->json(["error" => "Error al importar los datos"], 500);
        }
    }


    private function tablaTemporal():String{
        try {
            $tabla = 'poblacion_temporal';
            Log::info("Creacion de tabla temporal: ".$tabla);
            DB::statement("
                CREATE TEMPORARY TABLE $tabla (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(250) COLLATE utf8mb4_unicode_ci,
                    paterno VARCHAR(250) COLLATE utf8mb4_unicode_ci,
                    materno VARCHAR(250) COLLATE utf8mb4_unicode_ci,
                    telefono VARCHAR(250),
                    calle VARCHAR(250),
                    numero_exterior VARCHAR(250),
                    numero_interior VARCHAR(250),
                    colonia VARCHAR(250),
                    cp INTEGER,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            return $tabla;
        } catch (\Exception $th) {
            Log::info("Ocurrio un error al crear tabla temporal: ".$tabla);
            return null;
        }
    }

    private function ejecutarStoreProcedure()
    {
        try {
            DB::statement('CALL poblacion.migracion_datos;');
            Log::info("Se ejecuto correctamente el store procedure");
            return true;
        } catch (\Exception $th) {
            Log::info("Ocurrio un error al ejecutar el store procedure");
            return false;
        }
    }

}
