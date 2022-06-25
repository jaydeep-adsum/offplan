@extends('layouts.admin-app')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Login History List</h3>
                        </div>

                        <div class="card-body">
                            <div class="table">
                                <table id="example2"
                                       class="table table-striped table-hover datatable"
                                       cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Last Login</th>
                                        <th>Last Logout</th>
                                        <th>Last 7 days</th>
                                        <th>Last 30 days</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($monitoringData as $monitoring)
                                        <tr>
                                            <td>{{$monitoring['name']}} </td>
                                            <td>{{$monitoring['last_login']}}</td>
                                            <td>{{$monitoring['last_logout']?$monitoring['last_logout']:'NULL'}}</td>
                                            <td>{{ date('H:i:s',strtotime($monitoring['total_seven_time']))}}</td>
                                            <td>{{ date('H:i:s',strtotime($monitoring['total_thirty_time']))}}</td>
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
