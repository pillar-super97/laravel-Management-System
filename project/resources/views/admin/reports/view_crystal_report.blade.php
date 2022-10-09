@extends('layouts.app')
@section('pageTitle', 'Edit Report')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">Report Details</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    <iframe width="100%" height="800px" src="https://reports.msi-inv.com/Report.aspx?token=<?=$token?>&user=<?=Auth::id()?>&filename=<?=$report->rpt_file_name?>"></iframe>
                </div>
            </div>
        </div>
    </div>
   <a href="{{ route('admin.reports.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop