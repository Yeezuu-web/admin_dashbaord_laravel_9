@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.genre.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.genres.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.genre.fields.id') }}
                        </th>
                        <td>
                            {{ $genre->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.genre.fields.title') }}
                        </th>
                        <td>
                            {{ $genre->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.genre.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Genre::STATUS_RADIO[$genre->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.genres.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection