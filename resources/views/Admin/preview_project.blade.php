@extends('layouts.admin-app')
<link rel="stylesheet" type="text/css" href="{{ asset('public/project/css/web_view.css') }}">
@section('content')
    @if($response = session('response'))
    <div class="alert @if($response['status']) alert-success @else alert-danger @endif">
        {{ $response['message'] }}
    </div>
    @endif
<div class="row">
    <div class="col-10">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0 text-dark">{{$manage_listings->title}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col mt-3 text-dark">
        <a href="{{ route('view-unit', ['id' => $manage_listings['id'],'userid'=>$encrypt_userid]) }}" style="font-size: 12px;" class="btn btn-success" target="_blank">Visit List Page</a>
        @if($permission)
            @if($permission->update)
                @if(Request::is('preview-unit*'))
                    <a href="{{ route('edit-unit', ['id' => $manage_listings['id']]) }}" class="btn btn-success" style="font-size: 12px;" target="_blank">Edit Page</a>
                @endif
                @if(Request::is('manage-unit-status*'))
                    <a href="{{ route('ready-edit-unit', ['id' => $manage_listings['id']]) }}" class="btn btn-success" style="font-size: 12px;" target="_blank">Edit Page</a>
                @endif
                @if(Request::is('manage-outdated-unit*'))
                    <a href="{{ route('outdated-edit-unit', ['id' => $manage_listings['id']]) }}" class="btn btn-success" style="font-size: 12px;" target="_blank">Edit Page</a>
                @endif
            @endif
        @endif
    </div>
    <!--<div class="col-2 mt-3 text-dark">-->
    <!--</div>-->
