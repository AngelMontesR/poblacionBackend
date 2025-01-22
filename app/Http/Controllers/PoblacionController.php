<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargaPoblacionRequest;
use App\Http\Requests\ConsultaPoblacionRequest;
use App\Http\Resources\PoblacionResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Persona;

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

            //Load Data
            $this->cargaTablaTemporal($rutaCompleta,$nombreTabla);

            $registros = DB::select("select * from $nombreTabla");
            Log::info("Registros cargados: ".count($registros));
            Log::info($registros);
            $this->ejecutarStoreProcedure();
            Storage::delete($rutaFinal);
            return response()->json(['message' => 'Carga Exitosa'], 200);
        } catch (\Exception $e) {
            Log::info("Error al importar los datos: ".$e->getMessage());
            return response()->json(["error" => "Error al importar los datos"], 500);
        }
    }

    /*
    * Funcion que retorna los datos de las personas
    */
    public function obtenerPoblacion(ConsultaPoblacionRequest $request)
    {
      try {
        $poblacion = Persona::with('direcciones','telefonos')->paginate(100);
        return new PoblacionResource($poblacion);
      } catch (\Exception $e) {
        Log::info("Error al consultar los datos: ".$e->getMessage());
        return response()->json(["error" => "Error al consultar los datos:"], 500);
      }
    }

    /*
     * Funcion que permite cargar informacion a tabla temporal
     */
    private function cargaTablaTemporal($ruta,$tabla){
      try {
            DB::statement("LOAD DATA LOCAL INFILE '$ruta' INTO TABLE $tabla
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (nombre, paterno, materno, telefono, calle, numero_exterior, numero_interior, colonia, cp)");
        } catch (\Exception $th) {
            Log::error("Error en la carga de registros en tabla temporal");
            return false;
        }
    }

    /*
    * Funcion que permite la creacion de la tabla temporal
    */
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

    /*
    * Funcion que permite la ejecucion del Store Procedure para migrar los datos tabla temporal a personas,telefono,direccion
    */
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
