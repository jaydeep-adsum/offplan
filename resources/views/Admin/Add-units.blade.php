@extends('layouts.admin-app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .message{
        text-align: left;
        padding-left: 55px;
    }
</style>
<script type="text/javascript">
    function setvalue() {
        localStorage.clear();
        localStorage.setItem('click_unit', 'yes');
        var project = document.getElementById("project").value;
        var bedrooms = document.getElementById("bedrooms").value;
        var bathrooms = document.getElementById("bathrooms").value;
        var size = document.getElementById("size").value;
        var price = document.getElementById("price").value;
        var title = document.getElementById("title").value;
        var description = CKEDITOR.instances.description.getData();
        var payment_plan = document.getElementById("payment_plan").value;

        var pre_handover_amount = document.getElementById("pre_handover_amount").value;
        var handover_amount = document.getElementById("handover_amount").value;
        var post_handover_amount = document.getElementById("post_handover_amount").value;

        var installment = [];
        var installmentinput = document.getElementsByClassName("installment");
        for (let i = 0; i < installmentinput.length; i++) {
            const element = installmentinput[i];
            installment.push(element.value);

        }
        var milestone = [];
        var milestoneinput = document.getElementsByClassName("milestone");
        for (let i = 0; i < milestoneinput.length; i++) {
            const element = milestoneinput[i];
            milestone.push(element.value);

        }
        var percentage = [];
        var percentageinput = document.getElementsByClassName("percentage");
        for (let i = 0; i < percentageinput.length; i++) {
            const element = percentageinput[i];
            percentage.push(element.value);

        }
        var total_amount = [];
        var total_amountinput = document.getElementsByClassName("total_amount");
        for (let i = 0; i < total_amountinput.length; i++) {
            const element = total_amountinput[i];
            total_amount.push(element.value);

        }

        var arr = $('.featuresList').map(function() {
            return this.checked;
        }).get();

        localStorage.setItem("project_unit", project);
        localStorage.setItem("bedrooms_unit", bedrooms);
        localStorage.setItem("bathrooms_unit", bathrooms);
        localStorage.setItem("size_unit", size);
        localStorage.setItem("price_unit", price);
        localStorage.setItem("title_unit", title);
        localStorage.setItem("description_unit", description);
        localStorage.setItem("checked_unit", JSON.stringify(arr));
        localStorage.setItem("payment_plan_unit", payment_plan);

        localStorage.setItem("pre_handover_amount_unit", pre_handover_amount);
        localStorage.setItem("handover_amount_unit", handover_amount);
        localStorage.setItem("post_handover_amount_unit", post_handover_amount);

        localStorage.setItem("installment_unit", installment);
        localStorage.setItem("milestone_unit", milestone);
        localStorage.setItem("percentage_unit", percentage);
        localStorage.setItem("total_amount_unit", total_amount);

        if(imgArray.length)
        {
            var imageNameData = imgArray;
            localImageSaveUnit(imageNameData);
        }
        else
        {
            finalSubmit();
        }
        var local_image_id = document.getElementById("local_image_id").value;
        localStorage.setItem("local_image_id", local_image_id);
    }
