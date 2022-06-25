@extends('layouts.admin-app')
@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">View Client</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <span>{{$lead->name}}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Phone</label>
                        <div class="col-sm-10">
                            <span>{{$lead->phone}}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <span>{{$lead->email}}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Note</label>
                        <div class="col-sm-10">
                            <span>{{$lead->note}}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Project</label>
                        <div class="col-sm-10 example">
                            @foreach($projects as $key=> $project)
                                {{$key+1}}. {{ $project }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div id="note_table">
                        @if(!$lead->notes->isEmpty())
                            <div class="card mt-3">
                                <div class="card-header header">
                                    <p class="note desc m-0">Note</p>
                                </div>
                                <div class="card-body" style="overflow-x:auto;">
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <td>DATE ADDED</td>
                                            <td>NOTE</td>
                                            <td>CONTROLS</td>
                                        </tr>
                                        @foreach($lead->notes as $value)
                                            <tr>
                                                <td>{{$value->updated_at}}</td>
                                                <td>{{$value->note}}</td>
                                                <td>
                                                    <a href='javascript:void(0)' class='note-delete'
                                                       onclick='removeNote(this)'
                                                       data-id="{{$value->id}}"><i style='color: red'
                                                                                   class='fas fa-trash-alt'
                                                                                   data-toggle='tooltip'
                                                                                   data-placement='bottom'
                                                                                   title='Delete'></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    @if($lead->reminder()->exists())
                        <div id="reminder_div">
                            <div class="card mt-3">
                                <div class="card-header header">
                                    <p class="property desc ">Reminder</p>
                                </div>
                                <div class="card-body ">
                                    <caption>Reminder/FollowUp</caption>
                                    <table class="table table-striped">
                                        <thead>
                                        <th>Comment</th>
                                        <th>Date</th>
                                        <th>Done?</th>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                {{$lead->reminder->comment}}
                                            </td>
                                            <td>
                                                {{date('d-M-Y',strtotime($lead->reminder->reminder_date))}}
                                            </td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                           id="customSwitch{{$key}}" data-id="{{$lead->reminder->id}}"
                                                           onclick="fn_project_reminder_status_changes(this)"
                                                           value="1" {{($lead->reminder->status)?'checked':''}}>
                                                    <label class="custom-control-label"
                                                           for="customSwitch{{$key}}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <caption>Reminder History</caption>
                                    <table class="table table-striped">

                                        <tbody>
                                        @if($lead->reminder->reminder_date <= \Carbon\Carbon::now()->toDateString() && $lead->reminder->status == 0)
                                            <?php
                                            $class = "red";
                                            ?>
                                            <tr style="color:{{$class}};">
                                                <td colspan="2">AddedOn
                                                    @if ($lead->reminder->created_at)
                                                        {{\Carbon\Carbon::parse($lead->reminder->created_at)->format('d-M-Y g:i A')}}
                                                        @if($lead->reminder->reminder_date < \Carbon\Carbon::now()->toDateString() )
                                                            <button class="btn-sm bg-danger border-dark">Date Time
                                                                gone
                                                            </button>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Reminder Title:</td>
                                                <td>@if ($lead->reminder->title)
                                                        {{$lead->reminder->title}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Reminder Date:</td>
                                                <td>@if ($lead->reminder->reminder_date)
                                                        {{date('d-M-Y',strtotime($lead->reminder->reminder_date))}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Comment:</td>
                                                <td>@if ($lead->reminder->comment)
                                                        {{$lead->reminder->comment}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <!-- </table> -->
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
    <script type="text/javascript">
        function removeNote(e) {
            var id = $(e).data("id");
            $.ajax({
                url: '{{route('remove-lead-note')}}',
                method: "GET",
                data: {
                    'id': id
                },
                beforeSend: function () {
                    return confirm("Are you sure?");
                },
                success: function (data) {
                    if (data.status == 1) {
                        $("#note_table").load(location.href + " #note_table>*", "");
                    } else {
                        swal("ERROR!", data.message, "error");
                    }
                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                }
            });
        }

        function fn_project_reminder_status_changes(e) {
            var id = $(e).data("id");
            var value = $(e).is(':checked');
            $.ajax({
                url: "{{route('update_reminder_status')}}",
                method: "GET",
                data: {'id': id, 'value': value},
                beforeSend: function () {
                    return confirm("Are you sure?");
                },
                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");
                        $("#reminder_div").load(location.href + " #reminder_div>*", "");
                    } else {
                        swal("ERROR!", data.message, "error");
                    }

                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                }
            });
        }
    </script>
@endsection
