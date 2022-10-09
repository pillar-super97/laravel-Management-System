@extends('layouts.app')
@section('pageTitle', 'Dashboard')
@section('content')
<style>
    .nav.client-detail>li {
    position: relative;
    display: block;
    padding: 10px 15px;
}
</style>
    <div class="row">
        <div class="col-md-12 m-b-30"> 
                                <h1 class="dashboard_h1">Dashboard</h1>
        </div>

        <div class="col-md-12">
            <ol class="breadcrumb dashboard_breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li class="active">Dashboard</li>
            </ol>
        </div>
    </div>
        
        <div class="row">
            <?php if (Gate::allows('isAdmin') || Gate::allows('isCorporate') || Gate::allows('isOperations') || Gate::allows('isOperationsManager')) {?>
            
                <div class="listingBox-col">
                <div class="listingBox2"> 
                    <div class="listingBox-title">
                            <div class="listingBox-textInMiddle">
                                    <h5><a href="{{ route('admin.clients.index') }}">Clients</a></h5>
                                    <a href="{{ route('admin.clients.index') }}" class="moreBtn">more <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                            </div>
                    </div> 

                    <div class="listingBox-icon">
                            <div class="listingBox2-num">{{$clients}}</div>
                            <img src="{{asset('uploads/images/dashboard_icons/bodyBoxs_icons/2.png')}}" class="listingBox2-img" alt="">
                    </div>
                </div>
            </div>
            <?php }?>


            <?php if (Gate::allows('isAdmin') || Gate::allows('isCorporate') || Gate::allows('isOperations') || Gate::allows('isOperationsManager')) {?>
                <div class="listingBox-col">
                    <div class="listingBox1"> 
                            <div class="listingBox-title">
                                    <div class="listingBox-textInMiddle">
                                            <h5><a href="{{ route('admin.stores.index') }}">Stores</a></h5>
                                            <a href="{{ route('admin.stores.index') }}" class="moreBtn">more <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                    </div>
                            </div> 

                            <div class="listingBox-icon">
                                    <div class="listingBox1-num">{{$stores}}</div>
                                    <img src="{{asset('uploads/images/dashboard_icons/bodyBoxs_icons/4.png')}}" class="listingBox4-img" alt="">
                            </div>
                    </div>
                </div>
            <?php }?>

            <?php if (Gate::allows('isAdmin') || Gate::allows('isCorporate') || Gate::allows('isOperations') || Gate::allows('isOperationsManager') || Gate::allows('isArea')) {?>
                <div class="listingBox-col">
                    <div class="listingBox3"> 
                        <div class="listingBox-title">
                            <div class="listingBox-textInMiddle">
                                    <h5><a href="#">Employees</a></h5>
                                    Number of Employees - <?=$empcount;?><br>
                                    Avg Benchmark - <?=$avg_benchmark;?>%<br>
                                    Supervisors - <?=$supervisor;?><br>
                                    Drivers - <?=$driver;?><br>
                                    RX - <?=$rx;?>
                            </div>
                        </div> 
                        <div class="listingBox-icon">
                                <img src="{{asset('uploads/images/dashboard_icons/bodyBoxs_icons/3.png')}}" class="listingBox3-img" alt="">
                        </div>
                    </div>
                </div>
            <?php }?>

            <?php if (Gate::allows('isAdmin') || Gate::allows('isCorporate') || Gate::allows('isOperations') || Gate::allows('isOperationsManager') || Gate::allows('isArea') || Gate::allows('isTeam')) {?>
            <div class="listingBox-col">
                <div class="listingBox2"> 
                    <div class="listingBox-title">
                        <div class="listingBox-textInMiddle">
                            <h5>Performance</h5>
                            <?php if (Gate::allows('isTeam')){
                                echo 'Area 30 day Avg - '.$thirtydayaverage.'<br>';
                                echo 'Your 7 Day Avg - '.$teamsevendayaverage.'<br>';
                                echo 'Your 30 Day Avg - '.$teamthirtydayaverage.'<br>';
                                echo 'Positive Experiences - '.$teampositivepercent.'%';
                            }else{?>
                            7 Day Average - <?=$sevendayaverage?><br>
                            30 Day Average - <?=$thirtydayaverage?><br>
                            Positive Experiences - <?=$positivepercent?>%
                            <?php }?>
                        </div>
                    </div> 
                    <div class="listingBox-icon">
                        <img src="{{asset('uploads/images/dashboard_icons/bodyBoxs_icons/2.png')}}" class="listingBox2-img" alt="">
                    </div>
                </div>
            </div>
            <?php }?>

            <?php if (Gate::allows('isArea') || Gate::allows('isTeam')) {?>
            <div class="listingBox-col">
                <div class="listingBox2"> 
                    <div class="listingBox-title">
                        <div class="listingBox-textInMiddle">
                            <h5><a href="{{ route('admin.clients.index') }}">Events</a></h5>
                            Next Week - <?=$nextweek_events?><br>
                            Next 30 Days - <?=$nextthirtydaysevents?>
                        </div>
                    </div> 
                    <div class="listingBox-icon">
                        <img src="{{asset('uploads/images/dashboard_icons/bodyBoxs_icons/2.png')}}" class="listingBox2-img" alt="">
                    </div>
                </div>
            </div>
            <?php }?>
    </div>

      
    
    @if(Gate::allows('isClient'))

    <div class="col-md-4">
                <div class="box box-widget widget-user-2">

                    <div class="widget-user-header bg-primary">


                        <h3 class="no-margin">{{auth()->user()->client->name}}</h3>
                        <h5>{{auth()->user()->client->address}}</h5>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked client-detail">
                        <li><b>Billing Contact:</b></li>
                        <li>Name<span class="pull-right">{{auth()->user()->client->billing_contact_name}}</span>
                            </li>
                            <li>Email<span class="pull-right">{{auth()->user()->client->billing_contact_email}}</span>
                            </li>
                            <li>Address<span
                                    class="pull-right">{{auth()->user()->client->billing_contact_address}}</span></li>

                        </ul>
                    </div>
                </div>
            </div>



    <div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4> Stores</h4>

        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($reportstores) > 0 ? 'datatable' : '' }}">
                <thead>
                    <tr>
                        <th>Store</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                @if (count($reportstores) > 0)

                    @foreach ($reportstores as $rstore)
                    <tr data-entry-id="{{ $rstore->store_id }}">

                    <td>{{ $rstore->store_name }}</td>
                    <td>{{ $rstore->state_name }}</td>
                    <td>{{ $rstore->city_name }}</td> 
                    <td><a href="{{ url('admin/events?store='.$rstore->store_id) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a></td>
                    </tr>
                    @endforeach
        
                @endif


                </tbody>
            </table>
        </div>
    </div>

            </div>

    @endif
    
@endsection
