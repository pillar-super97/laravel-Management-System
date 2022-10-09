@extends('layouts.app')
@section('pageTitle', 'View User')
@section('content')
    <h3 class="page-title">@lang('global.users.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.users.fields.name')</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.users.fields.email')</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.users.fields.role')</th>
                            <td>
                                @foreach ($user->role as $singleRole)
                                    <span class="label label-info label-many">{{ $singleRole->title }}</span>
                                @endforeach
                            </td>
                        </tr>
                        
                        <tr >
                            <th style="display: <?php if(in_array(4,$user->role->pluck('id')->toArray())){echo 'block';}else{ echo 'none';}?>">Area</th>
                            <td style="display: <?php if(in_array(4,$user->role->pluck('id')->toArray())){echo 'block';}else{ echo 'none';}?>">
                                @foreach ($user->area as $singleRole)
                                    <span class="label label-info label-many">{{ $singleRole->title }}</span>
                                @endforeach
                            </td>
                        </tr>
                        
                        <tr>
                            <th>Employee Name</th>
                            <td>{{ @$user->employee->name }}</td>
                        </tr>
                        
                        
                    </table>
                </div>
            </div><!-- Nav tabs -->

            <a href="{{ route('admin.users.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop