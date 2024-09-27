<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB;

use App\Models\Pacientes;

class PacientesController extends Controller
{
    public function index(Request $request)
    {
        try{
            $parametros = $request->all();
            $obj = Pacientes::whereNull("deleted_at");

            if(isset($parametros['query'])){
                $obj = $obj->where(function($query)use($parametros){
                    return $query->where('concat(nombre," ",apellido_paterno," ",apellido_materno)','LIKE','%'.$parametros['query'].'%');
                });
            }
            
            if(isset($parametros['page'])){
                $obj = $obj->orderBy('nombre');
                $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
                $obj = $obj->paginate($resultadosPorPagina);
            }
            return response()->json(['data'=>$obj],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function show($id)
    {
        try{
            $obj = Pacientes::/*with("responsable")->*/find($id);
            
            $obj = $obj->first();

            return response()->json(['data'=>$obj],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $inputs = $request->all();
            $object = Pacientes::find($id);
            $object->nombre              = $inputs['nombres']." ".$inputs['apellido_paterno']." ".$inputs['apellido_materno'];
            $object->update();
    
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
            $obj = Pacientes::find($id);
            $obj->delete();

            return response()->json(['data'=>"Paciente Eliminado"], HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
}
