@extends('layouts.admin-app')
@section('content')
<section class="content pt-3">
    <div class="col-12">
        @if($response = session('response'))
        <div class="alert @if($response['status']) alert-success @else alert-danger @endif">
            {{ $response['message'] }}
        </div>
        @endif
        <div class="card">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Community</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Community</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if($permission)
                @if($permission->create)
                    <div class="container">
                        <form id="frmTarget" autocomplete="off">
                            @csrf
                            <div class="form-group row">
                                <label for="" class="col-sm-2 col-form-label mt-3">Community</label>
                                <div class="col-sm-5">
                                    <div class="input-group mt-3">
                                        <input type="text" class="form-control" name="name" id="community"
                                            placeholder="Enter Community" aria-label="Enter Community"
                                            aria-describedby="basic-addon2">
                                        <input type="hidden" name="id" id="id" value="">
                                    </div>
                                    <button class="col-sm-4  mt-3 btn btn-primary" type="submit" id="submit-all">Submit</button>
                                    <button class="col-sm-4  mt-3  btn btn-secondary reset" type="button">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title m-0 text-dark">Community List</h1>
                    </div>
                    <div class="card-body">
                        <table id="example2" style="font-size: .9rem" class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var table = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('managecommunityDatatable')}}",
                type: "POST",
                data: function (d) {
                    d._token = '{{csrf_token()}}';
                }
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ],
            createdRow: function (row, data, index) {
                if (data['action']=="-") {
                    table.column(1).visible( false );
                }
            },
        });


        $('#submit-all').click(function (event) {
            event.preventDefault()
            var formData = $("#frmTarget").serialize();
            $.ajax({
                type: "POST",
                url: "{{route('add-community')}}",
                data: formData,
                success: function (data) {
                    if (data.status == 1) {
                        swal({
                            title: "Done!",
                            text: data.message,
                            type: "success",
                            timer: 700
                        }).then(function () {
                            $('.datatable').DataTable().draw(true);
                            $('#community').val('');
                        });
                    } else {
                        swal("ERROR!", data.message, "error");
                    }
                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                    console.log(data);
                },
            });
        });
    });


    $(document).on('click', '.reset', function () {
        $('.datatable').DataTable().draw(true);
        $('#community').val('');
    });
    
    $(document).on('click', '.edit-community', function () {
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        $('#community').val(name);
        $('#id').val(id);
    });

    $(document).on('click', '.delete-confirm', function () {
        var id = $(this).attr('data-community_id');
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
                    url: "{{route('delete-community')}}/" + id,
                    type: "POST",
                    data: {
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
                            }).then(function () {
                                $('.datatable').DataTable().draw(true);
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

</script>
@endsection
