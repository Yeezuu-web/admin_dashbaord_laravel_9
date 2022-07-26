<?php

namespace App\Http\Requests;

use App\Models\Genre;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreGenreRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('genre_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}