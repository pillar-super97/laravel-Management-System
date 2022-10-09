@extends('layouts.app')
@section('pageTitle', $title)
@section('content')
<h3 class="page-title text-capitalize"> {{str_replace('_',' ',$title)}}
</h3>

<div class="panel panel-default">
    <div class="panel-heading"> &nbsp;

        <a href="?clear=true" class="pull-right">Clear Log</a>
    </div>

    <div class="panel-body">
        <pre style="min-height:30rem;">{{$content}}</pre>

    </div>
</div>




@endsection