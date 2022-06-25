@extends('layouts.admin-app')
@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        @if($response = session('response'))
        <div class="alert @if($response['status']) alert-success @else alert-danger @endif">
            {{ $response['message'] }}
        </div>
        @endif
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Add Agent Detail</h3>
            </div>
            <form class="form-horizontal" method="POST" action="{{route('submit_add_agent')}}"
                enctype="multipart/form-data">
                <div class="card-body">@csrf
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="firstname" class="form-control mr-1" placeholder="Enter Firstname"
                                style="width: 40%; float: left;">
                            <input type="text" style="width: 40%;" name="lastname" class="form-control"
                                placeholder="Enter Lastname">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" name="email" class="form-control" placeholder="Enter email"
                                style="width: 80%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="password" placeholder="Enter Password"
                                style="width: 80%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Phone</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="phone" placeholder="Enter phone"
                                style="width: 80%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">User Role</label>
                        <div class="col-sm-8">
                            <select class="custom-select" name="role" id="role">
                                <option value="2">Agent</option>
                                <option value="3">Associate</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Profile</label>
                        <div class="col-sm-10">
                            <input type="file" name="image" style="width: 80%;">
                        </div>
                    </div>
                    <div class="form-group row" id="permission">
                        <label class="col-sm-2 col-form-label">Permission</label>
                        <div class="col-sm-10">
                            <table class="table table-hover" style="width: 600px">
                                <thead>
                                    <th>Permission List</th>
                                    <th>Read</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </thead>
                                <tbody>
                                    @foreach ($permission as $item)
                                    <tr>
                                        <td>{{$item->permission_name}}</td>
                                        <td><input type="checkbox" value="1" name="permission_array[{{$item->id}}][read]"></td>
                                        <td><input type="checkbox" value="1" name="permission_array[{{$item->id}}][create]"></td>
                                        <td><input type="checkbox" value="1" name="permission_array[{{$item->id}}][update]"></td>
                                        <td><input type="checkbox" value="1" name="permission_array[{{$item->id}}][delete]"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-info waves-effect waves-light m-r-30">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript">
        if($('#role').val()==3){
            $('#permission').hide();
        }
            $(document).on('change', '#role', function () {
                if(this.value==3){
                    $('#permission').hide();
                } else {
                    $('#permission').show();
                }
            });
    </script>
@endsection
