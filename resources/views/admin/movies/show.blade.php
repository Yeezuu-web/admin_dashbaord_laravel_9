@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.movie.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.movies.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.id') }}
                        </th>
                        <td>
                            {{ $movie->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.title') }}
                        </th>
                        <td>
                            {{ $movie->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.hint_code') }}
                        </th>
                        <td>
                            {{ $movie->hint_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.synopsis') }}
                        </th>
                        <td>
                            {!! $movie->synopsis !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.thumnail') }}
                        </th>
                        <td>
                            @if($movie->thumnail)
                                <a href="{{ $movie->thumnail->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $movie->thumnail->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.images') }}
                        </th>
                        <td>
                            @foreach($movie->images as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.genres') }}
                        </th>
                        <td>
                            @foreach($movie->genres as $key => $genres)
                                <span class="label label-info">{{ $genres->title }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.movies.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection