<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{
    public function __construct(Marca $marca) {
        $this->marca = $marca;
        // $this->marcaRepository = $marcaRepo;
    }

    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);
        
        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados( $atributos_modelos );
        }else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }


        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro);
        }
         
        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);

        }
        return response()->json($marcaRepository->getResultado(), 200);
       


     //---------------------------------------//


        // $marcas = array();

        // if($request->has('atributos_modelos')){
        //     $atributos_modelos = $request->atributos_modelos;
        //     $marcas = $this->marca->with('modelos:id,'.$atributos_modelos);
        // }else{
        //     $marcas = $this->marca->with('modelos');
        // }


        // if($request->has('filtro')){
        //     $filtros = explode(';', $request->filtro);
            
        //     foreach($filtros as $key => $condicao){
             
        //     $c = explode(':', $condicao);
        //     $marcas = $marcas->where($c[0], $c[1], $c[2]);
        //     }
        //  }

         

        // if($request->has('atributos')){
        //     $atributos = $request->atributos;
        //     $marcas = $marcas->selectRaw( $atributos )->get();

        // }else{
        //     $marcas =  $marcas->get();
        // }
       
 
        // //$marcas = Marca::all();
        // //$marcas = $this->marca->with('modelos')->get();
        // return response()->json($marcas, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404) ;
        } 

        return response()->json($marca, 200);
    }


    public function edit(Marca $marca)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas, $marca->feedback());

        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        
        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }
        
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 200);
    }



    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo
        Storage::disk('public')->delete($marca->imagem);        

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
        
    }
}
