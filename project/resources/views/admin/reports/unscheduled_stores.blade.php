@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Store List')
@section('content')
    <h3 class="page-title">Unscheduled Stores</h3>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            Filter Stores
        </div>

        <div class="panel-body">
            <div class="row">
                
                <div class="col-xs-3 form-group">
                    <label for="month" class="control-label">Month</label>
                    <select id="month" class="form-control month select2" name="month" >
                        <option value="">Select Month</option>
                        <option value="01" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="01"){echo 'selected="selected"';} ?>>January</option>
                        <option value="02" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="02"){echo 'selected="selected"';} ?>>February</option>
                        <option value="03" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="03"){echo 'selected="selected"';} ?>>March</option>
                        <option value="04" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="04"){echo 'selected="selected"';} ?>>April</option>
                        <option value="05" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="05"){echo 'selected="selected"';} ?>>May</option>
                        <option value="06" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="06"){echo 'selected="selected"';} ?>>June</option>
                        <option value="07" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="07"){echo 'selected="selected"';} ?>>July</option>
                        <option value="08" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="08"){echo 'selected="selected"';} ?>>August</option>
                        <option value="09" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="09"){echo 'selected="selected"';} ?>>September</option>
                        <option value="10" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="10"){echo 'selected="selected"';} ?>>October</option>
                        <option value="11" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="11"){echo 'selected="selected"';} ?>>November</option>
                        <option value="12" <?php if(($request->session()->get('month')) && $request->session()->get('month')=="12"){echo 'selected="selected"';} ?>>December</option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="year" class="control-label">Year</label>
                    <select id="year" class="form-control year select2" name="year" >
                        <option value="">Select Year</option>
                        <?php 
                            for($i=2020;$i<=2030;$i++)
                            {
                                echo '<option value="'.$i.'"';
                                if(($request->session()->get('year')) && $request->session()->get('year')==$i){echo 'selected="selected"';}
                                echo '>'.$i.'</option>';
                            }
                        ?>
                    </select>
                </div>
                
                
            </div>
            
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Unscheduled Stores
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table id="store_list" class="table table-bordered table-striped datatable row">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Store Name</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Inventory Level</th>
                        <th>APR</th>
                        <th>Last Count Date</th>
                        <th>Contact Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Days available to schedule</th>
                        <th>Months available to schedule</th>
                    </tr>
                </thead>
                

            </table>
        </div>
    </div>
@stop

@section('javascript') 
    
    <script type="text/javascript">
    $(document).ready(function(){
        var dataTable = $('#store_list').DataTable({
            "aLengthMenu": [[25, 50,100,-1], [25,50,100,"All"]],
            "iDisplayLength": 25,
            'destroy': true,
            oLanguage: {
                sProcessing: "<img src='../uploads/images/ajax-loader.gif'>"
            },
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'sortable':false,
            'searching': false, // Remove default Search Control
            'ajax': {
             'url':'/admin/reports/unscheduled_store_list',
             'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
             'data': function(data){
                // Read values
                var month = $('#month').val();
                var year = $('#year').val();
                data.month = month;
                data.year = year;
             }
          },
          'columns': [
             { data: 'id'},
             { data: 'store'},
             { data: 'city'}, 
             { data: 'state'},
             { data: 'inventory_level'},
             { data: 'apr'},
             { data: 'last_count_date'},
             { data: 'scheduling_contact_name'},
             { data: 'scheduling_contact_email'},
             { data: 'scheduling_contact_phone'},
             { data: 'daysavailabletoschedule'},
             { data: 'monthavailabletoschedule'},
          ],
          'columnDefs': [ {
               'targets': [6,10,11], // column index (start from 0)
               'orderable': false, // set orderable false for selected columns
            }],
          'buttons': [
            'excel','print'
            ],
            'dom': 'Blfrtip',
        });
        $('#month').change(function(){
          dataTable.draw();
        });
        $('#year').change(function(){
          dataTable.draw();
        });
        
        
    })
    </script>
@endsection