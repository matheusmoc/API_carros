<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];


    public function rules(){
        return[
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpg,jpeg,gif,webp'
        ];
    }


    public function feedback(){
        return[
            'required' => 'O campo :attribute é obrigatório',
            'image.mimes' => 'Acesso negado, somente imagens podem ser submetidas.',
            'nome.unique' => 'O :attribute já existe',
            'nome.min' => 'O :attribute deve ter no mínimo 3 caracteres',
        ];
    }
}
