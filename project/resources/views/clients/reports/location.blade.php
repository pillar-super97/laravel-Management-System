@extends('clients.reports.layout', ['title'=> 'MSI Inventory Service Corporation'])

@section('content')



<table class="my-3 sub-heading col-12 p-1">
    <tr>

        <td class=" align-top">
            {{ $event->store->name }}<br />
            {{ $event->store->address }}
        </td>
        <td class="text-center align-top sub-heading">Loaction Report <br> Final</td>
        <td class="text-right">
            @if(@$event->schedule_employees[0]->employee->name)
            MSI Manager : {{ @$event->schedule_employees[0]->employee->name}} <br />
            @endif
            Store Manager : {{$event->store->manager_id}}
        </td>
    </tr>
</table>


<table class="table table-striped" id="myTable" style="font-size:10px;">
    <thead>
        <tr>
            <th>Location</th>
            <th>Jan 2 2022 Current</th>
            <th>Jan 2 2021 Prior</th>
            <th>Variance Amount</th>
            <th>Variance %</th>
        </tr>
    </thead>
    <tbody>
    @php
        $current_price_total = 0.0;
        $prior_price_total = 0.0;
    @endphp

        @foreach($result as $cr)

        <tr>
            <td class="left strong"><?=  $cr['location'];?> <br /> <?= $cr['sublocation']; ?></td>
            
            <td class="left">
                <?php
                                    if(empty($cr['current_price']) || $cr['current_price'] == 'null'){
                                        echo '0.00';
                                        $cr['current_price'] = 1;
                                    }else{
                                        echo $cr['current_price'];
                                    } 
                                    ?>
            </td>

            <td class="right">

                <?php
                                    if(empty($cr['prior_price']) || $cr['prior_price'] == 'null'){
                                        echo '0.00';
                                        $cr['prior_price'] = 1;
                                    }else{
                                        echo $cr['prior_price'];
                                    } 
                                    ?>

            </td>


            <td class="center"><?= ($cr['current_price'] - $cr['prior_price']); ?></td>
            <td class="right"><?= round(($cr['current_price'] / $cr['prior_price'])*100,2); ?></td>

        </tr>

        @php
            $current_price_total += $cr['current_price'];
            $prior_price_total += $cr['prior_price'];
        @endphp

       @endforeach

       

       <tr>
            <th class="border-top">Report Total</th>


            <th class="border-top">{{ sprintf('%0.2f',$current_price_total)}}</th>

            <th class="border-top">{{ sprintf('%0.2f',$prior_price_total)}}</th>
            <th class="border-top">{{ sprintf('%0.2f', $current_price_total - $prior_price_total)}}</th>
            <th class="border-top">
                {{
                                round(( ($current_price_total - $prior_price_total) / ($prior_price_total>0 ? $prior_price_total : 1) )*100 ,2) 
                                }}
            </th>
        </tr>



    </tbody>
</table>
@endsection