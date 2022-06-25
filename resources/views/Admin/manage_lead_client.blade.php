@extends('layouts.admin-app')
@section('content')

@if($data)
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                    @if($response = session('response'))
                        <div class="alert @if($response['status']) alert-success @else alert-danger @endif"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ $response['message'] }}
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-header-text"></h5>
                            <span class="card-header-right-span"><a href="{{route('lead_create')}}" class="btn btn-info "><i class="fa fa-plus"></i> Add Lead Client</a></span>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                <table class="table table-striped table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Updated On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                        <tr class="{{(isset($item->reminder) && $item->reminder->reminder_date <= Carbon\Carbon::now()->toDateString() && $item->reminder->status ==0)?'text-danger':''}}">
                                            <td>
                                                @if($item->name)
                                                    {{$item->name}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->phone)
                                                    {{$item->phone}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->email)
                                                    {{$item->email}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->updated_at)
                                                    {{date('d-M-Y', strtotime($item->updated_at))}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="row">
                                                     <div class="col-2">
                                                        <a href="{{ route('view_lead', ['id' => $item['id']]) }}"><i class="fas fa-eye" data-toggle="tooltip" data-placement="bottom" title="Preview"></i></a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a href="{{ route('lead_report', ['id' => $item['id']]) }}"><i class="fas fa-file-export" data-toggle="tooltip" data-placement="bottom" title="Report"></i></a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a class="user_dialog" data-toggle="modal" data-target="#imageModal" data-listid="{{$item['id']}}"><i style="color: #0080ff" class="fas fa-sticky-note mt-2" data-toggle="tooltip" data-placement="bottom" title="Note"></i></a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a href="{{ route('lead_edit', ['id' => $item['id']]) }}"><i class="fas fa-edit" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a class="reminder" data-toggle="modal" data-target="#reminderModel"  data-listid="{{$item['id']}}"><i style="color: #9400D3" class="fas fa-clock mt-2" data-toggle="tooltip" data-placement="bottom" title="Reminder"></i></a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a href="{{ route('lead_destroy', ['id' => $item['id']]) }}" onclick="return confirm('Are you sure?')"><i style="color: red" class="fas fa-trash-alt" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                        <input type="hidden" name="client_id" value="" id="client_id">
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-all" class="btn btn-primary">Save Note</button>
                    </form>
                    <table class="table">
                        <thead class="thead-dark">
                        <tr>
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
    <div class="modal fade" id="reminderModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
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
                        <input type="hidden" name="client_id" value="" id="rem_client_id">
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
@endif
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    $(document).on("click", ".user_dialog", function () {
        var client_id = $(this).data('listid');
        $(".modal-body #client_id").val(client_id);
    });
    $(document).on("click", ".reminder", function () {
        var rem_client_id = $(this).data('listid');
        $("#rem_client_id").val(rem_client_id);
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
            url: "{{route('lead-note')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#note').val('');
                    if (data.note) {
                        var table = '';
                        var note = data.note;
                        table += "<tr>";
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
    function removeNote(value, id) {
        $.ajax({
            url: '{{route('remove-lead-note')}}',
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
                    if (notelist.length) {
                        var table = '';
                        for (var i = 0; i < notelist.length; i++) {
                            var note = notelist[i];
                            table += "<tr>";
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
    $('#submit').click(function (event) {
        event.preventDefault()

        var myform = document.getElementById("reminderForm");
        var formData = new FormData(myform);
        formData: $(this).serialize();
        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // });
        $.ajax({
            type: "POST",
            url: "{{route('store-reminder')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#client_id').val('');
                    $('#title').val('');
                    $('#remiderdate').val('');
                    $('#comment').val('');
                    $('#reminderModel').modal('hide');
                    // $('.datatableManageListings').DataTable().draw(true);
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
