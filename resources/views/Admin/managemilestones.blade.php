@extends('layouts.admin-app')

@section('content')
   <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Milestones List</h3>

              </div>
              <div class="card-header">
                <h5 class="card-header-text"></h5>

                <span class="card-header-right-span"><a href="{{  route('add-milestones')}}" class="btn btn-info "><i class="fa fa-plus"></i> Add Milestones</a></span>

              </div>
              {{-- <div class="card-header">
                <form class="form-inline ml-3" method="post"  id="Search" >
                  @csrf

                  <div class="row">
                  <div class="input-group col-3 mb-3">
                    <input class="form-control mr-3 select-company" id="company" type="text" placeholder="Company Name" name="company">
                  </div>
                    <div class="input-group mb-3 col-3">
                      <input type="date"  name="min_date" class="form-control mr-3 select-min-date">
                    </div>
                    <div class="input-group mb-3 col-3">
                      <input type="date"  name="max_date" class="form-control mr-3 select-max-date">
                    </div>
                    <br>



                    <div class="col-3 ">
                    <button class="btn btn-primary mr-3" type="button" onclick="searchData()">Search</button>
                      <!-- <button class="btn btn-secondary" type="button" onclick="document.getElementById('Search').reset(); document.getElementById('Search').value = null; return false; ">Reset</button> -->
                      <button class="btn btn-secondary" type="button" onclick="window.location.reload(); ">Reset</button>
                  </div>

                </div>


              </form>
              <!--  <button onclick="document.getElementById('Search').reset(); document.getElementById('Search').value = null; return false; "><i class="fas fa-undo"></i></button> -->

              </div> --}}



              <!-- /.card-header -->
              <div class="card-body">
                <table id="#" class="table table-bordered table-hover datatable">
                  <thead>
                  <tr>
                    <!-- <th>Reference No</th> -->
                    <th>No.</th>
                    <th>Company Name</th>
                    <th>Point of Contact</th>
                    <th>Mobile No</th>
                    <th>Expiry Date</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody id="fetchdata">
                  {{-- @foreach($user as $key => $user) --}}
                  <tr>
                    {{-- <td>{{ $key+1 }} </td> --}}
                    {{-- <td>{{$user['company']}}</td> --}}
                    {{-- <td> {{$user['person']}}</td> --}}
                    {{-- <td> {{$user['phone']}}</td> --}}
                    {{-- <td> {{$user['date']}}</td> --}}
                    {{-- <td>

                      <img src="{{asset('/files/profile/'.json_decode($user['image']))}}" style="height:120px; width:100px"/>
                    </td> --}}
                    <td>
                        {{-- <a href="{{ route('edit-user', ['id' => $user['id']]) }} "><i class="fas fa-edit mr-3"></i> </a> --}}
                        {{-- <a href="{{route('delete-user', ['id' => $user['id']]) }}" onclick="return confirm('Are you sure?')"> <i style='color: red' class="fas fa-trash-alt"></i></a> --}}

                    </td>
                  </tr>
                  {{-- @endforeach --}}
                  </tbody>
                  <tfoot>
                  <!-- <tr>
                    <th>Rendering engine</th>
                    <th>Browser</th>
                    <th>Platform(s)</th>
                    <th>Engine version</th>
                    <th>CSS grade</th>
                  </tr> -->
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var table = $('.datatable').DataTable( {
        scrollCollapse: true,
        autoWidth:       true,
      "searching": false,
    } );
} );

function searchData(){

      var company = $('.select-company').val();

      var min_date = $('.select-min-date').val();
      var max_date = $('.select-max-date').val();
      var senddata = {
                       'company' : company,
                       'min_date' : min_date,
                       'max_date' : max_date
                    }
  $.ajax({
    url: '{{route('search-list')}}',
    method: "GET",
    data: senddata,
    dataType: "json",
    cache: false,
    success: function (data) {
    $('#fetchdata').empty();
      console.log(data);

      var fetchdata = '';
          $.each(data,function(index,data){
          fetchdata+='<tr>';
                fetchdata+='<td>'+ (index++ + 1) +'</td>';
                fetchdata+='<td>'+ data.company +'</td>';
                fetchdata+='<td>'+ data.person +'</td>';
                fetchdata+='<td>'+ data.phone +'</td>';
                fetchdata+='<td>'+ data.date +'</td>';

                var editurl = '{{ route("edit-user", ":id") }}';
                editurl = editurl.replace(':id', data.id);

                var deleteurl = '{{ route("delete-user", ":id") }}';
                deleteurl = deleteurl.replace(':id', data.id);

                fetchdata+='<td>'+
                "<a class='mr-3' href='"+editurl+"' target='_blank'> <i class='fas fa-edit'></i></a> <a href='"+deleteurl+"  onclick='return confirm('Are you sure?')'><i  style='color: red'  class='fas fa-trash-alt'></i></a>"
                +'</td>';
          fetchdata+='</tr>';
        });
        $('#fetchdata').append(fetchdata);
      $('#fetchdata').append(senddata);
    },
    error: function (er){
      console.log(er);
    }
});
}


</script>
@endsection
