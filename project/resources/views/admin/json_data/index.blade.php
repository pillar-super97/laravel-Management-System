@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Uploaded JSON Files List')
@section('content')
    <h3 class="page-title">JSON Files</h3>
    @can('upload_json_data')
    <p>
        <a href="{{ route('admin.json_data.create') }}" class="btn btn-success">Upload Json/Zip file</a>
        
    </p>
    @endcan
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All JSON Files
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif





            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Filename</th>
                        <th>Uploaded At</th>
                        <th>Data read At</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($json_files) > 0)
                        @foreach ($json_files as $json_file)
                            <tr data-entry-id="{{ $json_file->id }}">
                                <td>{{ @$json_file->id }}</td>
                                <td>{{ @$json_file->filename }}</td>
                                <td>{{ \Carbon\Carbon::parse($json_file->created_at)->format('m/d/Y \a\t h:i:s a') }}</td>
                                <td>
                                    @if($json_file->is_file_read)
                                    {{ \Carbon\Carbon::parse($json_file->updated_at)->format('m/d/Y \a\t h:i:s a') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="border-bottom: 1px solid;">
                                    @if(!$json_file->is_file_read)
                                    <form style="display: inline-block;" 
                                    action="{{route('admin.json_data.read',['jsonId' => base64_encode($json_file->id)])}}"
                                    
                                    >
                                    <button 
                                        onclick="if(confirm('Are you sure to read data?')){this.setAttribute('disabled', true); this.form.submit();}else{return false;}"
                                        title="Read Data"
                                        type="submit"
                                        class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button>
                                    </form>
                                    @endif
                                    @can('json_data_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.json_data.json_del', $json_file->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Json File','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop