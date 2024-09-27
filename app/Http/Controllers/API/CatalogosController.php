<?php


namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB;

use App\Models\Especificaciones;
use App\Models\Pacientes;
use App\Models\RegistroPaciente;
use Carbon\Carbon;

class CatalogosController extends Controller
{
    public function catalogos(Request $request)
    {
        try{
            $obj['especificaciones']    = Especificaciones::orderBy("id")->get();
            return response()->json($obj,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function clientes(Request $request)
    {
        try{
            $parametros = $request->all();
            $obj            = Pacientes::orderBy("nombres");
            $obj = $obj->where(function($query)use($parametros){
                return $query->where('nombres','LIKE','%'.$parametros['query'].'%');
            })->limit(5)->get();
            return response()->json($obj,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function filtros(Request $request)
    {
        try{
            $obj = RegistroPaciente::groupBy('anio')
            ->orderBy("anio", "asc")
            ->select(DB::raw('YEAR(fecha) anio'))
            ->get();
            $today = Carbon::now();

            $anios = [];
            foreach ($obj as $key => $value) {
                array_push($anios, $value->anio);
            }
            if($anios[count($anios)-1] != $today->year)
            {
                array_push($anios, $today->year);
            }
            

            return response()->json(["anios"=>$anios, "anio_actual"=>$today->year, "mes_actual"=>$today->month],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
}
