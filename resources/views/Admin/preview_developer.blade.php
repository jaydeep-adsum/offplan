@extends('layouts.admin-app')
@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Developer Preview</h3>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Expiry Date</th>
                        <th>Note</th>
                        <th>Attachment (PDF/Word/Image)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$developer->company}}</td>
                        <td>{{$developer->email}}</td>
                        <td>{{$developer->date}}</td>
                        <td>{{$developer->note}}</td>
                        <td>
                            @foreach (json_decode($developer->pdf) as $item)
                            <a href="{{asset('public/files/developer/'.$item)}}" target="_blank" download>
                                @php ($pdf = explode('-', $item))@endphp
                                {{$pdf[1]}}
                            </a><br>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<section class="content pt-3 col-12">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Contact Details</h3>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Point of Contact</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contact as $item)
                            <tr>
                                <td>
                                    {{$item->person}}
                                </td>
                                <td>
                                    {{$item->phone}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Attachment File</h3>
                        <button class="btn add-row p-0" type="button" style="float: right; font-size: initial;"><i class="fas fa-plus-circle"></i></button>
                    </div>
                    <form class="form-horizontal" method="POST" id="frmTarget" enctype="multipart/form-data">
                        <div class="card-body abc">
                            @csrf
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="text" name="attachment[0][attachment_name]" class="form-control mb-2"
                                        placeholder="Enter The File Name">
                                </div>
                                <input type="hidden" name="id" value="{{$developer->id}}" id="developer_id">
                                <div class="col-sm-6">
                                    <input type="file" name="attachment[0][attachment_multiple]" id="files"
                                        accept="image/*, video/*, .pdf , .doc, .docx, application/vnd.ms-excel, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required/>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-10">
                                    <div id="image_preview" class="row">
                                        @if($multipleimage)
                                        @foreach($multipleimage as $key=>$image)
                                        <div class='upload-image text-center mb-2' title="{{$image->attachment_name}}" id ="{{$image->id}}" >
                                            <a href="{{asset('public/files/developer_attachment/'.json_decode($image->attachment_multiple))}}" data_id="{{$key}}" target="_blank">
                                                {{$image->attachment_name}}
                                            </a>
                                            <i style='color: red' onclick="removeattachment(this,{{$image->id}})" class="fas fa-trash-alt delete mt-2" data-toggle="tooltip" data-placement="bottom" title="Delete"></i>
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
        </div>
    </div>
</section>

<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addDeveloperNote" autocomplete="off">
                    @csrf
                    <textarea class="form-control" rows="3" name="note" placeholder="Enter note."
                        id="note"></textarea>
                    <input type="hidden" name="developer_id" id="developer_id" value="{{$developer->id}}">
                    <input type="hidden" name="developer_note_id" id="developer_note_id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submitAddUpdate">Save Note</button>
            </div>
        </div>
    </div>
</div>
<section class="content pt-3">
    <div class="col-6">
        <div class="card card-info">
            <div class="card-header header">
                <h3 class="card-title">Notes</h3>
                <button class="btn p-0" type="button" id="addNote" style="float: right; font-size: initial;"><i class="fas fa-plus-circle"></i></button>
            </div>
            <div class="card-body note-history">
                @foreach ($developerNote as $key => $item)
                <div class="card p-2 noteRemove">
                    <div>
                        <span class="mr-3"><b>{{$item->agentName->name}}</b></span>
                        <span>Added On {{date('d-M-Y H:i A',strtotime($item->created_at))}}</span>
                        <a href="javascript:void(0)" style="float: right;" onclick="deleteNote({{$item->id}}, this)"><i style="font-size: 17px; color: red" class="fas fa-times-circle" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>
                        <a href="javascript:void(0)" style="float: right;" onclick="editNote({{$item->id}}, this)" class="editDeveloperNote"><i style="font-size: 17px;" class="fas fa-edit mr-3" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>
                        <br>
                        <p style="margin-top: 10px">{{$item->note}}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="content pt-3">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header header">
                <h3 class="card-title">{{$developer->company}} Project Details</h3>
            </div>
            <div class="card-body">
                <table style="font-size: .9rem" class="table table-striped table-hover datatableDeveloperProjectList">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Property Type</th>
                            <th>Location</th>
                            <th>Completion Status</th>
                            <th>Price Range</th>
                            <th>Payment Plan</th>
                            <th>Commission</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection
@section('scripts')
<script type="text/javascript">

    $('#addNote').on('click', function () {
        $('#addDeveloperNote').trigger('reset');
        $('#note').val('');
        $('#developer_note_id').val('');
        $('#noteModal').modal('show');
    });

    $('#submitAddUpdate').click(function (event) {
        event.preventDefault()
        var myform = document.getElementById("addDeveloperNote");
        var formData = new FormData(myform);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addDeveloperNote')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#noteModal').modal('hide');
                    $(".note-history").load(location.href+" .note-history>*");
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

    function editNote(id, el) {
        var developer_note_id = id;
        $.ajax({
            url: "{{route('getDeveloperNote')}}",
            type: "POST",
            data: {
                developer_note_id: developer_note_id,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (data) {
                if(data.id)
                {
                    $('#developer_note_id').val(data.id);
                    $('#note').val(data.note);
                    $('#noteModal').modal('show');
                }
            }
        });
    };

    function deleteNote(id, el) {
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
                    url: "{{route('deleteDeveloperNotes')}}",
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
                                type: "success",
                                timer: 800,
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

    $(function () {
        var table = $('.datatableDeveloperProjectList').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('datatableDeveloperProjectList')}}",
                type: "POST",
                data: function (d) {
                    d._token = '{{csrf_token()}}',
                    d.developer_id = $('#developer_id').val();
                }
            },
            columns: [{
                    data: 'project_name',
                    name: 'project_name'
                },
                {
                    data: 'property_type',
                    name: 'property_type'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'completion_status',
                    name: 'completion_status'
                },
                {
                    data: 'price_range',
                    name: 'price_range'
                },
                {
                    data: 'payment_plan',
                    name: 'payment_plan'
                },
                {
                    data: 'commission',
                    name: 'commission'
                }
            ]
        });
    });

    var index = 0;
    $(".add-row").click(function () {        
        index++;
        var add =
            "<div class='form-group row'><div class='col-sm-6'><input type='text' name='attachment["+ index +"][attachment_name]' class='form-control mb-2' placeholder='Enter The File Name'></div><input type='hidden' name='id' value='{{$developer->id}}'><div class='col-sm-6'> <input type='file' name='attachment["+ index +"][attachment_multiple]' id='files' accept='image/*, .pdf , .doc, .docx, application/vnd.ms-excel, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'/></div></div>"
        $(".abc").append(add);
    });

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
            title: "Uploading...",
            text: "Please wait",
            imageUrl: "{{ asset('public/ajaxloader/ajaxloader.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
            });
            $.ajax({
                type: "POST",
                url: "{{route('attachment-post')}}",
                data: formData,
                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");       
                        var preview_developer = '{{ route("preview-user", ":id") }}';
                        preview_developer = preview_developer.replace(':id', segments[4]);
                        window.location.href = preview_developer;
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

    function removeattachment(value,id)
    {
        var segments = location.pathname.split('/');
        $.ajax({
            url: '{{route('removeattachment')}}',
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
                var preview_developer = '{{ route("preview-user", ":id") }}';
                preview_developer = preview_developer.replace(':id', segments[4]);
                window.location.href = preview_developer;
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
