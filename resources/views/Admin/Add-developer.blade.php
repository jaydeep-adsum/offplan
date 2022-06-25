@extends('layouts.admin-app')
@section('content')
<style>
    .message{
    text-align: left;
    padding-left: 30px;
    }
</style>
<section class="content pt-3">
    <div class="container-fluid">
        @if($response = session('response'))
        <div class="alert @if($response['status']) alert-success @else alert-danger @endif"><a href="#" class="close"
                data-dismiss="alert" aria-label="close">&times;</a>
            {{ $response['message'] }}
        </div>
        @endif
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Developer Contact</h3>
            </div>
            <form class="form-horizontal" method="POST" id="frmTarget" enctype="multipart/form-data">
                <div class="card-body">@csrf
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Company Name</label>
                        <div class="col-sm-5">
                            <input type="text" name="company"  class="form-control"
                                placeholder="Enter Company Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="" class="col-form-label d-block">Point of Contact</label>
                            <button class="btn btn-info add-row" type="button"><i class="fas fa-user-plus"></i></button>
                        </div>
                        <div class="col-sm-10">
                            <div class="row abc">
                                <div class="col-sm-6">
                                    <hr><h6 class='text-center'>Contact 1</h6><hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="text" class="form-control mb-2" name="multiplecontact[0][person]"
                                                placeholder="Enter Name Person">
                                        </div>
                                        <div class="col-12">
                                            <input type="number" class="form-control" min="0" name="multiplecontact[0][phone]"
                                                placeholder="Enter Phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Note</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="note"
                                placeholder="Enter Name Note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-5">
                            <input type="email" class="form-control" name="email"
                                placeholder="Enter Email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Expiry Date</label>
                        <div class="col-sm-5">
                            <input type="date" class="form-control" name="date" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Agency Agreement</label>
                        <div class="col-sm-5">
                            <input type="file" name="pdf[]" accept=".pdf" required multiple />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <button type="submit" id="submit-all" class="btn btn-info">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
    var index = 0;
    $(".add-row").click(function () {        
        index++;
        var add =
            "<div class='col-sm-6 mb-2'><div class='row'><div class='col-12'><hr><h6 class='text-center'>Contact "+ (index + 1) +"</h6><hr><input type='text' class='form-control mb-2' name='multiplecontact["+ index +"][person]' placeholder='Enter Name Person'></div><div class='col-12'><input type='number' class='form-control' min='0' name='multiplecontact["+ index +"][phone]' placeholder='Enter Phone'></div></div></div>"
        $(".abc").append(add);
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#submit-all').click(function (event) {
            event.preventDefault()
            var myform = document.getElementById("frmTarget");
            var formData = new FormData(myform);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            window.swal({
            title: "Checking...",
            text: "Please wait",
            imageUrl: "{{ asset('public/ajaxloader/ajaxloader.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
            });
            $.ajax({
                type: "POST",
                url: "{{route('submit-add-user')}}",
                data: formData,
                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");       
                        window.location.href = "{{route('manage-user')}}";
                    } else {
                        swal("ERROR!",'<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                    }
                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                    console.log(data);
                },
                cache: false,
                contentType: false,
                processData: false
            });    
        });
    });
</script>
@endsection
