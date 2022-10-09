@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Import')
@section('content')
<h3 class="page-title">Import</h3>





@if (Session::has('successmsg'))

<div class="col-md-12 alert alert-success">
    {{ Session::get('successmsg') }}
</div>

@endif

@if (Session::has('errormsg'))

<div class="col-md-12 alert alert-danger">
    {{ Session::get('errormsg') }}
</div>

@endif


<div class="row">
    
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail"> 
            <div class="caption">
                <h3>Import JSON</h3>
                <p>Import JSON data (categories, gap_query, gap_summery) from Sly Server.</p>
                <a class="btn pull-right" href="/admin/log/json_import/">View Log</a>
                <form action="" method="POST">
                @csrf

                <input type="submit" name="sly_import" class="btn btn-success" value="Import From SLY Server" />

            </form>
            
            </div>
        </div>
    </div>
    
</div>

@stop