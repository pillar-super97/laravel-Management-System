@extends('layouts.app')
@section('pageTitle', 'Add User')
@section('content')
<h3 class="page-title">Select a Client</h3>


{!! Form::open(['method' => 'POST', 'route' => ['admin.users.invite.select_client'],'autocomplete'=>'off']) !!}



<div class="panel panel-default col-md-8">

    <div class="panel-body">


        <div class="row">

            <div class="form-group">
            <div class="col-md-10">
                {!! Form::select('client', $clients, old('client'), ['class' => 'form-control select2', 'required' =>
                '']) !!}
                </div>
                <div class="col-md-2">
                {!! Form::submit('Next', ['class' => 'btn btn-primary pull-right']) !!}
                </div>
            </div>
                
                
            </div>
                
            
                
        
          
        

        
{!! Form::close() !!}



    </div>
</div>


@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

   
})
</script>
@stop