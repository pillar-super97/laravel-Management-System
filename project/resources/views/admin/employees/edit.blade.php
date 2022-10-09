@extends('layouts.app')
@section('pageTitle', 'Edit Employee')
@section('content')
    <h3 class="page-title">Employees</h3>
    
    {!! Form::model($employee, ['method' => 'PUT', 'route' => ['admin.employees.update', $employee->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Employee</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                    <label for="manager_id" class="control-label">Employee Availability</label>
                    <br>
                     <?php $availability_days = array();if(count($employee->availability_days)){foreach($employee->availability_days as $day)$availability_days[] = $day->days;}?>
                    <input <?php if(in_array('Monday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Monday" name="employee_availability[]" id="Monday"><label for="Monday">&nbsp;&nbsp;Monday&nbsp;&nbsp;</label>
                    <input <?php if(in_array('Tuesday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Tuesday" name="employee_availability[]" id="Tuesday"><label for="Tuesday">&nbsp;&nbsp;Tuesday&nbsp;&nbsp;</label>
                    <input <?php if(in_array('Wednesday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Wednesday" name="employee_availability[]" id="Wednesday"><label for="Wednesday">&nbsp;&nbsp;Wednesday&nbsp;&nbsp;</label>
                    <input <?php if(in_array('Thursday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Thursday" name="employee_availability[]" id="Thursday"><label for="Thursday">&nbsp;&nbsp;Thursday&nbsp;&nbsp;</label>
                    <input <?php if(in_array('Friday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Friday" name="employee_availability[]" id="Friday"><label for="Friday">&nbsp;&nbsp;Friday&nbsp;&nbsp;</label>
                    <input <?php if(in_array('Saturday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Saturday" name="employee_availability[]" id="Saturday"><label for="Saturday">&nbsp;&nbsp;Saturday&nbsp;&nbsp;</label>
                    <input <?php if(in_array('Sunday',$availability_days)){echo 'checked="checked"';}?> type="checkbox" value="Sunday" name="employee_availability[]" id="Sunday"><label for="Sunday">&nbsp;&nbsp;Sunday&nbsp;&nbsp;</label>
                </div>
                <div class="col-xs-3 form-group">
                    <input <?php if($employee->overnight){echo 'checked="checked"';}?> type="checkbox" value="1" name="overnight" id="overnight"><label for="overnight">&nbsp;&nbsp;Overnight&nbsp;&nbsp;</label>
                </div>
            </div>
        </div>
    </div>
    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
   <script type="text/javascript">
    $(document).ready(function(){
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker({
            autoclose: true,
            format:'mm-dd-yyyy'
        });
            }

        });
        $('.datepicker').datepicker({
            autoclose: true,
            format:'mm-dd-yyyy'
        })
        $('.blackout_dates_container').on('click', '.remove_blackout', function(events){
            var blackout_counter = $(this).attr('blackout');
            $('.blackout_counter'+blackout_counter).remove();
        });
        $('.add_more').click(function(){
            var blackout_counter = $('#blackout_counter').val();
            blackout_counter++;
            var html ='<div style="height:75px;" class="col-xs-2 blackout_counter'+blackout_counter+'"><label for="blackout_dates" class="control-label">Blackout Dates</label>\n\
                        <input class="form-control datepicker" name="blackout_dates[]" type="text" autocomplete="off"></div><div style="height:75px;" blackout="'+blackout_counter+'" class="col-xs-1 remove_blackout blackout_counter'+blackout_counter
                        +'"><i class="fa fa-trash" aria-hidden="true"></i></div>';
            $('#blackout_counter').val(blackout_counter);
            $(".blackout_dates_container").append(html);
           // alert('sdf');
        })
        $('[data-mask]').inputmask();
        $('.timepicker').timepicker({
            showInputs: false
        })
    
    $('.state_dropdown').on('change',function(){
    var stateID = $(this).val();
    var ele = $(this).attr('dropdown');   
    if(stateID){
        $.ajax({
           type:"GET",
           url:"{{url('get-city-list')}}?state_id="+stateID,
           success:function(res){               
            if(res){
                $("#"+ele+"_city").empty();
                $.each(res,function(key,value){
                    $("#"+ele+"_city").append('<option value="'+key+'">'+value+'</option>');
                });
           
            }else{
               $("#"+ele+"_city").empty();
            }
           }
        });
    }else{
        $("#"+ele+"_city").empty();
    }
        
   });})
</script>
@stop