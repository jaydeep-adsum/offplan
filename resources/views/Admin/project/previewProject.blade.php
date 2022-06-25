@extends('layouts.admin-app')
<link rel="stylesheet" type="text/css" href="{{ asset('public/project/css/web_view.css') }}">
@section('content')
@if($response = session('response'))
<div class="alert @if($response['status']) alert-success @else alert-danger @endif">
    {{ $response['message'] }}
</div>
@endif
<section class="content">
    <div class="row">
        <div class="col-10">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h1 class="m-0 text-dark">@if (!empty($subcommunity[0]->name))
                                {{$subcommunity[0]->name}},
                                @endif
                                @if (!empty($community[0]->name))
                                {{$community[0]->name}}
                                @endif</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mt-3 text-dark">
            @if (Auth::user()->role == 1)     
                @if(Request::segment(2) == "previewProject")
                <a href="{{ route('editProject', ['id' => $manage_project['id']]) }}" class="btn btn-success"
                    style="font-size: 12px;" target="_blank">Edit Page</a>
                @endif
                @if(Request::segment(2) == "previewReadyProject")
                <a href="{{ route('editReadyProject', ['id' => $manage_project['id']]) }}" class="btn btn-success"
                    style="font-size: 12px;" target="_blank">Edit Page</a>
                @endif
                @if(Request::segment(2) == "previewSoldOutProject")
                <a href="{{ route('editSoldOutProject', ['id' => $manage_project['id']]) }}" class="btn btn-success"
                    style="font-size: 12px;" target="_blank">Edit Page</a>
                @endif
            @else
                @if ($manage_project->projectAssignAgents)
                    @if (Auth::user()->id == $manage_project->projectAssignAgents->agent_id)
                        @if(Request::segment(2) == "previewProject")
                        <a href="{{ route('editProject', ['id' => $manage_project['id']]) }}" class="btn btn-success"
                            style="font-size: 12px;" target="_blank">Edit Page</a>
                        @endif
                        @if(Request::segment(2) == "previewReadyProject")
                        <a href="{{ route('editReadyProject', ['id' => $manage_project['id']]) }}" class="btn btn-success"
                            style="font-size: 12px;" target="_blank">Edit Page</a>
                        @endif
                        @if(Request::segment(2) == "previewSoldOutProject")
                        <a href="{{ route('editSoldOutProject', ['id' => $manage_project['id']]) }}" class="btn btn-success"
                            style="font-size: 12px;" target="_blank">Edit Page</a>
                        @endif
                    @endif
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-7 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <table class="table table1" style="font-size: .9rem">
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Type</th>
                                        <td>{{$manage_project['property']}}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px">Price Range(AED)</th>
                                        <td>
                                            @foreach($manage_project->projectBedrooms as $beddata)
                                            @if($beddata->min_price && $beddata->max_price)
                                            @if($beddata->bed_rooms == "Studio")
                                            {{'Studio : '.number_format($beddata->min_price,0, '.', ',').' - '.number_format($beddata->max_price,0, '.', ',')}}
                                            <br>
                                            @else
                                            {{$beddata->bed_rooms.'BR : '.number_format($beddata->min_price,0, '.', ',').' - '.number_format($beddata->max_price,0, '.', ',')}}
                                            <br>
                                            @endif
                                            @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Commission(%)</th>
                                        <td>
                                            @if ($manage_project->commission)
                                            {{ $manage_project->commission }}%
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Milestone</th>
                                        <td>
                                            @foreach ($manage_project['paymentPlanDetails'] as $key=>$payment)
                                            {{ $payment->percentage }}%
                                            On
                                            @if($payment->installment_terms != 0)
                                            {{ $payment->installment_terms }}
                                            @endif
                                            {{ $payment->milestone }}
                                            @if($payment->milestone == "Handover")
                                            {{ $manage_project->quarter}} {{$manage_project->handover_year}}
                                            @endif
                                            <br>
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table1" style="font-size: .9rem">
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Completion Status:</th>
                                        <td>
                                            @if ($manage_project->completion_status == 1)
                                            Ready
                                            @else
                                            {{$manage_project->quarter}}, {{$manage_project->handover_year}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 180px">Payment Plan:</th>
                                        <td>
                                            @if ($manage_project->payment_plan_comments)
                                            {{ $manage_project->payment_plan_comments }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            @if ($manage_project->rera_permit_no)
                            <p>
                                <b>RERA Number: </b>
                                {{ $manage_project->rera_permit_no }}
                            </p>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($manage_project->construction_status && $manage_project->construction_date)
                            <p>
                                <b>Status & Inspection Date : </b>
                                {{ $manage_project->construction_status }}% -
                                {{ date('d-M-Y', strtotime($manage_project->construction_date))  }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header header">
                    <span class="project-document-file m-0">Document File</span>
                    <button class="btn p-0 add-row" type="button" style="float: right; font-size: initial;"><i
                            class="fas fa-plus-circle"></i></button>
                </div>
                <form class="form-horizontal" id="documentAdd" enctype="multipart/form-data">
                    <div class="card-body abc">
                        @csrf
                        <input type="hidden" name="project_id" value="{{$manage_project['id']}}">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <input type="text" name="attachment[0][document_name]" class="form-control mb-2"
                                    placeholder="Enter The File Name">
                            </div>
                            <div class="col-sm-6">
                                <input type="file" name="attachment[0][document_file]" id="files"
                                    accept="image/*, video/*, .pdf , .doc, .docx, application/vnd.ms-excel, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-10">
                                <div id="image_preview" class="row">
                                    @if($projectDocuments)
                                    @foreach($projectDocuments as $key=>$image)
                                    <div class='upload-image text-center mb-2' title="{{$image->document_name}}" id="{{$image->id}}">
                                        <a href="{{asset('public/projectFiles/documents/'.json_decode($image->document_file))}}"
                                            data_id="{{$key}}" target="_blank">
                                            {{$image->document_name}}
                                        </a>
                                        <i style='color: red' onclick="deleteProjectDocuments(this,{{$image->id}})" class="fas fa-trash-alt delete mt-2" data-toggle="tooltip" data-placement="bottom" title="Delete"></i>
                                    </div>
                                    @endforeach
                                    @endif

                                    @if ($manage_project->pdf)
                                    @foreach (json_decode($manage_project->pdf) as $key => $item)
                                    <div class='upload-image text-center mb-2' title="{{$item}}">
                                        <a href="{{asset('public/projectFiles/documents/'.$item)}}" target="_blank">
                                            @php ($pdf = explode('-', $item))@endphp
                                            {{$pdf[1]}}
                                        </a>
                                        <i style='color: red' onclick="deleteProjectAttachments(this,{{$manage_project->id}}, {{$key}})" class="fas fa-trash-alt" data-toggle="tooltip" data-placement="bottom" title="Delete"></i>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="submit" id="submit-all" class="btn btn-primary mr-1 mb-1"
                                    style="float: right;">Attach</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if ($manage_project->description)
            <div class="card">
                <div class="card-header header">
                    <p class="project-description m-0">Description</p>
                </div>
                <div class="card-body">
                    @if ($manage_project->description)
                    {!! $manage_project->description !!}
                    @else
                    -
                    @endif
                </div>
            </div>
            @endif

            @if ($manage_project->image)
            <div class="card mt-3">
                <div class="card-header header">
                    <p class="project-image m-0">Images</p>
                </div>
                <div class="card-body ">
                    @if ($manage_project->image)
                    @foreach (json_decode($manage_project->image) as $item)
                    <a download="property_image.jpg" href="{{asset('public/projectFiles/images/'.$item)}}">
                        <img src="{{asset('public/projectFiles/images/'.$item)}}"
                            style="height:120px; width:100px; object-fit:cover;" /></a>
                    @endforeach
                    @else

                    @endif
                </div>
            </div>
            @endif
        </div>
        <div class="col-lg-4 col-md-5 col-12">
            <div class="card mt-3 mt-md-0">
                <div class="card-header header">
                    <span class="project-contact m-0">{{$manage_project['developer']['company']}}</span>
                </div>
                <div class="card-body">
                    @foreach ($manage_project['multipleContact'] as $item)
                    <div class="media">
                        <img class="mr-3 user" style="height: 60px"
                            src="https://static.thenounproject.com/png/17241-200.png">
                        <div class="media-body">
                            <p class="m-0">{{$item->person}}</p>
                            <p class="m-0">{{$item->phone}}</p>
                            <p class="m-0">{{ $manage_project['developer']['email'] }}</p>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                </div>
            </div>

            <div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Note</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addProjectNote" autocomplete="off">
                                @csrf
                                <textarea class="form-control" rows="3" name="note" placeholder="Enter note."
                                    id="note"></textarea>
                                <input type="hidden" name="proj_id" id="proj_id" value="{{$manage_project['id']}}">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="submitAddUpdate">Save Note</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header header">
                    <span class="project-note m-0">Note History</span>
                    <button class="btn p-0" type="button" id="addNote" style="float: right; font-size: initial;"><i class="fas fa-plus-circle"></i></button>
                </div>
                <div class="card-body note-history">
                    @foreach ($projectNote as $key => $item)
                    <div class="card p-2 noteRemove">
                        <div>
                            <span class="mr-3"><b>{{$item->agentName->name}}</b></span>
                            <span>Added On {{date('d-M-Y H:i A',strtotime($item->created_at))}}</span>
                            <a href="javascript:void(0)" style="float: right;"
                                onclick="deleteConfirmation({{$item->id}}, this)"><i style="font-size: 17px; color: red"
                                    class="fas fa-times-circle" data-toggle="tooltip" data-placement="bottom"
                                    title="Delete"></i></a>
                            <br>
                            <p style="margin-top: 10px">{{$item->note}}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header header">
                    <span class="project-reminder m-0">Reminder</span>
                    <button class="btn p-0" type="button" id="addReminder" style="float: right; font-size: initial;"><i class="fas fa-plus-circle"></i></button>
                </div>
                <div class="card-body reminder">
                    @if(!$manage_project->projectReminders->isEmpty())
                        <caption><span id="removetitle">Reminder/FollowUp</span></caption>
                        <table class="table table-striped">
                            <tbody>
                                @foreach ($manage_project->projectReminders as $item)
                                    <div class="card p-2 reminderRemove">
                                        <div>
                                            <span class="mr-3"><b>{{$item->user->name}}</b></span>
                                            <span>
                                                @if ($item->created_at)
                                                    @if($item->reminder_date <= \Carbon\Carbon::now()->toDateString() )
                                                        <span style="color: red">Added On {{\Carbon\Carbon::parse($item->created_at)->format('d-M-Y g:i A')}}</span>
                                                    @else
                                                        <span>Added On {{\Carbon\Carbon::parse($item->created_at)->format('d-M-Y g:i A')}}</span>
                                                    @endif
                                                    
                                                    <div style="display:inline;float:right;">
                                                        <button data-toggle="tooltip" data-placement="bottom" title="Edit" data-id="{{$item->id}}" class="btn p-0 editReminder" onclick="editReminder({{$item->id}}, this)" type="button" style="float: right; font-size: initial;"><i class="fas fa-edit"></i></button>
                                                    </div>
                                                    <div class="custom-control custom-switch" style="display:inline;float:right;" data-toggle="tooltip" data-placement="bottom" title="Done">
                                                        <input type="checkbox" class="custom-control-input" id="customSwitch{{$item->id}}" data-id="{{$item->id}}" onclick="fn_project_reminder_status_changes(this)" value="1" {{($item->status)?'checked':''}}>
                                                        <label class="custom-control-label" for="customSwitch{{$item->id}}"></label>
                                                    </div>
                                                    
                                                    @if($item->reminder_date <= \Carbon\Carbon::now()->toDateString() )
                                                        <br><button style="background-color: #8B2323;color:white; border-top-right-radius: initial;" class="mt-2 mb-2">Date Time gone</button>
                                                    @else
                                                        {{-- <br><button style="background-color: rgb(63, 159, 223); color:white; border-top-right-radius: initial;" class="mt-2 mb-2">Future Date</button> --}}
                                                    @endif
                                                @else
                                                -    
                                                @endif
                                            </span>
                                            <br>
                                            <span><b>Reminder Title:</b> {{$item->title}}</span>
                                            <br>
                                            <span><b>Reminder Date:</b> {{date('d-M-Y',strtotime($item->reminder_date))}}</span>
                                            <br>
                                            <span><b>Comment:</b> {{$item->comment}}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="remindermodel" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUpdateTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addProjectReminder" autocomplete="off">
                    @csrf
                    <input type="hidden" name="project_id" value="{{$manage_project['id']}}" id="project_id">
                    <input type="hidden" name="project_reminder_id" value="" id="project_reminder_id">
                    <div class="form-group">
                        <label class="col-form-label">Title</label>
                        <input class="form-control" type="text" name="title" value="" placeholder="Enter Title" id="title" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Reminder Date</label>
                        <input class="form-control" type="date" name="reminder_date" id="reminder_date" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Comment</label>
                        <textarea class="form-control" rows="3" placeholder="Enter comment." name="comment" id="comment"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="submitAddUpdateReminder">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $('#addNote').on('click', function () {
        $('#addProjectNote').trigger('reset');
        $('#note').val('');
        $('#noteModal').modal('show');
    });

    $('#addReminder').on('click', function () {
        $('#addUpdateTitle').html('Add Event');
        $('#project_reminder_id').val('');
        $('#title').val('');
        $('#reminder_date').val('');
        $('#comment').val('');
        $('#remindermodel').modal('show');
    });

function editReminder(id, el) {
        var project_id = $('#project_id').val();
        var reminder_id = id;
        $.ajax({
            url: "{{route('getProjectReminder')}}",
            type: "POST",
            data: {
                project_id: project_id,
                reminder_id: reminder_id,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (data) {
                $('#addUpdateTitle').html('Edit Event');
                var id = data.id ? data.id : '';
                var title = data.title ? data.title : '';
                var reminder_date = data.reminder_date ? data.reminder_date : '';
                var comment = data.comment ? data.comment : '';
                $('#project_reminder_id').val(id);
                $('#title').val(title);
                $('#reminder_date').val(reminder_date);
                $('#comment').val(comment);
                $('[data-toggle="tooltip"]').tooltip('dispose');
            }
        });
        $('#remindermodel').modal('show');
    };

    $('#submitAddUpdateReminder').click(function (event) {
        event.preventDefault()

        var myform = document.getElementById("addProjectReminder");
        var formData = new FormData(myform);
        formData: $(this).serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addProjectReminder')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                     $('#remindermodel').modal('hide');
                    $(".reminder").load(location.href+" .reminder>*");
                } else {
                    swal("ERROR!", '<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    $('#submitUpdateReminder').click(function (event) {
        event.preventDefault()

        var myform = document.getElementById("editProjectReminder");
        var formData = new FormData(myform);
        formData: $(this).serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addProjectReminder')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    location.reload();
                } else {
                    swal("ERROR!", '<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    $('#submitAddUpdate').click(function (event) {
        event.preventDefault()
        var myform = document.getElementById("addProjectNote");
        var formData = new FormData(myform);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addProjectNote')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#noteModal').modal('hide');
                    var html = '<div class="card p-2 noteRemove"><div><span><b>' + data.note
                        .agent_name + '</b></span> &nbsp; &nbsp; &nbsp;<span>Added On ' + data.note
                        .date +
                        '</span><a href="javascript:void(0)" style="float: right;" onclick="deleteConfirmation(' +
                        data.note.id +
                        ', this)"><i style="font-size: 17px; color: red" class="fas fa-times-circle" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a><br><p style="margin-top: 10px">' +
                        data.note.note + '</p></div>'
                    $('.note-history').append(html);
                } else {
                    swal("ERROR!", data.message, "error");
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

    function deleteConfirmation(id, el) {
        swal({
            title: "Delete?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {
                $.ajax({
                    url: "{{route('deleteProjectNotes')}}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.status) {
                            swal({
                                title: "Done!",
                                text: data.message,
                                type: "success"
                            }).then(function () {
                                var index = $(el).parents(".noteRemove").remove();
                            });
                        } else {
                            swal("Error!", data.message, "error");
                        }
                    }
                });
            } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })
    }

    var index = 0;
    $(".add-row").click(function () {
        index++;
        var add =
            "<div class='form-group row'><div class='col-sm-6'><input type='text' name='attachment[" +
            index +
            "][document_name]' class='form-control mb-2' placeholder='Enter The File Name'></div><input type='hidden' name='id' value=''><div class='col-sm-6'> <input type='file' name='attachment[" +
            index +
            "][document_file]' id='files' accept='image/*, .pdf , .doc, .docx, application/vnd.ms-excel, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'/></div></div>"
        $(".abc").append(add);
    });

    function fn_project_reminder_status_changes(e) {
        var id = $(e).data("id");
        $.ajax({
            url: "{{route('changeProjectReminderStatus')}}",
            type: "POST",
            data: {
                id: id,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    $('[data-toggle="tooltip"]').tooltip('dispose');
                    var index = $(e).parents(".reminderRemove").remove();
                    var numItems = $('.reminderRemove').length
                    if(!numItems)
                    {
                        $('#removetitle').text('');
                    }
                } else {
                    swal("ERROR!", '<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                }
            }
        });
    }

</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#submit-all').click(function (event) {
            event.preventDefault()
            var myform = document.getElementById("documentAdd");
            var formData = new FormData(myform);
            var segments = location.pathname.split('/');
            formData: $(this).serialize();

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
                url: "{{route('addProjectDocument')}}",
                data: formData,
                success: function (data) {
                    if (data.status == 1) {
                        location.reload();
                    } else {
                        swal("ERROR!", '<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
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

    function deleteProjectDocuments(value, id) {

        swal({
            title: "Delete?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {
                window.swal({
                    title: "Checking...",
                    text: "Please wait",
                    imageUrl: "{{ asset('public/ajaxloader/ajaxloader.gif') }}",
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                $.ajax({
                    url: "{{route('deleteProjectDocuments')}}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.status == 1) {
                            location.reload();
                        } else {
                            swal("ERROR!", '<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                        }
                    }
                });
            } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })
    }

    function deleteProjectAttachments(value, id, key) {

        swal({
            title: "Delete?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {
                window.swal({
                    title: "Checking...",
                    text: "Please wait",
                    imageUrl: "{{ asset('public/ajaxloader/ajaxloader.gif') }}",
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                $.ajax({
                    url: "{{route('deleteProjectAttachments')}}",
                    type: "POST",
                    data: {
                        id: id,
                        key: key,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.status == 1) {
                            location.reload();
                        } else {
                            swal("ERROR!", '<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                        }
                    }
                });
            } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })
    }

</script>
@endsection