</script>
@section('content')
    <div class="card">
        @if($response = session('response'))
            <div class="alert @if($response['status']) alert-success @else alert-danger @endif" style="margin-top:10px">
                <?php $error = explode(',', $response['message'])?>
                <ul>
                    @foreach ($error as $item)
                        <li>{{ $item  }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-header">
            <div class="container">
                <form class="form-horizontal" method="POST" id='frmTarget' enctype="multipart/form-data" autocomplete="off">
                    @if(Request::segment(1) == "manage-unit-status")
                        <input type="hidden" name="ready_status" value="1">
                    @endif
                    @if(Request::segment(1) == "manage-soldout-unit")
                        <input type="hidden" name="sold_out_status" value="1">
                    @endif

                    <input type="hidden" name="local_image_id" id="local_image_id">

                    <div class="form-row justify-content-between">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Project Name</label>
                                <select name="project" id="project" class="form-control select2" style="width: 100%">
                                    <option value="0">Select Project</option>
                                    @foreach ($project as $key => $item)
                                        <option value="{{$key}}">{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>No. Of Bedrooms</label>
                                <select class="form-control select2" id="bedrooms" name="bedrooms" style="width: 100%">
                                    <option selected disabled>Select Bedrooms</option>
                                    <option value="Studio">Studio</option>
                                    @for ($i = 1; $i <= 20; $i++) <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>No. Of Bathrooms</label><br>
                                <input type="number" class="form-control" id="bathrooms" name="bathrooms" min='0'
                                       placeholder="Enter Bathrooms">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Size</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" id="size" name="size" min='0'
                                           placeholder="Enter Size">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">Sq.Ft</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Price</label>
                                <div class="input-group mb-3 abc">
                                    <input type="number" class="form-control am" id="price" min='0' pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==8) return false;" onchange="rowcol(this)" name="price" min="1"
                                           placeholder="Enter Price">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">AED</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Title</label>
                            <textarea class="form-control" id="title" name="title" rows="3"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>

                    <div style="border:solid 1px #ddd; padding:10px;" id="cont">
                        <label><i class="fas fa-angle-double-right"></i> Features List</label><br>
                        <div class="grid-container row featuresListChecked">
                            @if($featuresList)
                                @foreach($featuresList as $featuresName)
                                    <div class="col-3">
                                        <input type="checkbox" class="featuresList" name="featuresList[]" value="{{$featuresName['fname']}}">
                                        <label for="vehicle1"> {{$featuresName['fname']}}</label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-3">
                            <button type="button" class="btn btn-outline-success form-control" data-toggle="modal"
                                    data-target="#imageModal">
                                Select Image
                            </button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-outline-success form-control" data-toggle="modal"
                                    data-target="#floorplan_model">
                                Select Floor Plan Image
                            </button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-outline-success form-control" data-toggle="modal"
                                    data-target="#video_model">
                                Select Video
                            </button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-outline-success form-control" data-toggle="modal"
                                    data-target="#pdf_model">
                                Select PDF OR Excel
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row justify-content-between">
                        <div class="col-5 mb-3">
                            <label>Select Payment Plan</label>
                            <select class="form-control select2" id="payment_plan" onchange="payment(this.value)" name="payment_plan" style="width: 100%">
                                <option selected disabled>Select Payment Plan</option>
                                <option value="Yes">YES</option>
                                <option value="No">NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="card show_hide" style="padding: 15px 15px 15px 15px;">
                        <div class="mb-1">
                            <label>1. Amounts to be Payed</label>
                            <hr>
                        </div>

                        <div class="form-row justify-content-between">
                            <div class="col-4 mb-3">
                                <div class="form-group">
                                    <label>Pre-Handover Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min='0' name="pre_handover_amount" id="pre_handover_amount"
                                               class="form-control" placeholder="Enter Pre-Handover Amount">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="form-group">
                                    <label>Handover Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min='0' name="handover_amount" id="handover_amount"
                                               class="form-control" placeholder="Enter Handover Amount">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="form-group">
                                    <label>Post-Handover Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min='0' name="post_handover_amount" id="post_handover_amount"
                                               class="form-control" placeholder="Enter Post-Handover Amount">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label>2. Milestone Section</label>
                        <div class="editRemove local-storage-save">
                            <div class='removeMilestone' data_milestone="0">
                                <hr>
                                <div class='row'>
                                    <div class='col-6'>
                                        <h6 class='text-center'>Milestone <span class='milestone_number'>1</span></h6>
                                    </div>
                                    <div class='col-6'>
                                        <h6 class='text-center'><a href='javascript:void(0)' style="visibility: hidden" onclick='deleteConfirmation(this)'>Delete</a></h6>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-between mile">
                                    <div class="col-5 mb-3">
                                        <label>Installment Terms</label>
                                        <input type="number" name="milestone[0][installment_terms]"
                                               class="form-control installment" placeholder="Enter Installment">
                                    </div>
                                    <div class="col-5 mb-3">
                                        <label>Payment Milestone</label>
                                        @if($milestoneData)
                                            <select class="form-control select2 milestone" name="milestone[0][milestones]" style="width: 100%">
                                                <option selected disabled>Select Milestone</option>
                                                @foreach($milestoneData as $key=>$milestone)
                                                    <option value="{{$milestone}}">{{$milestone}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="col-5 mb-3 abc">
                                        <label>Percentage</label>
                                        <input type="number" name="milestone[0][percentage]" onchange="precalc(this)"
                                               class="form-control per percentage" placeholder="Enter Percentage">
                                    </div>
                                    <div class="col-5 mb-3 abc">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="milestone[0][amount]" class="form-control total total_amount"
                                                       placeholder="Enter Amount">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">AED</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row button-row py-2">
                            <div class="col-12">
                                <button class="btn btn-info add-row" type="button">Add Milestone</button>
                            </div>
                        </div>
                    </div>

                    <!-- Image Modal -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1>Image</h1>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="input-group control-group increment">
                                        <input type="file" accept="image/*" name="image[]" id="files" required class="form-control"
                                               multiple>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="image_preview" class="row"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floor Plan Image Modal -->
                    <div class="modal fade" id="floorplan_model" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1>Floor Plan Image</h1>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="input-group control-group increment">
                                        <input type="file" accept="image/*" name="floorplanimage[]" id="floorplanimage"
                                               class="form-control" multiple>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="floorplanimage_preview" class="row"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Modal -->
                    <div class="modal fade" id="video_model" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1>Video</h1>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="input-group control-group increment">
                                        <input type="file" accept="video/*" name="video[]" id="in_video"
                                               class="form-control" multiple>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="video_preview" class="row"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PDF Modal -->
                    <div class="modal fade" id="pdf_model" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1>PDF OR Excel</h1>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="input-group control-group increment">
                                        <input type="file" accept="application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="pdf[]" id="pdf"
                                               class="form-control" multiple>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="pdf_preview" class="row"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="button" id="submit-all" name="submit" class="btn btn-info px-4"
                                onclick="setvalue();" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    {{-- <script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script> --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>

    <script>
        function localImageSaveUnit(imageNameData)
        {
            var formData = new FormData();
            formData.append('local_image_id', document.getElementById('local_image_id').value);
            formData.append('_token', '{{csrf_token()}}');
            formData.append('project', document.getElementById("project").value);
            imageNameData.forEach(function (el, i) {
                formData.append('localSaveImage[' + i + ']', el);
            });
            window.swal({
                title: "Checking...",
                text: "Please wait",
                imageUrl: "{{ asset('public/ajaxloader/ajaxloader.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            $.ajax({
                url: "{{route('localImageSaveUnit')}}",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if(data.status == 1)
                    {
                        localStorage.setItem("local_image_id", data.local_image_id);
                        document.getElementById('local_image_id').value = data.local_image_id;
                    }
                    finalSubmit();
                },
                processData: false,
                contentType: false
            });
        }
    </script>
    <script type="text/javascript">
        $('.select2').select2();

        const imgArray = [];
        const pdfArray = [];

        $('#project').on('change', function () {
            var id = this.value;
            (imgArray.length > 0) ? imgArray.length = 0 : imgArray;
            (pdfArray.length > 0) ? pdfArray.length = 0 : pdfArray;
            $.ajax({
                url: "{{route('getProjectData')}}",
                type: "POST",
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (data) {

                    var description = data.description ? data.description : '';
                    CKEDITOR.instances['description'].setData(description);

                    var imageLink = data.imageLink;
                    var imageName = data.imageName;
                    $('#image_preview').empty();
                    for (var i = 0; i < imageName.length; i++) {
                        html = "<div class='upload-image col-2 text-center' title="+ imageName[i] +" id ="+ imageName[i] +"><img class='img-responsive' data_id="+ i +" src='"+ imageLink[i] +"'><span class='remove btn btn-danger' onclick='removeitem(this)'>&times; </span></div>";
                        imgArray.push(imageName[i]);
                        $("#image_preview").append(html);
                    }

                    var pdfName = data.pdfName;
                    $('#pdf_preview').empty();
                    for (var i = 0; i < pdfName.length; i++) {
                        html = "<div class='upload-pdf col-2 text-center' title="+ pdfName[i] +" id ="+ pdfName[i] +"><p data_id="+ i +">"+ pdfName[i] +"</p><span class='remove btn btn-danger' onclick='removepdf(this)'>&times; </span></div>";
                        pdfArray.push(pdfName[i]);
                        $("#pdf_preview").append(html);
                    }


                    var featuresList = $(".featuresListChecked").find('.featuresList').length;
                    var arr = JSON.parse(data.features) || [];
                    if(arr.length)
                    {
                        arr.forEach(function(checked, i) {
                            for (let j = 0; j < featuresList; j++) {
                                const element = $(".featuresListChecked").find('.featuresList')[j];
                                (element.value == checked) ? $(element).prop('checked', true) : '';
                            }
                        });
                    }
                    else
                    {
                        for (let j = 0; j < featuresList; j++) {
                            const element = $(".featuresListChecked").find('.featuresList')[j];
                            $(element).prop('checked', false);
                        }
                    }

                    var payment_plan = data.payment_plan ? 'Yes' : 'No';
                    payment(payment_plan);
                    $('#payment_plan').val(payment_plan);
                    $(".editRemove").empty();
                    var payment_plan_details = data.payment_plan_details;
                    if(payment_plan_details.length)
                    {
                        indexProject = -1;
                        payment_plan_details.forEach(element => {
                            indexProject++;
                            var add =
                                "<div class='removeMilestone' data_milestone="+ indexProject +"><hr><div class='row'><div class='col-6'><h6 class='text-center'>Milestone <span class='milestone_number'>" + (indexProject + 1) + "</span></h6></div><div class='col-6'><h6 class='text-center'><a href='javascript:void(0)' onclick='deleteConfirmation(this)'>Delete</a></h6></div></div><hr><div class='form-row justify-content-between mile'><div class='col-5 mb-3'><label>Installment Terms</label><input type='number'name='milestone[" +
                                indexProject +
                                "][installment_terms]' class='form-control installment' value='"+element.installment_terms+"' placeholder='Enter Installment'> </div><div class='col-5 mb-3'> <label>Payment Milestone</label>@if($milestoneData)<select class='form-control select2 milestone' name='milestone[" +
                                indexProject +
                                "][milestones]' style='width: 100%'> "+
                                "<option selected disabled>Select Milestone</option>"+
                                " @foreach($milestoneData as $key=>$milestone)"+
                                "<option value='{{$milestone}}'>{{$milestone}}</option>"+
                                "@endforeach </select> @endif</div><div class='col-5 mb-3'><label>Percentage</label>  <input type='number' name='milestone[" +
                                indexProject +
                                "][percentage]' onchange='precalc(this)'  value='"+element.percentage+"'  class='form-control per percentage' placeholder='Enter Percentage'></div> <div class='col-5 mb-3'><div class='form-group'><label>Amount</label><div class='input-group mb-3'><input type='number' name='milestone[" +
                                indexProject + "][amount]' class='form-control total total_amount' placeholder='Enter Amount'><div class='input-group-append'><span class='input-group-text' id='basic-addon2'>AED</span></div></div></div></div></div></div>"
                            $(".editRemove").append(add);

                            $($(".milestone").last()).find("option[value='"+element.milestone+"']").attr("selected","selected");
                        });
                    }
                    else
                    {
                        var html = '<div class="mb-1"><hr><h6 class="text-center">Milestone 1</h6><hr></div><div class="form-row justify-content-between mile"><div class="col-5 mb-3"><label>Installment Terms</label><input type="number" name="milestone[0][installment_terms]"class="form-control installment" placeholder="Enter Installment"></div><div class="col-5 mb-3"><label>Payment Milestone</label>@if($milestoneData)<select class="form-control select2 milestone" name="milestone[0][milestones]" style="width: 100%"><option selected disabled>Select Milestone</option>@foreach($milestoneData as $key=>$milestone)<option value="{{$milestone}}">{{$milestone}}</option>@endforeach</select>@endif</div><div class="col-5 mb-3 abc"><label>Percentage</label><input type="number" name="milestone[0][percentage]" onchange="precalc(this)"class="form-control per percentage" placeholder="Enter Percentage"></div><div class="col-5 mb-3 abc"><div class="form-group"><label>Amount</label><div class="input-group mb-3"><input type="number" name="milestone[0][amount]" class="form-control total total_amount"placeholder="Enter Amount"><div class="input-group-append"><span class="input-group-text" id="basic-addon2">AED</span></div></div></div></div></div>';
                        $(".editRemove").append(html);
                    }
                    $('.select2').select2();
                }
            });
        });

        function deleteConfirmation(el)
        {
            var index = $(el).parents(".removeMilestone").remove();
            for (let j = 0; j < $(".editRemove").find('.milestone_number').length; j++) {
                const element = $(".editRemove").find('.milestone_number')[j];
                const installment = $(".editRemove").find('.installment')[j];
                const milestone = $(".editRemove").find('.milestone')[j];
                const percentage = $(".editRemove").find('.percentage')[j];
                const total_amount = $(".editRemove").find('.total_amount')[j];
                var a = j + 1;
                $(element).text(a).parents(".removeMilestone").attr("data_milestone", a - 1);
                $(installment).attr("name","milestone[" + (a-1) + "][installment_terms]");
                $(milestone).attr("name","milestone[" + (a-1) + "][milestones]");
                $(percentage).attr("name","milestone[" + (a-1) + "][percentage]");
                $(total_amount).attr("name","milestone[" + (a-1) + "][amount]");
            }
            var indexlast = $(".removeMilestone").last().attr("data_milestone") ?? 0;
            if (indexlast == '0') {
                $(".removeMilestone").find('a').css("visibility", "hidden");
            }
        }

    </script>
    <!-- start script for upload first image-->
    <script type="text/javascript">
        var lastcount = 0;

        document.getElementById("files").onchange = function (event) {
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                if (!f.type.match('image.*')) {
                    continue;
                }
                html = "<div class='upload-image col-2 text-center'><img class='img-responsive' data_id=" + (
                        lastcount) + " src='" + URL.createObjectURL(document.getElementById("files").files[i]) +
                    "'><span class=\"remove btn btn-danger\" onclick='removeitem(this)'>&times; </span></div>"
                $("#image_preview").append(html);

                imgArray.push(document.getElementById("files").files[i]);
                lastcount++;
                for (let j = 0; j < $("#image_preview").find('.upload-image').length; j++) {
                    const element = $("#image_preview").find('.upload-image')[j];
                    $(element).find('img').attr('data_id', j);
                }
            }
        }

        function removeitem(el) {
            var index = $(el).parent(".upload-image").find("img").attr("data_id");
            imgArray.splice(index, 1);
            $(el).parent(".upload-image").remove();
            for (let j = 0; j < $("#image_preview").find('.upload-image').length; j++) {
                const element = $("#image_preview").find('.upload-image')[j];
                $(element).find('img').attr('data_id', j);
            }
        }

    </script>
    <!--end script for upload first image-->

    <!--start script for upload second image-->
    <script type="text/javascript">
        var lastfloorcount = 0;
        var floorplanimageArray = [];
        document.getElementById("floorplanimage").onchange = function (event) {
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                if (!f.type.match('image.*')) {
                    continue;
                }
                html = "<div class='upload-image-floor col-2 text-center'><img class='img-responsive' data_ids=" + (
                        lastfloorcount) + " src='" + URL.createObjectURL(document.getElementById("floorplanimage").files[
                        i]) +
                    "'><span class=\"remove btn btn-danger\" onclick='removeitems(this)'>&times; </span></div>"
                $("#floorplanimage_preview").append(html);

                floorplanimageArray.push(document.getElementById("floorplanimage").files[i]);
                lastfloorcount++;
            }
        }

        function removeitems(el) {
            var index = $(el).parent(".upload-image-floor").find("img").attr("data_ids");
            floorplanimageArray.splice(index, 1);
            $(el).parent(".upload-image-floor").remove();
            for (let j = 0; j < $("#floorplanimage_preview").find('.upload-image-floor').length; j++) {
                const element = $("#floorplanimage_preview").find('.upload-image-floor')[j];
                $(element).find('img').attr('data_ids', j);
            }
        }

    </script>
    <!--end script for upload second image-->

    <!--start script for upload video-->
    <script type="text/javascript">
        var lastvideocount = 0;
        var videoArray = [];
        document.getElementById("in_video").onchange = function (event) {
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                if (!f.type.match('video.*')) {
                    continue;
                }
                html =
                    '<div class="upload-video col-2 text-center"><video data_id= ' + (lastvideocount) +
                    ' style="max-width:100%" controls> <source src="' +
                    URL.createObjectURL(document.getElementById("in_video").files[i]) +
                    '" type="video/mp4"> Your browser does not support the video tag. </video><span class=\"remove btn btn-danger\" onclick="removevideo(this)">&times; </span></div>'
                $("#video_preview").append(html);

                videoArray.push(document.getElementById("in_video").files[i]);
                lastvideocount++;
            }
        }

        function removevideo(el) {
            var index = $(el).parent(".upload-video").find("video").attr("data_id");
            videoArray.splice(index, 1);
            $(el).parent(".upload-video").remove();
            for (let j = 0; j < $("#video_preview").find('.upload-video').length; j++) {
                const element = $("#video_preview").find('.upload-video')[j];
                $(element).find('video').attr('data_id', j);
            }
        }

    </script>
    <!--script end for upload video-->

    <!--start script for upload pdf-->
    <script type="text/javascript">
        var lastcount = 0;
        // var pdfArray = [];
        document.getElementById("pdf").onchange = function (event) {
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                if (!f.type.match('pdf.*')) {
                    continue;
                }
                html = '<div class="upload-pdf col-2 text-center"><p data_id = ' + (lastcount) + ' > ' + f.name +
                    ' </p><span class=\"remove btn btn-danger\" onclick="removepdf(this)">&times; </span></div>'
                $("#pdf_preview").append(html);

                pdfArray.push(document.getElementById("pdf").files[i]);
                lastcount++;
            }
        }

        function removepdf(el) {
            var index = $(el).parent(".upload-pdf").find("p").attr("data_id");
            pdfArray.splice(index, 1);
            $(el).parent(".upload-pdf").remove();
            for (let j = 0; j < $("#pdf_preview").find('.upload-pdf').length; j++) {
                const element = $("#pdf_preview").find('.upload-pdf')[j];
                $(element).find('p').attr('data_id', j);
            }
        }

    </script>
    <!--end script for upload pdf-->

    <script type="text/javascript">
        function array_move(arr, old_index, new_index) {
            if (new_index >= arr.length) {
                var k = new_index - arr.length + 1;
                while (k--) {
                    arr.push(undefined);
                }
            }
            arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
            return arr; // for testing
        };
    </script>

    <script>
        $("#handover_year").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });
    </script>

    <script>
        function payment(value) {
            if (value == 'Yes') {
                $(".show_hide").show();
            } else {
                $(".show_hide").hide();
            }
        }
    </script>

    <script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        CKEDITOR.replace('description');
    </script>

    <script>
        $(".add-row").click(function () {
            var index = $(".removeMilestone").last().attr("data_milestone") ?? 0;
            index++;
            var add =
                "<div class='removeMilestone' data_milestone="+ index +"><hr><div class='row'><div class='col-6'><h6 class='text-center'>Milestone <span class='milestone_number'>" + (parseInt(index) + 1) + "</span></h6></div><div class='col-6'><h6 class='text-center'><a href='javascript:void(0)' onclick='deleteConfirmation(this)'>Delete</a></h6></div></div><hr><div class='form-row justify-content-between mile'><div class='col-5 mb-3'><label>Installment Terms</label><input type='number'name='milestone[" +
                index +
                "][installment_terms]' class='form-control installment' placeholder='Enter Installment'> </div><div class='col-5 mb-3'> <label>Payment Milestone</label>@if($milestoneData)<select class='form-control select2 milestone' name='milestone[" +
                index +
                "][milestones]' style='width: 100%'> <option selected disabled>Select Milestone</option> @foreach($milestoneData as $key=>$milestone)<option value='{{$milestone}}'>{{$milestone}}</option>@endforeach </select> @endif</div><div class='col-5 mb-3'><label>Percentage</label>  <input type='number' name='milestone[" +
                index +
                "][percentage]' onchange='precalc(this)' class='form-control per percentage' placeholder='Enter Percentage'></div> <div class='col-5 mb-3'><div class='form-group'><label>Amount</label><div class='input-group mb-3'><input type='number' name='milestone[" +
                index + "][amount]' class='form-control total total_amount' placeholder='Enter Amount'><div class='input-group-append'><span class='input-group-text' id='basic-addon2'>AED</span></div></div></div></div></div></div>"
            $(".add-row").before(add);
            if (index !== '0') {
                $(".removeMilestone").find('a').css("visibility", "visible");
            }
            $('.select2').select2();
        });

        $("#price, #percentage").on('change', function () {

        });

        function rowcol(el) {
            var amount = $(el).val();
            $('.per').each(function (i, sel) {
                var percentage = $(sel).val();
                var total = (amount * percentage) / 100;
                $(sel).parents(".mile").find(".total").val(total);

            })
        }

        function precalc(sel) {
            var amount = $('#price').val();
            var percentage = $(sel).val();
            var total = (amount * percentage) / 100;
            $(sel).parents(".mile").find(".total").val(total);
        }
    </script>

    <script type="text/javascript">
        function finalSubmit()
        {
            // event.preventDefault()
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            var myform = document.getElementById("frmTarget");
            var formData = new FormData(myform);
            formData: $(this).serialize();

            imgArray.forEach(function (el, i) {
                formData.append('filesList[' + i + ']', el);
            });

            floorplanimageArray.forEach(function (el, i) {
                formData.append('floor_plan_image[' + i + ']', el);
            });

            videoArray.forEach(function (el, i) {
                formData.append('video[' + i + ']', el);
            });

            pdfArray.forEach(function (el, i) {
                formData.append('pdfList[' + i + ']', el);
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
                url: "{{route('add-unit')}}",
                data: formData,

                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");
                        var segments = location.pathname.split('/');
                        var chksegments = segments[3];
                        if(chksegments == "manage-unit-status"){
                            localStorage.clear();
                            window.location.href = "{{route('ready_unit_list')}}";
                        } else {
                            localStorage.clear();
                            window.location.href = "{{route('manage_listings')}}";
                        }
                    } else {
                        swal("ERROR!",'<div class="message"> <ul> <li>' + data.message + '</li> </ul></div>', "error");
                    }
                },
                error: function (data) {
                    swal("ERROR!", data, "error");
                },
                cache: false,
                contentType: false,
                processData: false
            });

        }
        $(document).ready(function () {
            payment('No');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#community').on('change', function() {
                $("#subinput").hide();
                var id = this.value;
                $.ajax({
                    url:"{{route('get-subcommunity')}}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType : 'json',
                    success: function(result){
                        $('#subcommunity').html('<option selected disabled>Select sub community</option>');
                        $.each(result.subcommunity,function(key,value){
                            $("#subcommunity").append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                });
            });

            $("#image_preview").sortable({
                update: function (event, ui) {
                    var oldpos1 = $(ui.item[0].firstChild).attr('data_id');
                    for (let j = 0; j < $("#image_preview").find(
                        '.upload-image').length; j++) {
                        const element = $("#image_preview").find(
                            '.upload-image')[j];
                        $(element).find('img').attr('data_id', j);
                    }
                    var newpos1 = $(ui.item[0].firstChild).attr('data_id');
                    array_move(imgArray, oldpos1, newpos1);
                }
            });

            $("#floorplanimage_preview").sortable({
                update: function (event, ui) {
                    var oldpos2 = $(ui.item[0].firstChild).attr('data_ids');
                    for (let j = 0; j < $("#floorplanimage_preview").find(
                        '.upload-image-floor').length; j++) {
                        const element = $("#floorplanimage_preview").find(
                            '.upload-image-floor')[j];
                        $(element).find('img').attr('data_ids', j);
                    }
                    var newpos2 = $(ui.item[0].firstChild).attr('data_ids');
                    floorplanimageArray = array_move(floorplanimageArray, oldpos2, newpos2);
                }
            });

            $("#video_preview").sortable({
                update: function (event, ui) {
                    var oldpos3 = $(ui.item[0].firstChild).attr('data_id');
                    for (let j = 0; j < $("#video_preview").find(
                        '.upload-video').length; j++) {
                        const element = $("#video_preview").find(
                            '.upload-video')[j];
                        $(element).find('video').attr('data_id', j);
                    }
                    var newpos3 = $(ui.item[0].firstChild).attr('data_id');
                    videoArray = array_move(videoArray, oldpos3, newpos3);
                }
            });

            var local_image_id = localStorage.getItem("local_image_id");
            if(local_image_id)
            {
                document.getElementById('local_image_id').value = local_image_id;
                $.ajax({
                    url: "{{route('getLocalImageProduct')}}",
                    type: "POST",
                    data: {
                        local_image_id: local_image_id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        result.data.imageArray.forEach(function (imagesData, i){
                            html = "<div class='upload-image col-2 text-center'><img class='img-responsive' data_id=" + (i) + " src='" + imagesData + "'><span class=\"remove btn btn-danger\" onclick='removeitem(this)'>&times; </span></div>";
                            $("#image_preview").append(html);
                        });

                        result.data.imageName.forEach(function (imagesData, i){
                            imgArray.push(imagesData);
                        });
                        for (let j = 0; j < $("#image_preview").find('.upload-image').length; j++) {
                            const element = $("#image_preview").find('.upload-image')[j];
                            $(element).find('img').attr('data_id', j);
                        }
                    }
                });
            }
            else
            {
                $.ajax({
                    url: "{{route('getLocalImageProduct')}}",
                    type: "POST",
                    data: {
                        local_image_id: local_image_id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        document.getElementById('local_image_id').value = result.data;
                    }
                });
            }

            // $('#submit-all').click(function (event) {

            if (localStorage.getItem('click_unit') == 'yes') {
                document.getElementById('project').value = localStorage.getItem("project_unit");
                document.getElementById('bedrooms').value = localStorage.getItem("bedrooms_unit");
                document.getElementById('bathrooms').value = localStorage.getItem("bathrooms_unit");
                document.getElementById('size').value = localStorage.getItem("size_unit");
                document.getElementById('price').value = localStorage.getItem("price_unit");
                document.getElementById('title').value = localStorage.getItem("title_unit");
                CKEDITOR.instances['description'].setData(localStorage.getItem("description_unit"));

                var arr = JSON.parse(localStorage.getItem('checked_unit')) || [];
                arr.forEach(function(checked, i) {
                    $('.featuresList').eq(i).prop('checked', checked);
                });

                document.getElementById('payment_plan').value = localStorage.getItem("payment_plan_unit");
                if(localStorage.getItem("payment_plan_unit") === "Yes"){

                    $(".show_hide").show();

                    document.getElementById('pre_handover_amount').value = localStorage.getItem("pre_handover_amount_unit");
                    document.getElementById('handover_amount').value = localStorage.getItem("handover_amount_unit");
                    document.getElementById('post_handover_amount').value = localStorage.getItem("post_handover_amount_unit");

                    var installment = localStorage.getItem("installment_unit").split(',');
                    var milestone = localStorage.getItem("milestone_unit").split(',');
                    var percentage = localStorage.getItem("percentage_unit").split(',');
                    var total_amount = localStorage.getItem("total_amount_unit").split(',');
                    if(installment.length){
                        $(".editRemove").empty();
                    }
                    index = -1;
                    installment.forEach(element => {

                        index++;
                        var add =
                            "<div class='removeMilestone' data_milestone="+ index +"><hr><div class='row'><div class='col-6'><h6 class='text-center'>Milestone <span class='milestone_number'>" + (parseInt(index) + 1) + "</span></h6></div><div class='col-6'><h6 class='text-center'><a href='javascript:void(0)' onclick='deleteConfirmation(this)'>Delete</a></h6></div></div><hr><div class='form-row justify-content-between mile'><div class='col-5 mb-3'><label>Installment Terms</label><input type='number'name='milestone[" +
                            index +
                            "][installment_terms]' class='form-control installment' value='"+installment[index]+"' placeholder='Enter Installment'> </div><div class='col-5 mb-3'> <label>Payment Milestone</label>@if($milestoneData)<select class='select2 milestone' name='milestone[" +
                            index +
                            "][milestones]' style='width: 100%'> "+
                            "<option selected disabled>Select Milestone</option>"+
                            " @foreach($milestoneData as $key=>$milestone)"+
                            "<option value='{{$milestone}}'>{{$milestone}}</option>"+
                            "@endforeach </select> @endif</div><div class='col-5 mb-3'><label>Percentage</label>  <input type='number' name='milestone[" +
                            index +
                            "][percentage]' onchange='precalc(this)'  value='"+percentage[index]+"'  class='form-control per percentage' placeholder='Enter Percentage'></div> <div class='col-5 mb-3'><div class='form-group'><label>Amount</label><div class='input-group mb-3'><input type='number' name='milestone[" +
                            index + "][amount]' class='form-control total total_amount'  value='"+total_amount[index]+"'  placeholder='Enter Amount'><div class='input-group-append'><span class='input-group-text' id='basic-addon2'>AED</span></div></div></div></div></div></div>"
                        $(".local-storage-save").append(add);
                        if (index !== '0') {
                            $(".removeMilestone").find('a').css("visibility", "visible");
                        }

                        $($(".milestone").last()).find("option[value='"+milestone[index]+"']").attr("selected","selected");
                    });
                }
                else
                {
                    $(".show_hide").hide();
                }
                $('.select2').select2();
                if (index != 0) {
                    $(".removeMilestone").find('a').css("visibility", "visible");
                }
                else
                {
                    $(".removeMilestone").find('a').css("visibility", "hidden");
                }
            }
        });
    </script>

    <script type="text/javascript" src="{{ asset('public/js/mapInput.js')}}"></script>

    <script defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu6xoWPgCs5Pum_0MlSSdseLzDVN7StwQ&libraries=places&callback=initialize">
    </script>
@endsection
