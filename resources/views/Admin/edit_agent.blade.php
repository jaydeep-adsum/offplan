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
                <h3 class="card-title">Edit Agent Detail</h3>
            </div>
            <form class="form-horizontal" method="POST" action="{{route('submit_edit_agent' , ['id' => $data['id']])}}"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    @csrf
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="firstname" class="form-control mr-2"
                                placeholder="Enter firstname  " value="@isset($name[0]){{ $name[0] }} @endisset"
                                style="width: 40%; float: left;">
                            <input type="text" value="@isset($name[1]){{ $name[1] }} @endisset" name="lastname"
                                class="form-control" placeholder="Enter  lastname " style="width: 40% ;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" name="email" class="form-control" value="{{$data->email}}"
                                placeholder="Enter email" style="width: 81%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="password" placeholder="Enter Password"
                                style="width: 81%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Phone</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="phone" value="{{ $data->phone }}"
                                placeholder="Enter phone" style="width: 81%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Profile</label>
                        <div class="col-sm-10">
                            <input type="file" name="image" style="width: 81%;">
                        </div>
                    </div>
                    <div class="form-group row">
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
                                    @foreach ($permission_role_mapping as $item)
                                        <tr>
                                            <td>
                                                {{$item->permission->permission_name}}
                                            </td>
                                            <td>
                                                <input type="checkbox" value="1" @if($item->read) checked @endif
                                                name="permission_array[{{$item->permission->id}}][read]">
                                            </td>
                                            <td>
                                                <input type="checkbox" value="1" @if($item->create) checked
                                                @endif name="permission_array[{{$item->permission->id}}][create]">
                                            </td>
                                            <td>
                                                <input type="checkbox" value="1" @if($item->update) checked
                                                @endif name="permission_array[{{$item->permission->id}}][update]">
                                            </td>
                                            <td>
                                                <input type="checkbox" value="1" @if($item->delete) checked
                                                @endif name="permission_array[{{$item->permission->id}}][delete]">
                                            </td>
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
@endsection
