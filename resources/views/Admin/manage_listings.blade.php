@extends('layouts.admin-app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"
    rel="stylesheet" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">

@section('content')
    <style>
        .dd-remove::before{
            display: none !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
            color: #FFFFFF;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            background-color: #17a2b8;
            border: 1px solid #17a2b8;
        }
    </style>
<section class="content">
    @if($response = session('response'))
    <div class="alert @if($response['status']) alert-success @else alert-danger @endif" style="margin-top:10px">
        {{ $response['message'] }}
    </div>
    @endif
    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Units</h3>
                    </div>
                    @if($permission)
                        @if($permission->create)
                            <div class="card-header">
                                <h5 class="card-header-text"></h5>
                                <span class="card-header-right-span"><a href="{{ route('add-view-unit')}}" class="btn btn-info "><i class="fa fa-plus"></i> Add Unit</a></span>
                            </div>
                        @endif
                    @endif
                    <div class="card-header">
                        <form class="form-inline ml-3" style="font-size: .8rem" method="post" id="Search" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="input-group col-3 mb-3">
                                    <select class="custom-select select-community select2" id="community"
                                        name="community">
                                        @if($community)
                                        <option selected disabled value="">Select Community</option>
                                        @foreach($community as $communityData)
                                        <option value="{{$communityData->id}}">{{$communityData->name}}</option>
                                        @endforeach
                                        @else
                                        <option disabled selected value="">Select Community</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="custom-select select-subcommunity select2" name="subcommunity"
                                        id="subcommunity">
                                        <option selected disabled value="">Select Sub Community</option>
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select-property select2" name="property">
                                        <option selected disabled value="">Select Property</option>
                                        @foreach ($typeList as $item)
                                        <option value="{{$item}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select-bedrooms select2" id="bedrooms" name="bedrooms">
                                        <option selected disabled value="">Select Bedrooms</option>
                                        <option value="Studio">Studio</option>
                                        @for ($i = 1; $i <= 20; $i++) <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="number" name="min_price" placeholder="Price From"
                                        class="form-control select-min-price">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="number" name="max_price" placeholder="Price To"
                                        class="form-control select-max-price">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="number" name="min_size" placeholder="Size From"
                                        class="form-control select-min-size">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="number" name="max_size" placeholder="Size To"
                                        class="form-control select-max-size">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="custom-select select-project select2" id="project" name="project">
                                        @if($project)
                                        <option selected disabled value="">Select Project</option>
                                        @foreach($project as $userdata)
                                        <option value="{{$userdata->project}}">{{$userdata->project}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select-company select2" id="company" name="company">
                                        <option disabled selected value="">Select Developer</option>
                                        @if($userlist)
                                        @foreach($userlist as $userData)
                                        <option value="{{$userData['company']}}">{{$userData['company']}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="text" name="handover_year" id="handover_year"
                                        placeholder="Handover Year" class="form-control  select-handover-year">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select-quarter select2" name="quarter">
                                        <option selected disabled value="">Select Quarter</option>
                                        <option value="Q1">Q1</option>
                                        <option value="Q2">Q2</option>
                                        <option value="Q3">Q3</option>
                                        <option value="Q4">Q4</option>
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select-payment-plan select2" name="payment_plan">
                                        <option selected disabled value="">Select Payment Plan</option>
                                        <option value="0">Yes</option>
                                        <option value="1">No</option>
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="number" name="amount-upto-handover" id="amount-upto-handover"
                                        placeholder="Amount UpTo Handover"
                                        class="form-control select-amount-upto-handover">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <input type="number" name="post-handover" id="post-handover"
                                        placeholder="Amount Post Handover" class="form-control select-post-handover">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select-flag select2" name="flag[]" multiple="multiple">
                                        <option value="1">New Launch</option>
                                        <option value="2">High in Demand</option>
                                        <option value="3">Limited Availability</option>
                                        <option value="4">Value for Money</option>
                                        <option value="5">Best Layout</option>
                                        <option value="6">Attractive Payment Plan</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-primary" type="button" id="searchData">Search</button>
                                    <button class="btn btn-secondary" type="button" id="resetSearchData">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example2" style="font-size: .8rem"
                            class="table table-striped table-hover datatableManageListings">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>Flag</th>
                                    <th>Developer</th>
                                    <th>Project</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Beds</th>
                                    <th>Size(Sq.ft)</th>
                                    <th>Price(AED)</th>
                                    <th>Handover</th>
                                    <th>Upto Handover(AED)</th>
                                    <th>Post-Handover(AED)</th>
                                    <th>Project To Ready</th>
                                    <th>Project To Soldout</th>
                                    <th>Updated Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">NOTE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" id='frmTarget'>
                        @csrf
                        <textarea class="form-control" rows="3" name="note" placeholder="Enter note."
                            id="note"></textarea>
                        <input type="hidden" name="proj_id" value="" id="proj_id">
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-all" class="btn btn-primary">Save Note</button>
                    </form>
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ADDED BY</th>
                                <th scope="col">DATE ADDED</th>
                                <th scope="col">NOTE</th>
                                <th scope="col">CONTROLS</th>
                            </tr>
                        </thead>
                        <tbody id="note-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="remindermodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" id='reminderForm'>
                        @csrf
                        <input type="hidden" name="project_id" value="" id="project_id">
                        <label>Title</label>
                        <input class="form-control" type="text" name="title" value="" placeholder="Enter Title"
                            id="title" required>
                        <label>Reminder Date</label>
                        <input class="form-control" type="date" name="reminder_date" id="remiderdate" required>
                        <label>Comment</label>
                        <textarea class="form-control" rows="2" placeholder="Enter comment." name="comment"
                            id="comment"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit" class="btn btn-primary">Save Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<script>
    $("#handover_year").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    $(function () {
        var table = $('.datatableManageListings').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('datatableManageListings')}}",
                type: "POST",
                data: function (d) {
                    d.community = $('.select-community').val(),
                        d.subcommunity = $('.select-subcommunity').val(),
                        d.property = $('.select-property').val(),
                        d.bedrooms = $('.select-bedrooms').val(),
                        d.min_price = $('.select-min-price').val(),
                        d.max_price = $('.select-max-price').val(),
                        d.min_size = $('.select-min-size').val(),
                        d.max_size = $('.select-max-size').val(),
                        d.project = $('.select-project').val(),
                        d.company = $('.select-company').val(),
                        d.handover_year = $('.select-handover-year').val(),
                        d.quarter = $('.select-quarter').val(),
                        d.payment_plan = $('.select-payment-plan').val(),
                        d.amountuptohandover = $('.select-amount-upto-handover').val(),
                        d.posthandover = $('.select-post-handover').val(),
                        d.flag = $('.select-flag').val(),
                        d.status = 'listing',
                        d._token = '{{csrf_token()}}'
                }
            },
            columns: [{
                    data: 'rf_no',
                    name: 'rf_no'
                },
                {
                    data: 'flag',
                    name: 'flag'
                },
                {
                    data: 'developer_company',
                    name: 'developer_company'
                },
                {
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'communitys',
                    name: 'communitys'
                },
                {
                    data: 'property',
                    name: 'property'
                },
                {
                    data: 'bedrooms',
                    name: 'bedrooms'
                },
                {
                    data: 'size',
                    name: 'size',
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'quarter_and_handover_year',
                    name: 'quarter_and_handover_year'
                },
                {
                    data: 'up_to_handover',
                    name: 'up_to_handover'
                },
                {
                    data: 'post_handover',
                    name: 'post_handover'
                },
                {
                    data: 'ready_status',
                    name: 'ready_status'
                },
                {
                    data: 'sold_out_status',
                    name: 'sold_out_status'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
            createdRow: function (row, data, index) {
                if (data['active'] == 1) {
                    $('td', row).css('color', 'red');
                }
            },
        });
    });

    $(document).on("click", ".reminder", function () {
        var projid = $(this).data('listid');
        $("#project_id").val(projid);
        var reminderlist = $(this).data('reminderlist');
        if (reminderlist.length > 0) {
            $.each(reminderlist, function (key, value) {
                $("#title").val(value.title);
                $("#remiderdate").val(value.reminder_date);
                $("#comment").val(value.comment);
            });
        } else {
            $("#title").val('');
            $("#remiderdate").val('');
            $("#comment").val('');
        }
    });

    $(document).on("click", ".user_dialog", function () {
        var projid = $(this).data('listid');
        $(".modal-body #proj_id").val(projid);
        var addedby = $(this).data('name');
        $.ajax({
            url: "{{route('getUnitNoteList')}}",
            type: "POST",
            data: {
                projid: projid,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (notelist) {
                $('#note-table').empty();
                if (notelist.length) {
                    var table = '';
                    for (var i = 0; i < notelist.length; i++) {
                        var note = notelist[i];
                        table += "<tr>";
                        table += "<td>" + addedby + "</td>";
                        table += "<td>" + moment(note.updated_at).format("DD-MMM-YY") + "</td>";
                        table += "<td>" + note.note + "</td>";
                        table +=
                            "<td><a href='javascript:void(0)' class='note-delete' onclick='removeNote(this," +
                            note.id +
                            ")'><i style='color: red' class='fas fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i></a></td>";
                        table += "</tr>";
                    }
                    $('#note-table').append(table);
                }
            }
        });
    });

    $('#submit-all').click(function (event) {
        var note = $('#note').val();
        if (!note) {
            alert('please enter note');
            return false;
        }
        event.preventDefault()
        var myform = document.getElementById("frmTarget");
        var formData = new FormData(myform);
        formData: $(this).serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('add-note')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#note').val('');
                    if (data.note) {
                        var table = '';
                        var note = data.note;
                        table += "<tr>";
                        table += "<td>" + note.addedby + "</td>";
                        table += "<td>" + moment(note.updated_at).format("DD-MMM-YY") + "</td>";
                        table += "<td>" + note.note + "</td>";
                        table +=
                            "<td><a href='javascript:void(0)' class='note-delete' onclick='removeNote(this," +
                            note.id +
                            ")'><i style='color: red' class='fas fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i></a></td>";
                        table += "</tr>";
                        $('#note-table').append(table);
                    }
                } else {
                    swal("ERROR!", data.message, "error");
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

    $('#submit').click(function (event) {
        event.preventDefault()

        var myform = document.getElementById("reminderForm");
        var formData = new FormData(myform);
        formData: $(this).serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('reminder.store')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#project_id').val('');
                    $('#title').val('');
                    $('#remiderdate').val('');
                    $('#comment').val('');
                    $('#remindermodel').modal('hide');
                    $('.datatableManageListings').DataTable().draw(true);
                } else {
                    swal("ERROR!", data.message, "error");
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

</script>


<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        $('#community').on('change', function () {
            var id = this.value;
            $.ajax({
                url: "{{route('get-subcommunity')}}",
                type: "POST",
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#subcommunity').html(
                        '<option selected disabled>Select Sub Community</option>');
                    $.each(result.subcommunity, function (key, value) {
                        $("#subcommunity").append('<option value="' + value.id +
                            '">' + value.name + '</option>');
                    });
                }
            });
        });

        $(document).on("click", "#searchData", function () {
            $('.datatableManageListings').DataTable().draw(true);
        });

        $(document).on("click", "#resetSearchData", function () {
            $('.select-community').val('');
            $('.select-subcommunity').empty();
            $('.select-subcommunity').html(
                '<option selected disabled value="">Select Sub Community</option>');
            $('.select-property').val('');
            $('.select-bedrooms').val('');
            $('.select-min-price').val('');
            $('.select-max-price').val('');
            $('.select-min-size').val('');
            $('.select-max-size').val('');
            $('.select-project').val('');
            $('.select-company').val('');
            $('.select-handover-year').val('');
            $('.select-quarter').val('');
            $('.select-flag').val('');
            $('.select-payment-plan').val('');
            $('.select-amount-upto-handover').val('');
            $('.select-post-handover').val('');
            $('.select2').select2();
            $('.datatableManageListings').DataTable().draw(true);
        });

        $(document).on('click', '.delete-confirm', function () {
            var id = $(this).attr('data-listid');
            $.ajax({
                url: '{{route('delete-unit')}}',
                method: "GET",
                data: {
                    'id': id
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: false,
                beforeSend: function () {
                    return confirm("Are you sure you want to delete this record ?");
                },
                success: function (data) {
                    if (data.status == 1) {
                        $('.datatableManageListings').DataTable().draw(true);
                    } else {
                        swal("ERROR!", data.message, "error");
                    }
                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                }
            });
        });
    });

    function removeNote(value, id) {
        $.ajax({
            url: '{{route('remove-note')}}',
            method: "GET",
            data: {
                'id': id
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },

            cache: false,
            beforeSend: function () {
                return confirm("Are you sure?");
            },
            success: function (data) {
                if (data.status == 1) {
                    $('#note-table').empty();
                    var notelist = data.noteList;
                    console.log(data);
                    if (notelist.length) {
                        var table = '';
                        for (var i = 0; i < notelist.length; i++) {
                            var note = notelist[i];
                            table += "<tr>";
                            table += "<td>" + data.addedby + "</td>";
                            table += "<td>" + moment(note.updated_at).format("DD-MMM-YY") + "</td>";
                            table += "<td>" + note.note + "</td>";
                            table +=
                                "<td><a href='javascript:void(0)' class='note-delete' onclick='removeNote(this," +
                                note.id +
                                ")'><i style='color: red' class='fas fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i></a></td>";
                            table += "</tr>";
                        }
                        $('#note-table').append(table);
                    }
                } else {
                    swal("ERROR!", data.message, "error");
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
            }
        });
    }

    function fn_project_status_changes(e) {
        var id = $(e).data("id");
        $.ajax({
            url: '{{route('add_ready_status')}}',
            method: "GET",
            data: {
                'id': id,
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: false,
            beforeSend: function () {
                return confirm("Are you sure?");
            },
            success: function (data) {
                if (data.status == 1) {
                    $('.datatableManageListings').DataTable().draw(true);
                } else {
                    swal("ERROR!", data.message, "error");
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
            }
        });
    }

    function sold_out_project_status_changes(e) {
        var id = $(e).data("id");
        $.ajax({
            url: '{{route('add_sold_out_status')}}',
            method: "GET",
            data: {
                'id': id,
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: false,
            beforeSend: function () {
                return confirm("Are you sure?");
            },
            success: function (data) {
                if (data.status == 1) {
                    $('.datatableManageListings').DataTable().draw(true);
                } else {
                    swal("ERROR!", data.message, "error");
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
            }
        });
    }
    $(document).on("click", ".flag", function () {
        var client_id = $(this).data('id');
        var flag = $(this).data('flag');
        $.ajax({
            url: "{{route('update_flag_status')}}",
            method: "GET",
            data: {'id': client_id, 'flag': flag},
            beforeSend: function () {
                return confirm("Are you sure?");
            },
            success: function (data) {
                if (data.status == 1) {
                    swal("Done!", data.message, "success");
                    $('.datatableManageListings').DataTable().draw(true);
                } else {
                    swal("ERROR!", data.message, "error");
                }

            },
            error: function (data) {
                swal("ERROR!", data, "error");
            }
        });
    });
</script>
@endsection
