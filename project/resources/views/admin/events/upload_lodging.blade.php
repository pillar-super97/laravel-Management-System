@extends('layouts.app')
@section('pageTitle', 'Import Events Lodging')
@section('content')
    <h3 class="page-title">Import Events Lodging</h3>
    {!! Form::open(['method' => 'POST','id'=>'importlodgingform', 'route' => ['admin.events.lodging'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Upload Event Lodging xls</div>
        
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
                            {!! Form::label('lodgingxls', 'Upload .xls file', ['class' => 'control-label required']) !!}
                            {!! Form::file('lodgingxls', old('lodgingxls'), ['class' => 'form-control required','required']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('lodgingxls'))
                                <p class="error-block">
                                    {{ $errors->first('lodgingxls') }}
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
            var lodgingxls = $("#lodgingxls").val();
            if(lodgingxls=="")
            {
                $("#lodgingxls").addClass('custom_error');
                return false;
                
            }
            $('.validateimportexcel').addClass('disabled');
            $('.cancel-btn').addClass('disabled');
            var postData = new FormData($("#importlodgingform")[0]);
                $.ajax({
                    type:"POST",
                    url:'/admin/events/validateEventLodgingImportExcel',
                    data:postData,
                    cache       : false,
                    processData: false,
                    contentType: false,
                    success:function(res){
                        $(".error-container").html('');
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
                            $("#importlodgingform").trigger("reset");
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