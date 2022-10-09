@extends('layouts.app')
@section('pageTitle', 'Add Event')
@section('content')
    <h3 class="page-title">Event Timesheet File</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.events.uploadmdb'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Upload Event mdb</div>
        
        <div class="error-container">
            @if ($errors->count() > 0)
                <div class="note note-danger">
                    <ul class="list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
        </div>
        <input type="hidden" name="event_id" value="{{$event_id}}">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-9">
                    <div class="row">
                        <div class="col-xs-3" style="height:75px;">
                            {!! Form::label('inventmdb', 'Upload .mdb file', ['class' => 'control-label required']) !!}
                            {!! Form::file('inventmdb', old('inventmdb'), ['class' => 'form-control required','required']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('inventmdb'))
                                <p class="error-block">
                                    {{ $errors->first('inventmdb') }}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            
            
        </div>
    </div>
    {!! Form::submit('Upload', ['class' => 'btn btn-success'],['tabindex'=>15]) !!}
    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
<script type="text/javascript">
    $(document).ready(function(){
        
   })
   
</script>
@stop