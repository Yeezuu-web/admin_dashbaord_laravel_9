<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyGenreRequest;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class GenresController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('genre_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Genre::query()->select(sprintf('%s.*', (new Genre())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'genre_show';
                $editGate = 'genre_edit';
                $deleteGate = 'genre_delete';
                $crudRoutePart = 'genres';

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
            $table->editColumn('status', function ($row) {
                return $row->status ? Genre::STATUS_RADIO[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.genres.index');
    }

    public function create()
    {
        abort_if(Gate::denies('genre_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        $genre = Genre::create($request->all());

        return redirect()->route('admin.genres.index');
    }

    public function edit(Genre $genre)
    {
        abort_if(Gate::denies('genre_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->all());

        return redirect()->route('admin.genres.index');
    }

    public function show(Genre $genre)
    {
        abort_if(Gate::denies('genre_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.genres.show', compact('genre'));
    }

    public function destroy(Genre $genre)
    {
        abort_if(Gate::denies('genre_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genre->delete();

        return back();
    }

    public function massDestroy(MassDestroyGenreRequest $request)
    {
        Genre::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}