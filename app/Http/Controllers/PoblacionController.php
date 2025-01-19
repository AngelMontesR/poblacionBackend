<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargaPoblacionRequest;

class PoblacionController extends Controller
{

    public function cargaArchivo(CargaPoblacionRequest $request)
    {
        dd($request);
    }

}
