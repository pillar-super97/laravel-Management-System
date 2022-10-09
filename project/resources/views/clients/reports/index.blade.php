@extends('layouts.app')
@section('pageTitle', 'Reports')
@section('content')

<style>
.col-xs-2 {
    text-align: center;
}

.col-half-offset {
    margin-left: 4.166666667%;
}

.iconclass i {
    font-size: 60px;
    text-align: center;
}

.col-xs-2 a {
    font-size: 16px;
}
</style>
<div class="row">
    <div class="col-md-12 m-b-30">
        <h1 class="dashboard_h1">Reports</h1>
    </div>

    <div class="col-md-12">
        <ol class="breadcrumb dashboard_breadcrumb">
            <li><a href="/">Home</a></li>
            <li class="active">Reports</li>
        </ol>
    </div>
</div>

<div class="col-sm-12">







    <div class="panel panel-default">

        <div class="container">
            <div class="row">

                <div class="panel-heading">
                    <h1>Under Progress</h1>
                </div>


                @if(0)
                <div class="panel-body">



                    <div class="col-xs-2 iconclass" id="p1">

                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i><br />


                        <a target="_blank" href="{{ url('area_report',[$event->id]) }}"> Area Report </a>


                    </div>

                    <div class="col-xs-2 col-half-offset iconclass" id="p2">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i><br />
                        <a target="_blank" href="{{ url('category_report', [$event->id]) }}">Category Report</a>
                    </div>


                    <div class="col-xs-2 col-half-offset iconclass" id="p2">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i><br />
                        <a target="_blank" href="{{ url('location_report', [$event->id])}}">Location Report</a>
                    </div>

                    <div class="col-xs-2 col-half-offset iconclass" id="p2">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i><br />
                        <a target="_blank" href="{{ url('consolidation_report', [$event->id]) }}">Location
                            Consolidation</a>
                    </div>

                    @if(0)
                    <div class="col-xs-2 col-half-offset iconclass" id="p2">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i><br />
                        <a target="_blank" href="{{ url('timesheet_report', [$event->id]) }}">Timesheet Report</a>
                    </div>
                    @endif

                </div>
                @endif
            </div>
        </div>


    </div>













    @endsection