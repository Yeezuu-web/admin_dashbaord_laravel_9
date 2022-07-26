<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyMovieRequest;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Genre;
use App\Models\Movie;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MoviesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('movie_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Movie::with(['genres'])->select(sprintf('%s.*', (new Movie())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'movie_show';
                $editGate = 'movie_edit';
                $deleteGate = 'movie_delete';
                $crudRoutePart = 'movies';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('hint_code', function ($row) {
                return $row->hint_code ? $row->hint_code : '';
            });
            $table->editColumn('thumnail', function ($row) {
                if ($photo = $row->thumnail) {
                    return sprintf(
        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
        $photo->url,
        $photo->thumbnail
    );
                }

                return '';
            });
            $table->editColumn('images', function ($row) {
                if (!$row->images) {
                    return '';
                }
                $links = [];
                foreach ($row->images as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' . $media->getUrl('thumb') . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });
            $table->editColumn('genres', function ($row) {
                $labels = [];
                foreach ($row->genres as $genre) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $genre->title);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'thumnail', 'images', 'genres']);

            return $table->make(true);
        }

        $genres = Genre::get();

        return view('admin.movies.index', compact('genres'));
    }

    public function create()
    {
        abort_if(Gate::denies('movie_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = Genre::pluck('title', 'id');

        return view('admin.movies.create', compact('genres'));
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

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $movie->id]);
        }

        return redirect()->route('admin.movies.index');
    }

    public function edit(Movie $movie)
    {
        abort_if(Gate::denies('movie_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = Genre::pluck('title', 'id');

        $movie->load('genres');

        return view('admin.movies.edit', compact('genres', 'movie'));
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

        return redirect()->route('admin.movies.index');
    }

    public function show(Movie $movie)
    {
        abort_if(Gate::denies('movie_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movie->load('genres');

        return view('admin.movies.show', compact('movie'));
    }

    public function destroy(Movie $movie)
    {
        abort_if(Gate::denies('movie_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movie->delete();

        return back();
    }

    public function massDestroy(MassDestroyMovieRequest $request)
    {
        Movie::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('movie_create') && Gate::denies('movie_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Movie();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}