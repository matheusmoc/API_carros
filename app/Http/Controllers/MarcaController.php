<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class MarcaController extends Controller
{

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    public function index()
    {
        // $marca = Marca::all();
        $marca = $this->marca->all();
        return response()->json($marca, 201);
    }



    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        // $marca = Marca::create($request->all());

        $image = $request->file('imagem');
        $image_urn = $image->store('imagens', 'public');
        // dd($image_urn);

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem'=> $image_urn
        ]);

        return response()->json($marca, 201);
    }


    public function show($id)
    {
        $marca = $this->marca->find($id);
        if (!$marca) {
            return response()->json(['erro' => 'Recurso não econtrado'], 404);
        }
        return response()->json($marca, 200);
    }


    public function update(Request $request, $id)
    {
        // print_r($request->all()); //dados atualizados
        // echo '<hr>';
        // print_r($marca->getAttributes()); //dados antigos

        $marca = $this->marca->find($id);

        $request->validate($marca->rules(), $marca->feedback());

        if (!$marca) {
            return response()->json(['erro' => 'Recurso não existe, impossível atualizar'], 404);
        }


        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
        } //remove o arquivo antigo caso outro seja requisitado
        
        $image = $request->file('imagem');
        $image_urn = $image->store('imagens', 'public'); //salva o novo


        $marca->update([
                'nome' => $request->nome,
                'imagem'=> $image_urn
        ]);

        return response()->json($marca, 200);
    }



    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if (!$marca) {
            return response()->json(['erro' => 'Recurso não existe, impossível excluir'], 404);
        }
        
        Storage::disk('public')->delete($marca->imagem);
        $marca->delete();
        return response()->json(['msg' => 'Marca removida com sucesso!'], 200);
    }
}
