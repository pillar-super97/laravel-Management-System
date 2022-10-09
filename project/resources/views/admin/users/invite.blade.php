@extends('layouts.app')
@section('pageTitle', 'Add User')
@section('content')
<style>
.nav.client-detail>li {
    position: relative;
    display: block;
    padding: 10px 15px;
}
</style>
<h3 class="page-title">Invite Client</h3>

<div class=" row">
    <div class="col-md-12">
        @if ($errors->count() > 0)
        <div class="alert alert-danger">
            <ul class="list-unstyled">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (Session::has('successmsg'))

        <div class="alert alert-success">
            {{ Session::get('successmsg') }}
        </div>

        @endif

    </div>

</div>





<div class="panelpanel-default">

    <div class="panel-body1">


        <div class="row">
            <div class="col-md-5">
                <div class="box box-widget widget-user-2">

                    <div class="widget-user-header bg-primary">


                        <h3 class="no-margin">{{$client->name}}</h3>
                        <h5>{{$client->address}}</h5>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked client-detail">
                            <li>Billing Contact Name<span class="pull-right">{{$client->billing_contact_name}}</span>
                            </li>
                            <li>Billing Contact Email<span class="pull-right">{{$client->billing_contact_email}}</span>
                            </li>
                            <li>Billing Contact Address<span
                                    class="pull-right">{{$client->billing_contact_address}}</span></li>

                            <li>Scheduling Contact Name<span
                                    class="pull-right">{{$client->scheduling_contact_name}}</span></li>
                            <li>Scheduling Contact Email<span
                                    class="pull-right">{{$client->scheduling_contact_email}}</span></li>
                            <li>Scheduling Contact Address<span
                                    class="pull-right">{{$client->scheduling_contact_address}}</span></li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-7">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title">{{$client->name }} Users</h3>
                    </div>

                    <div class="box-body">

                    <div class="table-responsive">

                    @if(count($users))

                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>@lang('global.users.fields.name')</th>
                                <th>@lang('global.users.fields.email')</th>
                                <th>@lang('global.users.fields.role')</th>
                                <th>Status</th>
                                <th></th>

                            </tr>

                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->role as $singleRole)
                                        <span class="label label-info label-many">{{ $singleRole->title }}</span>
                                    @endforeach
                                </td>
                                @if($user->status == 'inactive' && !$user->password)
                                <td>
                                

                                {!! Form::open(['method' => 'POST', 'route' => ['admin.users.invite_client',$client->id],'autocomplete'=>'off']) !!}

                                {!! Form::hidden('user_id', $user->id) !!}

                                <div class="btn-group">
<button type="button" class="btn btn-default btn-xs" disabled style="margin-right: 10px !important;">Pending</button>
<button type="submit" name="resend_invitation" class="btn btn-warning btn-xs" value="resend">Resend Invitation</button>
</div>

                              

                                {!! Form::close() !!}
                            
                            </td>
                                @else
                                <td><span class="label label-primary">{{ $user->status }}</span></td>
                                @endif

                                <td>@can('user_edit')
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}" class="btn btn-xs btn-info" title="Edit User"><i class="fa fa-edit"></i></a>
                                    @endcan</td>
                                

                            </tr>
                            @endforeach




                        </table>

                    @else
                    <h4 class="box-title">No User for {{$client->name }}</h4>

                    @endif
                       

                        <br>
                        
                        <h4 class="box-title">Invite a User</h4>
                        {!! Form::open(['method' => 'POST', 'route' => ['admin.users.invite_client',$client->id],'autocomplete'=>'off']) !!}

                        <div class="col-xs-6  form-group">
                            {!! Form::label('email', 'Email', ['class' => 'control-label required']) !!}
                            {!! Form::email('email', old('email'), ['class' =>
                            'form-control','autocomplete'=>'off', 'placeholder'
                            => '', 'required' => 'required']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('email'))
                            <p class="help-block">
                                {{ $errors->first('email') }}
                            </p>
                            @endif
                        </div>

                        <div class="col-xs-6 form-group">
                            {!! Form::label('role', 'Role', ['class' => 'control-label required']) !!}
                            {!! Form::select('role[]', $roles, (old('role') ?? 10) , ['class' => 'form-control select2
                            role_dropdown',
                            'multiple' => 'multiple', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('role'))
                            <p class="help-block">
                                {{ $errors->first('role') }}
                            </p>
                            @endif
                        </div>

                        <div class="col-xs-12 form-group">
                            {!! Form::submit('Send Invitation', ['class' => 'btn btn-primary pull-right']) !!}
                        </div>

                        {!! Form::close() !!}


                    </div>


                            </div>

                </div>
            </div>


            



        </div>
    </div>


    @stop

    @section('javascript')
    <script type="text/javascript">
    $(document).ready(function() {


    })
    </script>
    @stop