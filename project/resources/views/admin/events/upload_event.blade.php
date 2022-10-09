@extends('layouts.app')
@section('pageTitle', 'Import Events')
@section('content')
    <h3 class="page-title">Import Events</h3>
    {!! Form::open(['method' => 'POST','id'=>'importeventform', 'route' => ['admin.events.import'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Upload Event xls</div>
        
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
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-9">
                    <div class="row">
                        <div class="col-xs-3" style="height:75px;">
                            {!! Form::label('eventxls', 'Upload .xls file', ['class' => 'control-label required']) !!}
                            {!! Form::file('eventxls', old('eventxls'), ['class' => 'form-control required','required']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('eventxls'))
                                <p class="error-block">
                                    {{ $errors->first('eventxls') }}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            
            
        </div>
    </div>
    {!! Form::button('Validate Excel', ['class' => 'btn btn-success validateimportexcel'],['tabindex'=>15]) !!}
    {!! Form::submit('Upload', ['class' => 'btn btn-success submitimportevent','disabled'],['tabindex'=>15]) !!}
    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click','.validateimportexcel',function(){
            var eventxls = $("#eventxls").val();
            if(eventxls=="")
            {
                $("#eventxls").addClass('custom_error');
                return false;
                
            }
            $('.validateimportexcel').addClass('disabled');
            $('.cancel-btn').addClass('disabled');
            var postData = new FormData($("#importeventform")[0]);
                $.ajax({
                    type:"POST",
                    url:'/admin/events/validateEventImportExcel',
                    data:postData,
                    cache       : false,
                    processData: false,
                    contentType: false,
                    success:function(res){
                        if(res.status=="Success")
                        {
                            $('.validateimportexcel').attr('disabled',true);
                            $('.submitimportevent').attr('disabled',false);
                            $('.cancel-btn').removeClass('disabled');
                        }else{
                            var html='<div class="note note-danger"><ul class="list-unstyled">';
                            $.each( res.errors, function( key, value ) {
                                html+="<li>"+value+"</li>";
                            });
                            html+='</ul></div>';
                            $(".error-container").html(html);
                            $('.cancel-btn').removeClass('disabled');
                            $("#importeventform").trigger("reset");
                            $('.validateimportexcel').attr('disabled',false);
                            $('.validateimportexcel').removeClass('disabled');
//                            setTimeout(function(){
//                                $('.error-container').html('').hide();
//                            }, 15000);
                        }
                    }
                });
           
        });
    })
   
</script>
@stop