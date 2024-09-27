<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB;

use App\Models\RegistroEspecificacion;
use App\Models\RegistroPaciente;
use App\Models\Pacientes;
use App\Models\Especificaciones;
use Carbon\Carbon;

class RegistrosController extends Controller
{
    public function index(Request $request)
    {
        try{
            $parametros = $request->all();
            $obj = RegistroPaciente::with("cliente", "especificaciones");
            if($parametros['query']!="")
            {

            }
            $fecha_inicial =Carbon::now();
            $fecha_inicial->day = 1;
            $fecha_inicial->month = $parametros['mes'];
            $fecha_inicial->year = $parametros['anio'];

            $fecha_final = new Carbon($fecha_inicial);
            $fecha_final->day = $fecha_final->daysInMonth;
            $obj = $obj->whereBetween('fecha', [$fecha_inicial->format('Y-m-d'), $fecha_final->format('Y-m-d')]);

            if(isset($parametros['page'])){
                $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
               $obj = $obj->orderBy("num","desc");
                $obj = $obj->paginate($resultadosPorPagina);
            }else
            {
                $obj = $obj->orderBy("num","asc")->get();
            }
            return response()->json($obj,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    public function historial($id, Request $request)
    {
        try{
            $parametros = $request->all();
            $obj = RegistroPaciente::with("especificaciones")
                                    ->orderBy("fecha","desc")
                                    ->orderBy("num","desc")
                                    ->where("paciente_id",$id)->get();
            
            return response()->json($obj,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function store(Request $request)
    {
        $mensajes = [
            
            'required'      => "required",
            'email'         => "email",
            'unique'        => "unique"
        ];
        $inputs = $request->all();
        $inputs = $inputs['params'];
        $reglas = [
            'nombres'                       => 'required',
            /*'od_esfera'                     => 'required',
            'od_cilindro'                   => 'required',
            'od_eje'                        => 'required',
            'oi_esfera'                     => 'required',
            'oi_cilindro'                   => 'required',
            'oi_eje'                        => 'required',
            'add'                           => 'required',
            'ao'                            => 'required',
            'di'                            => 'required',*/
            //'nota'                          => 'required',
            //'armazon'                       => 'required',
            //'otros'                         => 'required',
        ];
        DB::beginTransaction();
        $object = new RegistroPaciente();
        $v = Validator::make($inputs, $reglas, $mensajes);
        if ($v->fails()) {
            return response()->json(['error' => "Hace falta campos obligatorios. ".$v->errors() ], HttpResponse::HTTP_CONFLICT);
        }
        
        try {
            $id_cliente = 0;
            //return Response::json(is_array($inputs['nombres']), HttpResponse::HTTP_CONFLICT);
            if(!is_array($inputs['nombres'])) {
                $cliente = Pacientes::create([
                    'nombres' => $inputs['nombres'],
                    'Estatus' => 0
                ]);
                $id_cliente = $cliente->id;
            }else{
                $id_cliente = $inputs['nombres']['id'];
            }

            /*obtenemos el num segun consulta */
            /*$today_final = Carbon::now();
            $today_inicial = Carbon::now();
            $today_inicial->day = 1;
            $today_final->day = $today_final->daysInMonth;
            $numero = RegistroPaciente::whereBetween('fecha', [$today_inicial->format('Y-m-d'), $today_final->format('Y-m-d')])->get()->count();
            $numero++;*/
            /* Fin consulta */
    
            $object->paciente_id        = $id_cliente;
            $object->num                = $inputs['num'];
            $object->fecha              = Carbon::now()->format('Y-m-d');
            $object->od_esfera          = $inputs['od_esfera'];
            $object->od_cilindro        = $inputs['od_cilindro'];
            $object->od_eje             = $inputs['od_eje'];
            $object->oi_esfera          = $inputs['oi_esfera'];
            $object->oi_cilindro        = $inputs['oi_cilindro'];
            $object->oi_eje             = $inputs['oi_eje'];
            $object->add1               = $inputs['add'];
            $object->ao                 = $inputs['ao'];
            $object->di                 = $inputs['di'];
            $object->nota               = $inputs['nota'];
            $object->armazon            = $inputs['armazon'];
            $object->otros              = $inputs['otros'];
     
            $arreglo_especificaciones = Array();
            if(isset($inputs['especificaciones']))
            {
                foreach ($inputs['especificaciones'] as $key => $value) {
                    $especificacion = Especificaciones::find($value);
                    $data = array(
                        "especificacion_id" => $value,
                        "descripcion" => $especificacion->especificacion
                      );
                      array_push($arreglo_especificaciones, $data);
                }
            }
            
            //return Response::json(['error' => gettype()], HttpResponse::HTTP_CONFLICT);
            
            $object->save();
            $object->especificaciones()->createMany($arreglo_especificaciones);
        
            DB::commit();
            
            return response()->json($object,HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }

    }

    public function update(Request $request, $id)
    {
        $mensajes = [
            
            'required'      => "required",
            'email'         => "email",
            'unique'        => "unique"
        ];
        $inputs = $request->all();
        $inputs = $inputs['params'];
        $reglas = [
            'nombres'                       => 'required',
            /*'od_esfera'                     => 'required',
            'od_cilindro'                   => 'required',
            'od_eje'                        => 'required',
            'oi_esfera'                     => 'required',
            'oi_cilindro'                   => 'required',
            'oi_eje'                        => 'required',
            'add'                           => 'required',
            'ao'                            => 'required',
            'di'                            => 'required',*/
            //'nota'                          => 'required',
            //'armazon'                       => 'required',
            //'otros'                         => 'required',
        ];
        DB::beginTransaction();
        $object = RegistroPaciente::find($id);
        $v = Validator::make($inputs, $reglas, $mensajes);
        if ($v->fails()) {
            return response()->json(['error' => "Hace falta campos obligatorios. ".$v->errors() ], HttpResponse::HTTP_CONFLICT);
        }
        
        try {
            $id_cliente = 0;
            
            if(!is_array($inputs['nombres'])) {
                $cliente = Pacientes::create([
                    'nombres' => $inputs['nombres'],
                    'Estatus' => 0
                ]);
                $id_cliente = $cliente->id;
            }else{
                $id_cliente = $inputs['nombres']['id'];
            }

            /*obtenemos el num segun consulta */
            /*$today_final = Carbon::now();
            $today_inicial = Carbon::now();
            $today_inicial->day = 1;
            $today_final->day = $today_final->daysInMonth;
            $numero = RegistroPaciente::whereBetween('fecha', [$today_inicial->format('Y-m-d'), $today_final->format('Y-m-d')])->get()->count();
            $numero++;*/
            /* Fin consulta */
    
            $object->paciente_id        = $id_cliente;
            $object->num                = $inputs['num'];
            //$object->num                = $numero;
            //$object->fecha              = Carbon::now()->format('Y-m-d');
            $object->od_esfera          = $inputs['od_esfera'];
            $object->od_cilindro        = $inputs['od_cilindro'];
            $object->od_eje             = $inputs['od_eje'];
            $object->oi_esfera          = $inputs['oi_esfera'];
            $object->oi_cilindro        = $inputs['oi_cilindro'];
            $object->oi_eje             = $inputs['oi_eje'];
            $object->add1               = $inputs['add'];
            $object->ao                 = $inputs['ao'];
            $object->di                 = $inputs['di'];
            $object->nota               = $inputs['nota'];
            $object->armazon            = $inputs['armazon'];
            $object->otros              = $inputs['otros'];
     
            $arreglo_especificaciones = Array();
            RegistroEspecificacion::where("registro_paciente_id", $id)->forceDelete();
            foreach ($inputs['especificaciones'] as $key => $value) {
                $especificacion = Especificaciones::find($value);
                $data = array(
                    "especificacion_id" => $value,
                    "descripcion" => $especificacion->especificacion
                  );
                  array_push($arreglo_especificaciones, $data);
            }
            //
            
            $object->save();
            $object->especificaciones()->createMany($arreglo_especificaciones);
        
            DB::commit();
            
            return response()->json($object,HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }

    }
    public function Destroy($id)
    {
        try{
            $obj = RegistroPaciente::find($id);
            $obj->delete();

            return response()->json(['data'=>"Registro Eliminado"], HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function getNum()
    {
        try{
            $mes_actual = new Carbon();
            $obj = RegistroPaciente::whereBetween("created_at", [$mes_actual->format("Y-m-01"), $mes_actual->format("Y-m-").$mes_actual->lastOfMonth()])->count();

            return response()->json(['data'=>$obj], HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

}