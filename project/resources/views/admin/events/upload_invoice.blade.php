@extends('layouts.app')
@section('pageTitle', 'Import Events Invoice')
@section('content')
    <h3 class="page-title">Import Invoice</h3>
    {!! Form::open(['method' => 'POST','id'=>'importinvoiceform', 'route' => ['admin.events.invoice'], 'files' => true,]) !!}

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
                            {!! Form::label('invoicexls', 'Upload .xls file', ['class' => 'control-label required']) !!}
                            {!! Form::file('invoicexls', old('invoicexls'), ['class' => 'form-control required','required']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('invoicexls'))
                                <p class="error-block">
                                    {{ $errors->first('invoicexls') }}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            
            
        </div>
    </div>

    <div class="row override hidden">
                        <div class="col-xs-6">
    
    {{ Form::checkbox('override', true, false,  ['class' => 'override-rows']) }}
    {!! Form::label('override', 'Override all rows') !!}
</div>
</div>
<div class="row">
                        <div class="col-xs-6">
    {!! Form::button('Validate Excel', ['class' => 'btn btn-success validateimportexcel'],['tabindex'=>15]) !!}
    {!! Form::submit('Upload', ['class' => 'btn btn-success submitimportevent','disabled'],['tabindex'=>15]) !!}
    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn']) !!}
    {!! Form::close() !!}
    </div>
</div>
@stop

@section('javascript')
    @parent
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click','.validateimportexcel',function(){
            var invoicexls = $("#invoicexls").val();
            if(invoicexls=="")
            {
                $("#invoicexls").addClass('custom_error');
                return false;
                
            }
            $('.validateimportexcel').addClass('disabled');
            $('.cancel-btn').addClass('disabled');
            var postData = new FormData($("#importinvoiceform")[0]);
                $.ajax({
                    type:"POST",
                    url:'/admin/events/validateEventInvoiceImportExcel',
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
                            $(".error-container").html('');
                            $(".error-container").html(html);
                            $('.cancel-btn').removeClass('disabled');
                            if(res.warning && !res.error) $('.override').removeClass('hidden');
                            //$("#importinvoiceform").trigger("reset");
                            $('.validateimportexcel').attr('disabled',false);
                            $('.validateimportexcel').removeClass('disabled');
                        }
                    }
                });
           
        });


    });

    $('.override-rows').click(function() {
        if ($(this).is(':checked')) {
            $('.submitimportevent').attr('disabled',false);
        }
        else $('.submitimportevent').attr('disabled',true);
    });
   
</script>
@stop