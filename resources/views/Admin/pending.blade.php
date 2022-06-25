@extends('layouts.admin-app')
@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pending Developer List</h3>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            <div class="input-group col-sm-3">
                                <input type="text" name="company" id="company" placeholder="Company Name" class="form-control" autocomplete="off">
                            </div>
                            <div class="input-group col-sm-3">
                                <input type="text" name="date_range" id="date_range" placeholder="Date From - Date to" class="form-control datepicker" autocomplete="off">
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary" type="button" id="searchData">Search</button>
                                <button class="btn btn-secondary" type="button" id="resetSearchData">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table style="font-size: .9rem" class="table table-striped table-hover datatableDeveloperList">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Point of Contact</th>
                                    <th>Mobile No</th>
                                    <th>Expiry Date</th>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.datepicker').daterangepicker({
            locale: {
                format: 'YYYY/MM/DD'
            }
        });
        $('#date_range').val('');

        $('input[name="date_range"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    });

    $(document).on("click", "#searchData", function () {
        $('.datatableDeveloperList').DataTable().draw(true);
    });

    $(document).on("click", "#resetSearchData", function () {
        $('#company').val('');
        $('#date_range').val('');
        $('.datatableDeveloperList').DataTable().draw(true);
    });

    $(function () {
        var table = $('.datatableDeveloperList').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('datatableDeveloperList')}}",
                type: "POST",
                data: function (d) {
                    d._token = '{{csrf_token()}}',
                        d.company = $('#company').val(),
                        d.date_range = $('#date_range').val(),
                        d.page_status = 'pending_contracts'
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'point_of_contact',
                    name: 'point_of_contact'
                },
                {
                    data: 'mobile_no',
                    name: 'mobile_no'
                },
                {
                    data: 'expiry_date',
                    name: 'expiry_date'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
    });

    $(document).on('click', '.delete-confirm', function () {
        var developer_id = $(this).attr('data-developer_id');
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
                    url: "{{route('deleteDeveloper')}}",
                    type: "POST",
                    data: {
                        developer_id: developer_id,
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
                            }).then(function () {
                                $('.datatableDeveloperList').DataTable().draw(true);
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
