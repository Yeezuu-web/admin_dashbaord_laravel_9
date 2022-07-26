<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\Admin\GenreResource;
use App\Models\Genre;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenresApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('genre_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GenreResource(Genre::all());
    }

    public function store(StoreGenreRequest $request)
    {
        $genre = Genre::create($request->all());

        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Genre $genre)
    {
        abort_if(Gate::denies('genre_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GenreResource($genre);
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->all());

        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Genre $genre)
    {
        abort_if(Gate::denies('genre_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genre->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}