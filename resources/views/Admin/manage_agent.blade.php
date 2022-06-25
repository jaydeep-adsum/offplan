@extends('layouts.admin-app')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Agent List</h3>
                    </div>
                    <div class="card-header">
                        <h5 class="card-header-text"></h5>
                        <span class="card-header-right-span"><a href="{{route('add_agent')}}" class="btn btn-info "><i
                                    class="fa fa-plus"></i> Add User</a></span>
                    </div>

                    <div class="card-body">
                        <div class="table">
                            <table id="example2"
                                class="table table-striped table-hover datatable"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Phone</th>
                                        <th>Image </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userData as $user)
                                    <tr>
                                        <td>{{$user['name']}} </td>
                                        <td>{{$user['email']}}</td>
                                        <td>{{$user['role']==2?'Agent':'Associate'}}</td>
                                        <td> {{$user['phone']}}</td>
                                        <td>
                                            <img src="{{asset('public/files/profile/'.json_decode($user['image']))}}"
                                                style="height:120px; width:100px" />
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ route('edit_agent', ['id' => $user['id']]) }} "><i
                                                            class="fas fa-edit"> </i> </a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{route('delete_agent', ['id' => $user['id']]) }}"
                                                        onclick="return confirm('Are you sure?')"> <i style="color: red"
                                                            class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.datatable').DataTable({

        });
    });
</script>
@endsection
