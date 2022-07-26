<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Resources\Admin\MovieResource;
use App\Models\Movie;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MoviesApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('movie_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MovieResource(Movie::with(['genres'])->get());
    }

    public function store(StoreMovieRequest $request)
    {
        $movie = Movie::create($request->all());
        $movie->genres()->sync($request->input('genres', []));
        if ($request->input('thumnail', false)) {
            $movie->addMedia(storage_path('tmp/uploads/' . basename($request->input('thumnail'))))->toMediaCollection('thumnail');
        }

        foreach ($request->input('images', []) as $file) {
            $movie->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('images');
        }

        return (new MovieResource($movie))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Movie $movie)
    {
        abort_if(Gate::denies('movie_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MovieResource($movie->load(['genres']));
    }

    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $movie->update($request->all());
        $movie->genres()->sync($request->input('genres', []));
        if ($request->input('thumnail', false)) {
            if (!$movie->thumnail || $request->input('thumnail') !== $movie->thumnail->file_name) {
                if ($movie->thumnail) {
                    $movie->thumnail->delete();
                }
                $movie->addMedia(storage_path('tmp/uploads/' . basename($request->input('thumnail'))))->toMediaCollection('thumnail');
            }
        } elseif ($movie->thumnail) {
            $movie->thumnail->delete();
        }

        if (count($movie->images) > 0) {
            foreach ($movie->images as $media) {
                if (!in_array($media->file_name, $request->input('images', []))) {
                    $media->delete();
                }
            }
        }
        $media = $movie->images->pluck('file_name')->toArray();
        foreach ($request->input('images', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $movie->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('images');
            }
        }

        return (new MovieResource($movie))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Movie $movie)
    {
        abort_if(Gate::denies('movie_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movie->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}