@extends('layouts.admin-app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"
    rel="stylesheet" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<style>
    .swal2-popup
    {
        width: 507px !important;
    }
</style>

@section('content')
<section class="content">
    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Project</h3>
                    </div>
                    <div class="card-header">
                        <h5 class="card-header-text"></h5>
                        <span class="card-header-right-span"><a href="{{ route('addProject')}}" class="btn btn-info "><i class="fa fa-plus"></i> Add Project</a></span>
                    </div>
                    <div class="card-header">
                        <form class="form-inline ml-3" style="font-size: .8rem" method="post" id="Search" autocomplete="off">
                            @csrf
                            <div class="row form-group w-100">
                                <div class="col-sm-3 mb-3">
                                    <select class="form-control select2" id="company" name="company" style="width: 100%">
                                        <option  selected value="">Select Developer</option>
                                        @if($userlist)
                                        @foreach($userlist as $userData)
                                        <option value="{{$userData['company']}}">{{$userData['company']}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <select class="form-control select2" id="project" name="project" style="width: 100%">
                                        @if($project)
                                        <option selected value="">Select Project</option>
                                        @foreach($project as $userdata)
                                        <option value="{{$userdata->project}}">{{$userdata->project}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <select class="form-control select2" id="community" name="community" style="width: 100%">
                                        <option selected value="">Select Location</option>
                                        @foreach($community as $communityData)
                                            <option value="{{$communityData->id}}">{{$communityData->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <select class="form-control select2" id="property" name="property" style="width: 100%">
                                        <option selected value="">Select Property Type</option>
                                        @foreach ($typeList as $item)
                                        <option value="{{$item}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group col-sm-3 mb-3">
                                    <select name="number_of_bedrooms" class="form-control select2" id="number_of_bedrooms">
                                        <option value="" selected >Select Bedrooms</option>
                                        <option value="Studio">Studio</option>
                                        @for ($i = 1; $i <= 20; $i+=0.5)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="input-group col-sm-3 mb-3">
                                    <input type="text" onKeyPress="if(this.value.length==10) return false;" name="min_price" placeholder="Enter Price From" class="form-control numbersOnly" id="min_price">
                                </div>
                                <div class="input-group col-sm-3 mb-3">
                                    <input type="text" onKeyPress="if(this.value.length==10) return false;" name="max_price" placeholder="Enter Price To" class="form-control numbersOnly" id="max_price">
                                </div>
                                <div class="input-group col-sm-3 mb-3">
                                    <select class="form-control select2" id="quarter" name="quarter" style="width: 100%">
                                        <option selected value="">Select Quarter</option>
                                        <option value="Q1">Q1</option>
                                        <option value="Q2">Q2</option>
                                        <option value="Q3">Q3</option>
                                        <option value="Q4">Q4</option>
                                    </select>
                                </div>
                                <div class="input-group col-sm-3 mb-3">
                                    <input type="number" name="handover_year" min='0' id="handover_year" class="form-control" placeholder="Enter Handover Year">
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select2" name="payment_plan" id="payment_plan">
                                        <option selected value="">Select Payment Plan</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="input-group col-3 mb-3">
                                    <select class="form-control select2" name="assigned_agents" id="assigned_agents">
                                        <option selected value="">Select Assigned Agents</option>
                                        @foreach ($agents as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3 mb-3">
                                    <button class="btn btn-primary" type="button" id="searchData">Search</button>
                                    <button class="btn btn-secondary" type="button" id="resetSearchData">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <table id="example2" style="font-size: .8rem" class="table table-striped table-hover datatableManageProject">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>Assign to</th>
                                    <th>Project Name</th>
                                    <th>Developer Name</th>
                                    <th>Location</th>
                                    <th>Completion Status</th>
                                    <th style="width: 210px">Price Range(AED)</th>
                                    <th>Payment Plan</th>
                                    <th>Commission(%)</th>
                                    <th>Updated Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="assignProjectModel" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Assign Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addAssignProject" autocomplete="off">
                    @csrf
                    <input type="hidden" name="project_id" value="" id="project_id">
                    <div class="form-group">
                        <label class="col-form-label">Project Name</label>
                        <input class="form-control" readonly type="text" name="project_name" id="project_name">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Select Agents</label>
                        <select class="select2" name="agents" id="agents" style="width: 100%">
                            <option selected disabled value="0">Select Agents</option>
                            @foreach ($agents as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                            <option value=""></option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="submitAddUpdate">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"
    rel="stylesheet" />

<script type="text/javascript">

    $('.select2').select2();

    document.querySelector(".numbersOnly").addEventListener("keypress", function (evt) {
        if (evt.which != 8 && evt.which != 0 && evt.which < 48 || evt.which > 57)
        {
            evt.preventDefault();
        }
    });

    $(document).on("click", "#searchData", function () {
        $('.datatableManageProject').DataTable().draw(true);
    });

    $(document).on("click", "#resetSearchData", function () {
        $('#company').val('');
        $('#project').val('');
        $('#community').val('');
        $('#property').val('');
        $('#number_of_bedrooms').val('');
        $('#min_price').val('');
        $('#max_price').val('');
        $('#quarter').val('');
        $('#handover_year').val('');
        $('#payment_plan').val('');
        $('#assigned_agents').val('');
        $('.select2').select2();
        $('.datatableManageProject').DataTable().draw(true);
    });

    $("#handover_year").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $(document).on('click', '.assignedProject', function () {
        var project_id = $(this).data('listid');
        var project_name = $(this).data('project_name');
        var agent_id = $(this).data('agent_id');
        $('#project_id').val(project_id);
        $('#project_name').val(project_name);
        $('#agents').val(agent_id);
        $('#agents').select2();
        $('#assignProjectModel').modal('show');
    });

    $(function () {
        var table = $('.datatableManageProject').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            "order": [[ 9, "desc" ]],
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('datatableManageProject')}}",
                type: "POST",
                data: function (d) {
                    d._token = '{{csrf_token()}}',
                    d.company = $('#company').val();
                    d.project = $('#project').val();
                    d.community = $('#community').val();
                    d.property = $('#property').val();
                    d.number_of_bedrooms = $('#number_of_bedrooms').val();
                    d.min_price = $('#min_price').val();
                    d.max_price = $('#max_price').val();
                    d.quarter = $('#quarter').val();
                    d.handover_year = $('#handover_year').val();
                    d.payment_plan = $('#payment_plan').val();
                    d.assigned_agents = $('#assigned_agents').val();
                    d.page_status = 'manage_project'
                }
            },
            columns: [{
                    data: 'rf_no',
                    name: 'rf_no'
                },
                {
                    data: 'assign_to',
                    name: 'assign_to'
                },
                {
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'developer_company',
                    name: 'developer_company'
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
                    data: 'payment_plan_comments',
                    name: 'payment_plan_comments'
                },
                {
                    data: 'commission',
                    name: 'commission'
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

    $(document).on('click', '.delete-confirm', function () {
        var id = $(this).attr('data-listid');
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
                    url: "{{route('deleteProject')}}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (results) {
                        if (results.status) {
                            swal({
                                title: "Done!",
                                text: results.message,
                                type: "success"
                            }).then(function() {
                                $('.datatableManageProject').DataTable().draw(true);
                            });
                        } else {
                            swal("Error!", results.message, "error");
                        }
                    }
                });
            } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })
    });

    $(document).on('click', '.readyclick', function () {
        var id = $(this).attr('data-listid');
        $.ajax({
            url: "{{route('moveToReadyProject')}}",
            type: "POST",
            data: {
                id: id,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (results) {
                if (results.status) {
                    swal({
                        title: "Done!",
                        text: results.message,
                        type: "success",
                        timer: 800
                    }).then(function() {
                        $('.datatableManageProject').DataTable().draw(true);
                    });
                } else {
                    swal("Error!", results.message, "error");
                }
            }
        });
    });

    $(document).on('click', '.soldoutclick', function () {
        var id = $(this).attr('data-listid');
        $.ajax({
            url: "{{route('moveToSoldOutProject')}}",
            type: "POST",
            data: {
                id: id,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function (results) {
                if (results.status) {
                    swal({
                        title: "Done!",
                        text: results.message,
                        type: "success",
                        timer: 800,
                        width: 1000
                    }).then(function() {
                        $('.datatableManageProject').DataTable().draw(true);
                    });
                } else {
                    swal("Error!", results.message, "error");
                }
            }
        });
    });

    $('#submitAddUpdate').click(function (event) {
        event.preventDefault()

        var myform = document.getElementById("addAssignProject");
        var formData = new FormData(myform);
        formData: $(this).serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addAssignProject')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#project_id').val('');
                    $('#project_name').val('');
                    $('#agent_id').val('');
                    $('#assignProjectModel').modal('hide');
                    $('.datatableManageProject').DataTable().draw(true);
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
@endsection
