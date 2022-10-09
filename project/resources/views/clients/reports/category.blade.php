@extends('clients.reports.layout', ['title'=> 'MSI Inventory Service Corporation'])

@section('content')
<p class="text-center m-0">Category Report</p>
<p class="text-center text-sm m-1">[Report 110]</p>



<table class="my-3 sub-heading col-12 p-1">
    <tr>

        <td class=" align-top">
            {{ $event->store->name }}<br />
            {{ $event->store->address }}
        </td>
        <td class="text-center align-top sub-heading"></td>
        <td class="text-right">
            @if(@$event->schedule_employees[0]->employee->name)
            MSI Manager : {{ @$event->schedule_employees[0]->employee->name}} <br />
            @endif
            Store Manager : {{$event->store->manager_id}}
        </td>
    </tr>
</table>

<p class="text-center">
    Final
</p>

<table class="table records" id="myTable">
    <thead>
        <tr>
            <th>Category</th>
            <th> Salesfloor</th>
            <th> Stockroom</th>

            <th>{{date('m/d/y', strtotime($event->date))}} Current</th>

            <th>Prior</th>
            <th>Variance Amount</th>

        </tr>
    </thead>
    <tbody>
        @php

        $sales_floor_total = $stock_room_total = $current_total = $prior_total = 0.0;

        @endphp

        @foreach($result as $cr)

        <tr>

            <td>
                <div style="font-weight: bold; font-size:12px"><?= $cr['category_name'];?></div>
                <div style="padding-top: 15px;">
                    <?=  $cr['pieces'].'&nbsp; &nbsp; &nbsp; '. $cr['category_name'];?>
</div>
            </td>


            <td class="left" style="padding-top: 45px;">

                <?php
                                    if(empty($cr['Salesfloor']) || $cr['Salesfloor'] == 'null'){
                                        echo '0.00';
                                        $cr['Salesfloor'] = 1;
                                    }else{
                                        echo $cr['Salesfloor'];
                                    } 
                                    ?>
            </td>

            <td class="left" style="padding-top: 45px;">

                <?php
                                    if(empty($cr['Stockroom']) || $cr['Stockroom'] == 'null'){
                                        echo '0.00';
                                        $cr['Stockroom'] = 1;
                                    }else{
                                        echo $cr['Stockroom'];
                                    } 
                                    ?>
            </td>


            <td class="left" style="padding-top: 45px;">

                <?php
                                    if(empty($cr['current_price']) || $cr['current_price'] == 'null'){
                                        echo '0.00';
                                        $cr['current_price'] = 1;
                                    }else{
                                        echo $cr['current_price'];
                                    } 
                                    ?>
            </td>

            <td class="right" style="padding-top: 45px;">

                <?php
                                    if(empty($cr['prior_price']) || $cr['prior_price'] == 'null'){
                                        echo '0.00';
                                        $cr['prior_price'] = 1;
                                    }else{
                                        echo $cr['prior_price'];
                                    } 
                                    ?>

            </td>


            <td class="center" style="padding-top: 45px;">

                <?= ($cr['current_price'] - $cr['prior_price']); ?>
            </td>

        </tr>

        <tr>

            <td class="left strong">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
                &nbsp;&nbsp;<?=  $cr['category_name']; ?></td>

            <td class="left">

                <?php
                                    if(empty($cr['Salesfloor']) || $cr['Salesfloor'] == 'null'){
                                        echo '0.00';
                                        $cr['Salesfloor'] = 1;
                                    }else{
                                        echo $cr['Salesfloor'];
                                    } 
                                    ?>
            </td>



            <td class="left">
                <?php
                                    if(empty($cr['Stockroom']) || $cr['Stockroom'] == 'null'){
                                        echo '0.00';
                                        $cr['Stockroom'] = 1;
                                    }else{
                                        echo $cr['Stockroom'];
                                    } 
                                    ?>
            </td>




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


            <td class="center">

                <?= ($cr['current_price'] - $cr['prior_price']); ?></td>


        </tr>

        @php
            $sales_floor_total += $cr['Salesfloor'];
            $stock_room_total += $cr['Stockroom'];
            $current_total += $cr['current_price'];
            $prior_total += $cr['prior_price'];
        @endphp

        @endforeach



        <tr>
                <th class="border-top">Report Total</th>

                <th class="border-top" id="Salesfloor">{{$sales_floor_total}}</th>

                <th class="border-top" id="Stockroom">{{$stock_room_total}}</th>
                <th class="border-top" id="current_total">{{$current_total}}</th>
                <th class="border-top">{{$prior_total}}</th>
                <th class="border-top">{{$current_total - $prior_total}}</th>

            </tr>



    </tbody>
</table>





</div>
</div>

@endsection