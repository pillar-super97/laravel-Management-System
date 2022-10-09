@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Excused Employee')
@section('content')
    <h3 class="page-title">Employees</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            List of Employees
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped ">
                <thead>
                    <tr>
                       

                        <th>Emp. Number</th>
                        <th>Name</th>
                        
                   </tr>
                </thead>
                
               
            </table>
        </div>
    </div>

@endsection