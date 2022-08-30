<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{

    
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->modelo, 201);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());


        $image = $request->file('imagem');
        $image_urn = $image->store('imagens/modelos', 'public');


        $modelo = $this->modelo->create([
            'marca_id'=>$request->marca_id,
            'nome' => $request->nome,
            'imagem'=> $image_urn,
            'numero_portas' =>$request->numero_portas,
            'lugares' =>$request->lugares,
            'air_bags' =>$request->air_bags,
            'abs'=>$request->abs,
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->find($id);
        if (!$modelo) {
            return response()->json(['erro' => 'Recurso não econtrado'], 404);
        }
        return response()->json($modelo, 200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $modelo = $this->modelo->find($id);

        $request->validate($modelo->rules());

        if (!$modelo) {
            return response()->json(['erro' => 'Recurso não existe, impossível atualizar'], 404);
        }


        if($request->method() === 'PATCH'){
            foreach($modelo->rules() as $input => $regra ){

                if(array_key_exists($input, $request->all())){
                    $regraDinamicas[$input] = $regra;
                }
            }

            $request->validate($regraDinamicas);
        }else{
            $request->validate($modelo->rules());
        }

        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        } 
        
        $image = $request->file('imagem');
        $image_urn = $image->store('imagens/modelos', 'public'); //salva o novo


        $modelo->update([
            'marca_id'=>$request->marca_id,
            'nome' => $request->nome,
            'imagem'=> $image_urn,
            'numero_portas' =>$request->numero_portas,
            'lugares' =>$request->lugares,
            'air_bags' =>$request->air_bags,
            'abs'=>$request->abs,
        ]);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if (!$modelo) {
            return response()->json(['erro' => 'Recurso não existe, impossível excluir'], 404);
        }
        
        Storage::disk('public')->delete($modelo->imagem);
        
        $modelo->delete();
        return response()->json(['msg' => 'Modelo removido com sucesso!'], 200);
    }
}
