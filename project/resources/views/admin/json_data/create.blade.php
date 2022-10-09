@extends('layouts.app')
@section('pageTitle', 'Json Data Uploader')
@section('content')
    <h3 class="page-title">Upload JSON</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.json_data.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Upload an Event JSON</div>
        <div class="error-container">
            @if (Session::has('message'))
                <div class="note note-info">
                    <p>{{ Session::get('message') }}</p>
                </div>
            @endif
            @if ($errors->count() > 0)
                <div class="note note-danger">
                    <ul class="list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            @if(Session::has('error_message'))
            <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('error_message') }}</p>
            @endif


        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('data', 'Json File', ['class' => 'control-label required']) !!}
                    {!! Form::file('data', null, ['class' => 'form-control','required' => true]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="error-block">
                            {{ $errors->first('data') }}
                        </p>
                    @endif
                </div>
                
            </div>
            
        </div>
    </div>

    {!! Form::submit('Upload', ['class' => 'btn btn-success']) !!}
    <a href="{{route('admin.json_data.index')}}" class="btn btn-warning cancel-btn">Cancel</a>
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            
        });
    </script>
@stop