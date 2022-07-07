<table class="table" bordered>
    <thead>
      <tr>
        <th scope="col" style="width: 5px; word-wrap: break-word;">Sl No</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Property Type</th>
        <th scope="col" style="width: 10px; word-wrap: break-word;">Beds</th>
        <th scope="col" style="width: 30px; word-wrap: break-word;">Location</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Size( Sq Ft)</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Price( AED)</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Developer</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Project Name</th>
        <th scope="col" style="width: 10px; word-wrap: break-word;">Handover</th>
        <th scope="col" style="width: 30px; word-wrap: break-word;">Payment Plan</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Pre-Handover Amount(AED)</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Handover Amount(AED)</th>
        <th scope="col" style="width: 15px; word-wrap: break-word;">Post Handover(AED)</th>
        <th scope="col" style="width: 30px">Project Link </th>
      </tr>
    </thead>
    <tbody>
        @foreach ($project_data as $key=>$item)
            <tr>
                <th style="word-wrap: break-word;">
                    {{$item->id}}
                </th>
                <th style="word-wrap: break-word;">
                    @if($item->property)
                        {{$item->property}}
                    @else
                        -
                    @endif
                </th>
                <th>
                    @if($item->bedrooms)
                        {{$item->bedrooms}}
                    @else
                        -
                    @endif
                </th>
                <th style="word-wrap: break-word;">
                    @if($item->location)
                        {{$item->location}}
                    @else
                        -
                    @endif
                </th>
                <th>
                    @if($item->size)
                        {{$item->size}}
                    @else
                        -
                    @endif
                </th>
                <th>
                    @if($item->price)
                        {{$item->price}}
                    @else
                        -
                    @endif
                </th>
                <th style="word-wrap: break-word;">
                    @if($item->developer->company)
                        {{$item->developer->company}}
                    @else
                        -
                    @endif
                </th>
                <th style="word-wrap: break-word;">
                    @if($item->project)
                        {{$item->project}}
                    @else
                        -
                    @endif
                </th>
                <th>
                    @if($item->quarter && $item->handover_year)
                        {{$item->quarter}}, {{$item->handover_year}}
                    @else
                        -
                    @endif
                </th>
                <th style="word-wrap: break-word;">
                @if (!($item->paymentplan->isEmpty() ))
                    @foreach ($item['paymentplan'] as $payment)
                        {{ $payment->percentage }}%
                        On
                        {{ $payment->installment_terms }}
                        {{ $payment->milestone }}
                        @if($payment->milestone == "Handover")
                            {{ $item->quarter}}, {{$item->handover_year}}
                        @endif
                        <br>
                    @endforeach
                @else
                    -
                @endif
                </th>
                <th>
                    @if($item->pre_handover_amount)
                        {{$item->pre_handover_amount}}
                    @else
                        -
                    @endif
                </th>
                <th>
                    @if($item->handover_amount)
                        {{$item->handover_amount}}
                    @else
                        -
                    @endif
                </th>
                <th>
                    @if($item->post_handover)
                        {{$item->post_handover}}
                    @else
                        -
                    @endif
                </th>
                <th style="word-wrap: break-word;">
                    {{URL::route('view-unit',[$item->id,$user_id])}}
                </th>
            </tr>
        @endforeach
    </tbody>
  </table>
