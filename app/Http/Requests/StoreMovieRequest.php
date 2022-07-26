<?php

namespace App\Http\Requests;

use App\Models\Movie;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMovieRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('movie_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'hint_code' => [
                'string',
                'required',
            ],
            'synopsis' => [
                'required',
            ],
            'thumnail' => [
                'required',
            ],
            'images' => [
                'array',
            ],
            'genres.*' => [
                'integer',
            ],
            'genres' => [
                'array',
            ],
        ];
    }
}