</div>
<section class="content">
    <div class="row">
        <div class="col-lg-8 col-md-7 col-12">
            @if ($manage_listings->description)
            <div class="card">
                <div class="card-header header">
                    <p class="desc m-0">Description</p>
                </div>
                <div class="card-body">
                    @if ($manage_listings->description)
                    {!! $manage_listings->description !!}
                    @else
                    -
                    @endif
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header header">
                    <p class="contact desc m-0">Details</p>
                </div>
                <div class="card-body "style="overflow-x:auto;">
                    <table class="table table-striped table-bordered">
                          <tr>
                            <td>RERA NO:</td>
                            <td>
                                @if ($manage_listings->rera_permit_no)
                                    {{ $manage_listings->rera_permit_no }}
                                @else
                                -
                                @endif
                            </td>
                          </tr>
                          <tr>
                            <td>Estimated Completion Date</td>
                                <td>@if ($manage_listings->quarter && $manage_listings->handover_year)
                                    {{ $manage_listings->quarter}},{{$manage_listings->handover_year}}
                                @else
                                -
                                @endif
                            </td>
                          </tr>
                          <tr>
                            <td>Location</td>
                            <td>
                                @if (!empty($subcommunity[0]->name))
                                    {{$subcommunity[0]->name}},
                                @endif
                                @if (!empty($community[0]->name))
                                    {{$community[0]->name}},
                                @endif
                                Dubai, UAE
                            </td>
                          </tr>
                          <tr>
                            <td>Construction Status By RERA</td>
                            <td>
                                @if ($manage_listings->construction_status)
                                    {{ $manage_listings->construction_status }}%
                                @else
                                -
                                @endif

                                @if ($manage_listings->construction_date)
                                    - {{ date('d-M-Y', strtotime($manage_listings->construction_date))  }}
                                @else
                                -
                                @endif
                            </td>
                          </tr>
                      </table>
                </div>
            </div>

            @if ($manage_listings->pre_handover_amount || $manage_listings->handover_amount || $manage_listings->post_handover)
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="amount desc m-0">Amount to be paid</p>
                </div>
                <div class="card-body "style="overflow-x:auto;">
                    <table class="table table-striped table-bordered">
                          <tr>
                            <td>Pre Handover Amount</td>
                            <td>
                                @if ($manage_listings->pre_handover_amount)
                                    AED {{number_format(($manage_listings->pre_handover_amount),2, '.', ',') }}
                                @else
                                -
                                @endif
                            </td>
                          </tr>
                          <tr>
                            <td>Handover Amount</td>
                            <td>
                                @if ($manage_listings->handover_amount)
                                    AED {{number_format(($manage_listings->handover_amount),2, '.', ',') }}
                                @else
                                -
                                @endif
                            </td>
                          </tr>
                          <tr>
                            <td>Post Handover Amount</td>
                            <td>
                                @if ($manage_listings->post_handover)
                                    {{-- AED {{ $manage_listings->post_handover }}    --}}
                                    AED {{number_format(($manage_listings->post_handover),2, '.', ',') }}
                                @else
                                -
                                @endif
                            </td>
                          </tr>
                      </table>
                </div>
            </div>
            @endif

            @if ( !($manage_listings->paymentplan->isEmpty() ))
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="property desc m-0">Payment Details</p>
                </div>
                <div class="card-body" style="overflow-x:auto;">
                    @foreach ($manage_listings['paymentplan'] as $key=>$payment)
                        {{ $payment->percentage }}%
                        On
                        @if($payment->installment_terms != 0)
                            {{ $payment->installment_terms }}
                        @endif
                        {{ $payment->milestone }}
                        @if($payment->milestone == "Handover")
                            {{ $manage_listings->quarter}} {{$manage_listings->handover_year}}
                        @endif
                        <br>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($manage_listings->image)
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="contact desc m-0">Images</p>
                </div>
                <div class="card-body ">
                    @if ($manage_listings->image)
                        @foreach (json_decode($manage_listings->image) as $item)
                        <a download="property_image.jpg" href="{{asset('public/files/profile/'.$item)}}">
                        <img src="{{asset('public/files/profile/'.$item)}}" style="height:120px; width:100px; object-fit:cover;"  /></a>
                        @endforeach
                    @else

                    @endif
                </div>
            </div>
            @endif

            @if (!$note->isEmpty())
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="note desc m-0">Note</p>
                </div>
                <div class="card-body "style="overflow-x:auto;">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>ADDED BY</td>
                            <td>DATE ADDED</td>
                            <td>NOTE</td>
                            <td>CONTROLS</td>
                        </tr>
                        @foreach($note as $value)
                        <tr>
                            <td>
                                {{ $manage_listings['developer']['company'] }}
                            </td>
                            <td>
                                {{$value->updated_at}}
                            </td>
                            <td>
                                {{$value->note}}
                            </td>
                            <td>
                                <a href='javascript:void(0)' class='note-delete' onclick='removeNote(this)' data-id="{{$value->id}}"><i style='color: red' class='fas fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            @endif

            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Attachment File</h3>
                    <button class="btn btn-info add-row" type="button" style="float: right;"><i class="fas fa-plus-square"></i></i></button>
                </div>
                <form class="form-horizontal" id="frmTarget" enctype="multipart/form-data">
                    <div class="card-body abc">
                        @csrf
                        <input type="hidden" name="project_id" value="{{$manage_listings['id']}}">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <input type="text" name="attachment[0][attachment_name]" class="form-control mb-2"
                                    placeholder="Enter The File Name">
                            </div>
                            <div class="col-sm-6">
                                <input type="file" name="attachment[0][attachment_multiple]" id="files"
                                    accept="image/*, video/*, .pdf , .doc, .docx, application/vnd.ms-excel, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-10">
                                <div id="image_preview" class="row">
                                    @if($unitmultipleattachment)
                                    @foreach($unitmultipleattachment as $key=>$image)
                                    <div class='upload-image text-center mb-2' title="{{$image->attachment_name}}" id ="{{$image->id}}" >
                                        <a href="{{asset('public/project_attachment/'.json_decode($image->attachment_multiple))}}" data_id="{{$key}}" target="_blank">
                                            {{$image->attachment_name}}
                                        </a>
                                        <i style='color: red' onclick="projectemoveattachment(this,{{$image->id}})" class="fas fa-trash-alt delete mt-2" data-toggle="tooltip" data-placement="bottom" title="Delete"></i>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="submit" id="submit-all" class="btn btn-primary mr-1 mb-1" style="float: right;">Attach</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4 col-md-5 col-12">
            <div class="card mt-3 mt-md-0">
                <div class="card-header header">
                    <p class="contact desc m-0">Contact</p>
                </div>
                <div class="card-body">
                    <div class="media">
                        <img class="mr-3 user" style="height: 60px" src="https://static.thenounproject.com/png/17241-200.png" >
                        <div class="media-body">
                            <h6 class="mt-0">{{ $manage_listings['developer']['company'] }}</h6>
                            <p class="m-0">{{ $manage_listings['user']['name'] }}</p>
                            <p class="m-0">{{ $manage_listings['user']['phone'] }}</p>
                            <p class="m-0">{{ $manage_listings['user']['email'] }}</p>
                        </div>
                      </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="property desc m-0">Property Details</p>
                </div>
                <div class="card-body px-0">
                    <table class="table table-striped">
                        <tr>
                          <td>Bedrooms</td>
                          <td>
                            @if ($manage_listings->bedrooms)
                                {{ $manage_listings->bedrooms }}
                            @else
                            -
                            @endif
                        </td>
                        </tr>
                        <tr>
                            <td>Full Bathrooms</td>
                            <td>
                            @if ($manage_listings->bathrooms)
                            {{ $manage_listings->bathrooms }}
                            @else
                            -
                            @endif
                        </td>
                        </tr>
                        <tr>
                            <td>Half Bathrooms</td>
                            <td>
                                @if ($manage_listings->bathrooms)
                                    {{ $manage_listings->bathrooms }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Size</td>
                            <td>
                                @if ($manage_listings->size)
                                    {{ $manage_listings->size }} Sq.Ft
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($manage_listings->price)
            <div class="card mt-3">
                <div class="card-header header">
                   <p class="price desc">Sales Price</p>
                </div>
                <div class="card-body px-0">
                    <table class="table table-striped">
                        <tr>
                          <td>Price Sq.ft</td>
                          <td>AED
                              @if ($manage_listings->price)
                                {{ number_format(($manage_listings->price/$manage_listings->size),2, '.', ',') }}
                              @else

                              @endif
                            </td>
                        </tr>
                        <tr>
                          <td>Price</td>
                          <td>AED
                            @if ($manage_listings->price)
                                {{number_format(($manage_listings->price),2, '.', ',') }}
                            @else

                            @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            @if ($manage_listings->video)
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="contact desc m-0">Video</p>
                </div>
                <div class="card-body ">
                    @if ($manage_listings->video)
                        @foreach (json_decode($manage_listings->video) as $items)
                            <video style="height:180px; width:190px" controls>
                             <source src="{{asset('public/files/profile/'.$items)}}" type="video/mp4">
                            </video>
                        @endforeach
                    @else

                    @endif
                </div>
            </div>
            @endif

            @if ($manage_listings->pdf)
            <div class="card mt-3">
                <div class="card-header header">
                     <p class="property desc m-0">Attachments</p>
                </div>
                <div class="card-body ">
                    @if ($manage_listings->pdf)
                    @foreach (json_decode($manage_listings->pdf) as $item)
                        <a href="{{asset('public/files/profile/'.$item)}}" style="height:120px; width:100px" target="_blank" download>
                                @php ($pdf = explode('-', $item))@endphp
                                {{$pdf[1]}}
                        </a><br>
                    @endforeach
                    @else

                    @endif
                </div>
            </div>
            @endif

             @if($manage_listings->reminder()->exists())

                <div class="card mt-3">
                    <div class="card-header header">
                         <p class="property desc ">Reminder</p>
                    </div>
                    <div class="card-body ">
                        <caption >Reminder/FollowUp</caption>
                            <table class="table table-striped">
                                <thead>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Owner</th>
                                    <th>Done?</th>
                                </thead>
                                <tbody>
                                @foreach($manage_listings->reminder as $key=>$reminder)
                                    <tr>
                                        <td>
                                            {{$reminder->comment}}
                                        </td>
                                        <td>
                                            {{date('d-M-Y',strtotime($reminder->reminder_date))}}
                                        </td>
                                        <td>
                                            {{$reminder->user->name}}
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch{{$key}}" data-id="{{$reminder->id}}" onclick="fn_project_reminder_status_changes(this)" value="1" {{($reminder->status)?'checked':''}}>
                                            <label class="custom-control-label" for="customSwitch{{$key}}">?</label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <caption>Reminder History</caption>
                            <table class="table table-striped">

                                <tbody>
                                @foreach($manage_listings->reminder as $reminder)
                                    @if($reminder->reminder_date <= \Carbon\Carbon::now()->toDateString() && $reminder->status == 0)
                                        <?php
                                            $class="red";
                                        ?>
                                        <tr style="color:{{$class}};">
                                        <td colspan="2">AddedOn
                                            @if ($reminder->created)
                                                {{\Carbon\Carbon::parse($reminder->created)->format('d-M-Y g:i A')}}
                                                @if($reminder->reminder_date < \Carbon\Carbon::now()->toDateString() )
                                                <button class="" style="background-color: #8B2323;color:white; border-top-right-radius: initial;">Date Time gone</button>
                                                @endif
                                            @else
                                             -
                                            @endif
                                        </td>
                                        </tr>
                                        <tr>
                                            <td>Reminder Title:</td>
                                            <td>@if ($reminder->title)
                                                    {{$reminder->title}}
                                                @else
                                                 -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reminder Date:</td>
                                            <td>@if ($reminder->reminder_date)
                                                    {{date('d-M-Y',strtotime($reminder->reminder_date))}}
                                                @else
                                                 -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Comment:</td>
                                            <td>@if ($reminder->comment)
                                                    {{$reminder->comment}}
                                                @else
                                                 -
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- </table> -->
                                    @endif
                                @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>

            @endif
        </div>
    </div>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
    var index = 0;
    $(".add-row").click(function () {
        index++;
        var add =
            "<div class='form-group row'><div class='col-sm-6'><input type='text' name='attachment["+ index +"][attachment_name]' class='form-control mb-2' placeholder='Enter The File Name'></div><input type='hidden' name='id' value=''><div class='col-sm-6'> <input type='file' name='attachment["+ index +"][attachment_multiple]' id='files' accept='image/*, .pdf , .doc, .docx, application/vnd.ms-excel, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'/></div></div>"
        $(".abc").append(add);
    });
</script>
<script type="text/javascript">
    function fn_project_reminder_status_changes(e){
        var id =  $(e).data("id");
        var value= $(e).is(':checked');
        $.ajax({
            url: "{{route('add_reminder_status')}}",
            method: "GET",
            data: { 'id': id,'value':value},
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            cache: false,
            beforeSend:function(){
                 return confirm("Are you sure?");
            },
            success: function (data) {
             if(data.status==1){
                swal("Done!", data.message, "success");
                var segments = location.pathname.split('/');
                var chksegments = segments[2];
                if(chksegments == "manage-unit-status"){
                    var editurl = '{{ route("ready-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else if(chksegments == "manage-outdated-unit"){
                    var editurl = '{{ route("outdated-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else if(chksegments == "manage-soldout-unit"){
                    var editurl = '{{ route("sold-out-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else{
                    var editurl = '{{ route("preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[3]);
                    window.location.href = editurl;
                }
            } else {
               swal("ERROR!", data.message, "error");
            }

            },
        error: function (data){
              swal("ERROR!", data, "error");
            }
        });
    }

    function removeNote(e)
    {
        var id =  $(e).data("id");
        var segments = location.pathname.split('/');
        $.ajax({
            url: '{{route('remove-note')}}',
            method: "GET",
            data: { 'id': id},
            headers: {'X-Requested-With': 'XMLHttpRequest'},

            cache: false,
            beforeSend:function(){
                 return confirm("Are you sure?");
            },
            success: function (data) {
            if(data.status==1){
              swal("Done!", data.message, "success");
                var segments = location.pathname.split('/');
                var chksegments = segments[2];
                if(chksegments == "manage-unit-status"){
                    var editurl = '{{ route("ready-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else if(chksegments == "manage-outdated-unit"){
                    var editurl = '{{ route("outdated-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else if(chksegments == "manage-soldout-unit"){
                    var editurl = '{{ route("sold-out-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else{
                    var editurl = '{{ route("preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[3]);
                    window.location.href = editurl;
                }
            } else {
               swal("ERROR!", data.message, "error");
            }

            },
        error: function (data){
              swal("ERROR!", data, "error");
            }
        });
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#submit-all').click(function (event) {
            event.preventDefault()
            var myform = document.getElementById("frmTarget");
            var formData = new FormData(myform);
            console.log(formData);
            var segments = location.pathname.split('/');
            formData:$(this).serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            window.swal({
            title: "Checking...",
            text: "Please wait",
            imageUrl: "{{ asset('public/ajaxloader/ajaxloader.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
            });
            $.ajax({
                type: "POST",
                url: "{{route('unit-attachment-post')}}",
                data: formData,
                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");
                        var segments = location.pathname.split('/');
                        var chksegments = segments[2];
                        if(chksegments == "manage-unit-status"){
                            var editurl = '{{ route("ready-preview-unit", ":id") }}';
                            editurl = editurl.replace(':id', segments[4]);
                            window.location.href = editurl;
                        } else if(chksegments == "manage-outdated-unit"){
                            var editurl = '{{ route("outdated-preview-unit", ":id") }}';
                            editurl = editurl.replace(':id', segments[4]);
                            window.location.href = editurl;
                        } else if(chksegments == "manage-soldout-unit"){
                            var editurl = '{{ route("sold-out-preview-unit", ":id") }}';
                            editurl = editurl.replace(':id', segments[4]);
                            window.location.href = editurl;
                        } else{
                            var editurl = '{{ route("preview-unit", ":id") }}';
                            editurl = editurl.replace(':id', segments[3]);
                            window.location.href = editurl;
                        }
                    } else {
                        swal("ERROR!",'<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                    }
                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                    console.log(data);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    });

    function projectemoveattachment(value,id)
    {
        var segments = location.pathname.split('/');
        $.ajax({
            url: '{{route('unit-removeattachment')}}',
            method: "GET",
            data: { 'id': id},
            headers: {'X-Requested-With': 'XMLHttpRequest'},

            cache: false,
            beforeSend:function(){
                 return confirm("Are you sure?");
            },
            success: function (data) {
            if(data.status==1){
                swal("Done!", data.message, "success");
                var segments = location.pathname.split('/');
                console.log(segments);
                var chksegments = segments[2];
                if(chksegments == "manage-unit-status"){
                    var editurl = '{{ route("ready-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else if(chksegments == "manage-outdated-unit"){
                    var editurl = '{{ route("outdated-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else if(chksegments == "manage-soldout-unit"){
                    var editurl = '{{ route("sold-out-preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[4]);
                    window.location.href = editurl;
                } else{
                    var editurl = '{{ route("preview-unit", ":id") }}';
                    editurl = editurl.replace(':id', segments[3]);
                    window.location.href = editurl;
                }
            } else {
               swal("ERROR!", data.message, "error");
            }

            },
            error: function (data){
              swal("ERROR!", data, "error");
            }
        });
    }
</script>
@endsection
