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
                            <h1 class="m-0 text-dark">Milestones</h1>
                        </div>
                    </div>
                </div>
            </div>
            @if($permission)
                @if($permission->create)
                    <div class="container">
                        <form id="addMilestoneForm" autocomplete="off">
                            <div class="input-group mb-3">
                                @csrf
                                <input type="text" class="form-control milestone" required name="milestone" placeholder="Enter Milestones" aria-label="Enter Machine" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit" id="addMilestone">Submit</button>
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
    <div class="col-12">
        <div class="card">
            <div style="padding: 10px 10px 10px 10px">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Milestones</th>
                            <th>Operations</th>
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
    $(document).ready(function () {
        let table = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('listmilestonesDatatable')}}",
                type: "POST",
                data: function (d) {
                    d._token = '{{csrf_token()}}';
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'milestone',
                    name: 'milestone'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ],
            createdRow: function (row, data, index) {
                if (data['action']=="-") {
                    table.column(2).visible( false );
                }
            },
        });
    });

    $('#addMilestone').click(function (event) {
        event.preventDefault()
        let formData = $("#addMilestoneForm").serialize();
        $.ajax({
            type: "POST",
            url: "{{ route('add-milestones')}}",
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
                            $('.milestone').val('');
                        });
                } else {
                    swal("ERROR!", data.message, "error");
                }
            },
        });
    });

     $(document).on('click', '.delete-confirm', function () {
        var id = $(this).attr('data-milestone_id');
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
                    url: "{{route('delete-milestones')}}/" + id,
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
                                timer:800
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

