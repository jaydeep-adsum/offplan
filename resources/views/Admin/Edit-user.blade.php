@extends('layouts.admin-app')

@section('content')
<section class="content">

    <div class="container-fluid py-2">
        @if($response = session('response'))
        <div class="alert @if($response['status']) alert-success @else alert-danger @endif">
            {{ $response['message'] }}
        </div>
        @endif
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Edit Developer Detail</h3>
            </div>
            <form class="form-horizontal" id="frmTarget" method="POST" action="#"
                enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Company Name</label>
                        <div class="col-sm-5">
                            <input type="text" name="company" value="{{ $data->company }}" class="form-control"
                                placeholder="Enter Company Name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="" class="col-form-label d-block">Point of Contact</label>
                            <button class="btn btn-info add-row" type="button"><i class="fas fa-user-plus"></i></button>
                        </div>
                        <div class="col-sm-10">
                            <div class="row abc">
                                @foreach ($data['multiplecontact'] as $key => $item)
                                <input type="hidden" name="multiplecontact[{{$key}}][id]" value="{{$item->id}}">
                                <div data_contact="{{$key}}" class="col-sm-6 contact">
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <h6 class="text-center">Contact {{$i=$key+1}}</h6>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-center"><a
                                                    href="{{ route('delete-contact', ['id' => $item['id']]) }}"
                                                    onclick="return confirm('Are you sure?')"><i style='color: red'
                                                        class="fas fa-trash-alt"></i></a></h6>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="text" class="form-control mb-2" value="{{ $item->person }}"
                                                name="multiplecontact[{{$key}}][person]"
                                                placeholder="Enter Name Person">
                                        </div>
                                        <div class="col-12">
                                            <input type="number" class="form-control" value="{{ $item->phone }}"
                                                name="multiplecontact[{{$key}}][phone]" placeholder="Enter phone">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Note</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="note" placeholder="Enter Name Note"
                                rows="5">{{$data->note}}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-5">
                            <input type="email" class="form-control" value="{{ $data->email }}" name="email"
                                placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Expiry Date</label>
                        <div class="col-sm-5">
                            <input type="date" class="form-control" name="date" value="{{ $data->date }}">
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{$data->id}}">
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Agency Agreement</label>
                        <div class="col-sm-5">
                            <input type="file" name="pdf[]" id="files" accept="application/pdf"
                                multiple class="mt-3" />
                        </div>
                    </div>
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-12">
                                <div id="image_preview" class="row">
                                    <?php 
                                        $imagelist=json_decode($data['pdf']);
                                        $id=$data['id'];  
                                        $i=0;
                                        $test =array();
                                    ?> 
                                    @if($imagelist)
                                    @foreach($imagelist as $image)
                                        <?php
                                        $abc['File']=array("name"=>$image);
                                        array_push($test,$abc);
                                        ?>
                                        <div class='upload-image col-2 text-center' title="{{$image}}" id ="{{$image}}">
                                        <a  href="{{asset('files/developer/'.$image)}}" data_id="{{$i}}" target="_blank">@php ($pdf = explode('-', $image))@endphp
                                        {{$pdf[1]}}</a>
                                        <span class="remove btn btn-danger" onclick="removeitem(this)">&times; </span>
                                        </div>
                                        <?php $i++;?>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-12">
                            <div id="image_preview" class="row"></div>
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
    $(".add-row").click(function () {
        var index = $(".contact").last().attr("data_contact") ?? 0;
        index++;
        var add =
            "<div class='col-sm-6 mb-2 contact' data_contact=" + index +
            "><div class='row'><div class='col-12'><hr><h6 class='text-center'>Contact " + (index + 1) +
            "</h6><hr><input type='text' class='form-control mb-2' name='multiplecontact[" + index +
            "][person]' placeholder='Enter Name Person'></div><div class='col-12'><input type='number' class='form-control' name='multiplecontact[" +
            index +
            "][phone]' placeholder='Enter phone'></div></div></div>"
        $(".abc").append(add);
    });

</script>
<script type="text/javascript">

    var lastcount = $("#image_preview div a").last().attr('data_id');
    var imgArray = [];

    lastcount=parseInt(lastcount)+1; 

    "<?php foreach($test as $value) { ?>"
    var f = new File([""], "<?php echo $value['File']['name']; ?>");
    imgArray.push(f);
    "<?php } ?>"

    
    document.getElementById("files").onchange = function (event) {
        if(isNaN(lastcount)){
            lastcount=0;
        }
        var files = event.target.files;
        for (var i = 0; i < files.length; i++) {
            var f = files[i];
            html = "<div class='upload-image col-2 text-center'><a class='img-responsive' data_id=" + (lastcount) + ">"+f.name+"</a><span class=\"remove btn btn-danger\" onclick='removeitem(this)'>&times; </span></div>"
            $("#image_preview").append(html);

            imgArray.push(document.getElementById("files").files[i]);
            lastcount++;
        }
    }

    function removeitem(el) {
        var index = $(el).parent(".upload-image").find("a").attr("data_id");

        imgArray.splice(index, 1);
        $(el).parent(".upload-image").remove();
        for (let j = 0; j < $("#image_preview").find('.upload-image').length; j++) {
            const element = $("#image_preview").find('.upload-image')[j];
            $(element).find('a').attr('data_id', j);
        }
        lastcount--;
    }
    
</script>


<script type="text/javascript">
    $(document).ready(function () {
        $('#submit-all').click(function (event) {
            event.preventDefault()
            var myform = document.getElementById("frmTarget");
            var formData = new FormData(myform);
            formData:$(this).serialize();

            imgArray.forEach(function(el,i){
                formData.append('pdf['+i+']', el);
            });

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
                url: "{{route('submit-edit-user')}}",
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
