@extends('layouts.admin-app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .message {
        text-align: left;
        padding-left: 55px;
    }

</style>
<script type="text/javascript">
    function setvalue() {
        localStorage.clear();
        localStorage.setItem('click', 'yes');
        var project = document.getElementById("project").value;
        var developer_id = document.getElementById("developer_id").value;
        var property = document.getElementById("property").value;
        var completion_status = document.getElementById("completion_status").value;
        var quarter = document.getElementById("quarter").value;
        var handover_year = document.getElementById("handover_year").value;
        var community = document.getElementById("community").value;
        var subcommunity = document.getElementById("subcommunity").value;
        var addressinput = document.getElementById("address-input").value;
        var addresslatitude = document.getElementById("address-latitude").value;
        var addresslongitude = document.getElementById("address-longitude").value;

        var bed_rooms = [];
        var bed_roomsinput = document.getElementsByClassName("bed_rooms");
        for (let i = 0; i < bed_roomsinput.length; i++) {
            const element = bed_roomsinput[i];
            bed_rooms.push(element.value);
        }

        var min_price = [];
        var min_priceinput = document.getElementsByClassName("min_price");
        for (let i = 0; i < min_priceinput.length; i++) {
            const element = min_priceinput[i];
            min_price.push(element.value);
        }

        var max_price = [];
        var max_priceinput = document.getElementsByClassName("max_price");
        for (let i = 0; i < max_priceinput.length; i++) {
            const element = max_priceinput[i];
            max_price.push(element.value);
        }

        var payment_plan = document.getElementById("payment_plan").value;
        var payment_plan_comments = document.getElementById("payment_plan_comments").value;

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

        var commission = document.getElementById("commission").value;
        var rera_project_number = document.getElementById("rera_project_number").value;
        var construction_status = document.getElementById("construction_status").value;
        var construction_date = document.getElementById("construction_date").value;
        var description = CKEDITOR.instances.description.getData();

        var arr = $('.featuresList').map(function () {
            return this.checked;
        }).get();

        localStorage.setItem("checked", JSON.stringify(arr));
        localStorage.setItem("project", project);
        localStorage.setItem("developer_id", developer_id);
        localStorage.setItem("property", property);
        localStorage.setItem("completion_status", completion_status);
        localStorage.setItem("quarter", quarter);
        localStorage.setItem("handover_year", handover_year);
        localStorage.setItem("community", community);
        localStorage.setItem("subcommunity", subcommunity);
        localStorage.setItem("address-input", addressinput);
        localStorage.setItem("address-latitude", addresslatitude);
        localStorage.setItem("address-longitude", addresslongitude);

        localStorage.setItem("bed_rooms", bed_rooms);
        localStorage.setItem("min_price", min_price);
        localStorage.setItem("max_price", max_price);

        localStorage.setItem("payment_plan", payment_plan);
        localStorage.setItem("payment_plan_comments", payment_plan_comments);

        localStorage.setItem("installment", installment);
        localStorage.setItem("milestone", milestone);
        localStorage.setItem("percentage", percentage);

        localStorage.setItem("commission", commission);
        localStorage.setItem("rera_project_number", rera_project_number);
        localStorage.setItem("construction_status", construction_status);
        localStorage.setItem("construction_date", construction_date);
        localStorage.setItem("description", description);
    }

</script>
@section('content')
<div class="card">
    <div class="card-header">
        <div class="container">
            <form class="form-horizontal" method="POST" id='frmTarget' enctype="multipart/form-data" autocomplete="off">

                @if(Request::segment(1) == "manage_ready_project")
                <input type="hidden" name="ready_status" value="1">
                @endif
                @if(Request::segment(1) == "manage_sold_out_project")
                <input type="hidden" name="sold_out_status" value="1">
                @endif

                @csrf

                <div class="form-row justify-content-between">
                    <div class="col-5">
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" name="project" id="project" class="form-control"
                                placeholder="Enter Project">
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            @if($developerData)
                            <label>Select Developer</label>
                            <select class="form-control select2" id="developer_id" name="developer_id">
                                <option selected disabled>Select Developer</option>
                                @foreach($developerData as $key=>$company)
                                <option value="{{$key}}">{{$company}}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <label>Type Of Property</label>
                            <select class="form-control select2" id="property" name="property">
                                <option selected disabled>Select Property</option>
                                @foreach ($typeList as $item)
                                <option value="{{$item}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <label>Select Completion Status</label>
                            <select name="completion_status" class="form-control select2" id="completion_status" onchange="completionStatusHideShow(this.value)">
                                <option value="0" selected disabled>Select Completion Status</option>
                                <option value="1">Ready</option>
                                <option value="2">Quarter and Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row justify-content-between completion_status_hide_show">
                    <div class="col-5">
                        <div class="form-group">
                            <label>Select Quater</label>
                            <select class="form-control select2" id="quarter" name="quarter" style="width: 100%">
                                <option selected disabled>Select Quarter</option>
                                <option value="Q1">Q1</option>
                                <option value="Q2">Q2</option>
                                <option value="Q3">Q3</option>
                                <option value="Q4">Q4</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <label>Select Handover Year</label>
                            <input type="number" name="handover_year" min='0' id="handover_year" class="form-control"
                                placeholder="Enter handover year">
                        </div>
                    </div>
                </div>
                <div class="form-row justify-content-between">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label>Community</label>
                            <div class="input-group">
                                <select class="custom-select select2" id="community" name="community">
                                    <option selected disabled>Select Community</option>
                                    @if($community)
                                    @foreach($community as $communityData)
                                    <option value="{{$communityData->id}}">{{$communityData->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="community">Community</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label>Sub Community</label>
                            <div class="input-group">

                                <select class="custom-select select2" name="subcommunity" id="subcommunity">
                                    <option selected disabled>Select Sub Community</option>
                                </select>
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="subcommunity">Sub Community</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="" class="col-sm-2 col-form-label">Location</label>
                    <div class="col-sm-10">
                        <input type="text" id="address-input" name="location" class="form-control map-input"
                            placeholder="Enter Location">
                        <input type="hidden" name="latitude" id="address-latitude" value="0" />
                        <input type="hidden" name="longitude" id="address-longitude" value="0" />
                    </div>
                </div>
                <div id="address-map-container" class="mb-2" style="width:100%; height:400px; ">
                    <div style="width: 100%; height: 100%" id="address-map">
                    </div>
                </div>

                <div style="border:solid 1px #ddd; padding:10px;" class="form-group new-bedrooms">
                    <label><i class="fas fa-angle-double-right"></i>Availability with Price Range</label><br>
                    <div data_contact="0" class="form-group row justify-content-between contact">
                        <div class="col-3">
                            <label for="bed_rooms">Bedrooms</label>
                            <select name="bedrooms[0][bed_rooms]" class="form-control select2 bed_rooms" id="bed_rooms">
                                <option value="" selected disabled>Select Bedrooms</option>
                                <option value="Studio">Studio</option>
                                @for ($i = 1; $i <= 20; $i+=0.5) <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="min_price">Min Price</label>
                            <input type="number" name="bedrooms[0][min_price]" placeholder="Enter Min Price"
                                class="form-control min_price" id="min_price">
                        </div>
                        <div class="col-3">
                            <label for="max_price">Max Price</label>
                            <input type="number" name="bedrooms[0][max_price]" id="max_price" placeholder="Enter Max Price"
                                class="form-control max_price">
                        </div>
                        <div class="col-1" style="align-self: end;">
                            <span class="card-header-right-span">
                                <button class="btn btn-info add-bedrooms" style="padding: 9px 11px;"><i
                                        class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-row justify-content-between">
                    <div class="col-3 mb-3">
                        <label>Select Payment Plan</label>
                        <select class="form-control select2" id="payment_plan" onchange="payment(this.value)"
                            name="payment_plan">
                            <option selected disabled>Select Payment Plan</option>
                            <option value="Yes">YES</option>
                            <option value="No">NO</option>
                        </select>
                    </div>
                    <div class="col-9 mb-3">
                        <label>Comments</label>
                        <input type="text" name="payment_plan_comments" class="form-control" id="payment_plan_comments" placeholder="Enter Comments">
                    </div>
                </div>

                <button type="button" class="btn float-right btn-info font-weight-bold more_details_button_hide_show">
                    More Details....
                </button>
                <div style="clear:both"></div>

                <div class="show_more_details">
                    <div class="card show_hide" style="padding: 15px 15px 15px 15px;">
                        <div class="editRemove">
                            <div class="mb-1">
                                <hr>
                                <h6 class='text-center'>Milestone 1</h6>
                                <hr>
                            </div>
                            <div class="form-row justify-content-between mile">
                                <div class="col-4 mb-3">
                                    <label>Installment Terms</label>
                                    <input type="number" name="milestone[0][installment_terms]"
                                        class="form-control installment" placeholder="Enter Installment">
                                </div>
                                <div class="col-4 mb-3">
                                    <label>Payment Milestone</label>
                                    @if($milestoneData)
                                    <select class="form-control milestone select2" name="milestone[0][milestones]" style="width: 100%">
                                        <option selected disabled>Select Milestone</option>
                                        @foreach($milestoneData as $key=>$milestone)
                                        <option value="{{$milestone}}">{{$milestone}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                                <div class="col-4 mb-3 abc">
                                    <label>Percentage</label>
                                    <input type="number" name="milestone[0][percentage]" class="form-control per percentage" placeholder="Enter Percentage">
                                </div>
                            </div>
                        </div>
                        <div class="row button-row py-2">
                            <div class="col-12">
                                <button class="btn btn-info add-row" type="button">Add Milestone</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-row justify-content-between">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Commission</label>
                                <div class="input-group mb-3">
                                    <input type="number" min='0' class="form-control" id="commission" name="commission"
                                        placeholder="Enter Commission">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>RERA Number</label>
                                <input type="text" class="form-control" id="rera_project_number" name="rera_permit_no"
                                    placeholder="Enter RERA Number">
                            </div>
                        </div>
                    </div>
                    <div class="form-row justify-content-between">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Construction Status By RERA</label>
                                <div class="input-group mb-3 ">
                                    <input type="number" min='0' class="form-control" id="construction_status"
                                        name="construction_status" placeholder="Enter Construction Status">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Inspection Date</label>
                                <div class="input-group mb-3 ">
                                    <input type="date" class="form-control" id="construction_date" name="construction_date">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2"><i
                                                class="fas fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
    
                    <div class="form-row justify-content-between">
                        <div class="col-12 mb-3">
                            <label>Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
    
                    <div style="border:solid 1px #ddd; padding:10px;" id="cont">
                        <label><i class="fas fa-angle-double-right"></i>Features List</label><br>
                        <div class="grid-container row">
                            @if($featuresList)
                            @foreach($featuresList as $key=>$featuresName)
                            <div class="col-3">
                                <input type="checkbox" id="featuresList{{$key}}" class="featuresList" name="featuresList[]"
                                    value="{{$featuresName['fname']}}">
                                <label for="featuresList"> {{$featuresName['fname']}}</label>
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
                </div>

                <button type="button" class="btn float-right btn-info font-weight-bold less_details_button_hide_show mt-3">
                    Less Details....
                </button>
                <div style="clear:both"></div>

                <hr>

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
                                    <input type="file" accept="image/*" name="image[]" id="files" required
                                        class="form-control" multiple>
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
                                    <input type="file"
                                        accept="application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                        name="pdf[]" id="pdf" class="form-control" multiple>
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
                        onclick="setvalue();">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"
    rel="stylesheet" />

<!-- start script for upload first image-->
<script type="text/javascript">
    var lastcount = 0;
    var imgArray = [];

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
                    lastfloorcount) + " src='" + URL.createObjectURL(document.getElementById("floorplanimage")
                    .files[
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
    var pdfArray = [];
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

    function completionStatusHideShow(value) {
        if (value == 2) {
            $(".completion_status_hide_show").show();
        } else {
            $('#quarter').empty();
            $('#quarter').append(
                '<option selected disabled>Select Quarter</option><option value="Q1">Q1</option><option value="Q2">Q2</option><option value="Q3">Q3</option><option value="Q4">Q4</option>'
            );
            $('#handover_year').val('');
            $(".completion_status_hide_show").hide();
        }
    }

</script>

<script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    CKEDITOR.replace('description');

</script>

<script>
    var index = 0;
    $(".add-row").click(function () {
        index++;
        var add =
            "<hr><h6 class='text-center'>Milestone " + (index + 1) +
            "</h6><hr><div class='form-row justify-content-between mile'><div class='col-4 mb-3'><label>Installment Terms</label><input type='number'name='milestone[" +
            index +
            "][installment_terms]' class='form-control installment' placeholder='Enter Installment'> </div><div class='col-4 mb-3'> <label>Payment Milestone</label>@if($milestoneData)<select class='form-control milestone select2' name='milestone[" +
            index +
            "][milestones]'> <option selected disabled>Select Milestone</option> @foreach($milestoneData as $key=>$milestone)<option value='{{$milestone}}'>{{$milestone}}</option>@endforeach </select> @endif</div><div class='col-4 mb-3'><label>Percentage</label>  <input type='number' name='milestone[" +
            index +
            "][percentage]' class='form-control per percentage' placeholder='Enter Percentage'></div></div>"
        $(".add-row").before(add);
        $('.select2').select2();
    });

    $(".add-bedrooms").click(function () {
        var index = $(".contact").last().attr("data_contact") ?? 0;
        index++;
        var add = '<div data_contact="' + index +
            '" class="form-group row justify-content-between contact"><div class="col-3"><label for="bed_rooms">Bedrooms</label><select name="bedrooms[' +
            (index + 1) +
            '][bed_rooms]" class="form-control select2 bed_rooms"><option value="" selected disabled>Select Bedrooms</option><option value="Studio">Studio</option>@for ($i = 1; $i <= 20; $i+= 0.5) <option value="{{$i}}">{{$i}}</option>@endfor</select></div><div class="col-3"><label for="min_price">Min Price</label><input type="text" name="bedrooms[' +
            (index + 1) +
            '][min_price]" placeholder="Enter Min Price" class="form-control min_price"></div><div class="col-3"><label for="max_price">Max Price</label><input type="text" name="bedrooms[' +
            (index + 1) +
            '][max_price]" placeholder="Enter Max Price" class="form-control max_price"></div><div class="col-1" style="align-self: end;"><span class="card-header-right-span"><button class="btn btn-danger" style="padding:9px 11px;" onclick="removeBedrooms(this)"><i class="fa fa-minus"></i></button></span></div></div>';

        $(".new-bedrooms").append(add);
        $('.select2').select2();
    });

    function removeBedrooms(el) {
        var index = $(el).parents(".contact").remove();
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        payment('No');
        completionStatusHideShow(1);

        $(".show_more_details").hide();
        $(".less_details_button_hide_show").hide();
        
        $('.more_details_button_hide_show').click(function(){
            $(".show_more_details").show();
            $(".more_details_button_hide_show").hide();
            $(".less_details_button_hide_show").show();
        });

        $('.less_details_button_hide_show').click(function(){
            $(".show_more_details").hide();
            $(".more_details_button_hide_show").show();
            $(".less_details_button_hide_show").hide();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#community').on('change', function () {
            var id = this.value;
            $.ajax({
                url: "{{route('get-subcommunity')}}",
                type: "POST",
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#subcommunity').html(
                        '<option selected disabled>Select Sub Community</option>');
                    $.each(result.subcommunity, function (key, value) {
                        $("#subcommunity").append('<option value="' + value.id +
                            '">' + value.name + '</option>');
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
                imgArray = array_move(imgArray, oldpos1, newpos1);
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

        $('#submit-all').click(function (event) {
            event.preventDefault()
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
                formData.append('pdf[' + i + ']', el);
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
                url: "{{route('addProjectSubmit')}}",
                data: formData,

                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");
                        var segments = location.pathname.split('/');
                        var chksegments = segments[2];
                        if (chksegments == "manage_ready_project") {
                            localStorage.clear();
                            window.location.href = "{{route('readyProjectIndex')}}";
                        } else if (chksegments == "manage_sold_out_project") {
                            localStorage.clear();
                            window.location.href = "{{route('soldOutProjectIndex')}}";
                        } else {
                            localStorage.clear();
                            window.location.href = "{{route('projectIndex')}}";
                        }
                    } else {
                        swal("ERROR!", '<div class="message"> <ul> <li>' + data.message +
                            '</li> </ul></div>', "error");
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

        if (localStorage.getItem('click') == 'yes') {
            document.getElementById('project').value = localStorage.getItem("project");
            document.getElementById('developer_id').value = localStorage.getItem("developer_id");
            document.getElementById('property').value = localStorage.getItem("property");

            document.getElementById('completion_status').value = localStorage.getItem("completion_status");

            if (localStorage.getItem("completion_status") == 2) {
                $(".completion_status_hide_show").show();
            } else {
                $('#quarter').empty();
                $('#quarter').append(
                    '<option selected disabled>Select Quarter</option><option value="Q1">Q1</option><option value="Q2">Q2</option><option value="Q3">Q3</option><option value="Q4">Q4</option>'
                );
                $('#handover_year').val('');
                $(".completion_status_hide_show").hide();
            }
            document.getElementById('quarter').value = localStorage.getItem("quarter");
            document.getElementById('handover_year').value = localStorage.getItem("handover_year");

            document.getElementById('community').value = localStorage.getItem("community");

            var id = localStorage.getItem("community");
            $.ajax({
                url: "{{route('get-subcommunity')}}",
                type: "POST",
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#subcommunity').html(
                        '<option selected disabled>Select Sub Community</option>');
                    $.each(result.subcommunity, function (key, value) {
                        if(value.id == localStorage.getItem("subcommunity"))
                        {
                            var selected = "selected";
                        }
                        else
                        {
                            var selected = "";
                        }
                        $("#subcommunity").append('<option value="' + value.id +
                            '"'+ selected +'>' + value.name + '</option>');
                    });
                }
            });
            
            document.getElementById('address-input').value = localStorage.getItem("address-input");
            document.getElementById('address-latitude').value = localStorage.getItem("address-latitude");
            document.getElementById('address-longitude').value = localStorage.getItem("address-longitude");

            var bed_rooms = localStorage.getItem("bed_rooms").split(',');
            var min_price = localStorage.getItem("min_price").split(',');
            var max_price = localStorage.getItem("max_price").split(',');
            if (bed_rooms.length) {
                index = -1;
                bed_rooms.forEach(element => {
                    index++;
                    if(index)
                    {
                        var add = '<div data_contact="' + index +
                        '" class="form-group row justify-content-between contact"><div class="col-3"><label for="bed_rooms">Bedrooms</label><select name="bedrooms[' +
                        (index + 1) +
                        '][bed_rooms]" class="form-control select2 bed_rooms"><option value="" selected disabled>Select Bedrooms</option><option value="Studio">Studio</option>@for ($i = 1; $i <= 20; $i+= 0.5) <option value="{{$i}}">{{$i}}</option>@endfor</select></div><div class="col-3"><label for="min_price">Min Price</label><input type="text" name="bedrooms[' +
                        (index + 1) +
                        '][min_price]" value="' + min_price[index] + '" placeholder="Enter Min Price" class="form-control min_price"></div><div class="col-3"><label for="max_price">Max Price</label><input type="text" name="bedrooms[' +
                        (index + 1) +
                        '][max_price]" value="' + max_price[index] + '" placeholder="Enter Max Price" class="form-control max_price"></div><div class="col-1" style="align-self: end;"><span class="card-header-right-span"><button class="btn btn-danger" style="padding:9px 11px;" onclick="removeBedrooms(this)"><i class="fa fa-minus"></i></button></span></div></div>';
                        $(".new-bedrooms").append(add);
                    }
                    else
                    {
                        $('#min_price').val(min_price[index]);
                        $('#max_price').val(max_price[index]);
                    }
                    $($(".bed_rooms").last()).find("option[value='" + bed_rooms[index] + "']").attr("selected", "selected");
                });
            }

            document.getElementById('payment_plan').value = localStorage.getItem("payment_plan");
            document.getElementById('payment_plan_comments').value = localStorage.getItem("payment_plan_comments");
            if (localStorage.getItem("payment_plan") === "Yes") {
                var installment = localStorage.getItem("installment").split(',');
                var milestone = localStorage.getItem("milestone").split(',');
                var percentage = localStorage.getItem("percentage").split(',');
                if (installment.length) {
                    $(".editRemove").remove();
                }
                index = -1;
                installment.forEach(element => {

                    index++;
                    var add =
                        "<hr><h6 class='text-center'>Milestone " + (index + 1) +
                        "</h6><hr><div class='form-row justify-content-between mile'><div class='col-4 mb-3'><label>Installment Terms</label><input type='number'name='milestone[" +
                        index +
                        "][installment_terms]' class='form-control installment' value='" + installment[
                            index] +
                        "' placeholder='Enter Installment'> </div><div class='col-4 mb-3'> <label>Payment Milestone</label>@if($milestoneData)<select class='form-control milestone select2' style='width: 100%' name='milestone[" +
                        index +
                        "][milestones]'> " +
                        "<option selected disabled>Select Milestone</option>" +
                        " @foreach($milestoneData as $key=>$milestone)" +
                        "<option value='{{$milestone}}'>{{$milestone}}</option>" +
                        "@endforeach </select> @endif</div><div class='col-4 mb-3'><label>Percentage</label>  <input type='number' name='milestone[" +
                        index +
                        "][percentage]' value='" + percentage[index] +
                        "'  class='form-control per percentage' placeholder='Enter Percentage'></div></div>"
                    $(".add-row").before(add);

                    $($(".milestone").last()).find("option[value='" + milestone[index] + "']").attr(
                        "selected", "selected");
                });
            }

            document.getElementById('commission').value = localStorage.getItem("commission");
            document.getElementById('rera_project_number').value = localStorage.getItem("rera_project_number");
            document.getElementById('construction_status').value = localStorage.getItem("construction_status");
            document.getElementById('construction_date').value = localStorage.getItem("construction_date");


            CKEDITOR.instances['description'].setData(localStorage.getItem("description"));

            var arr = JSON.parse(localStorage.getItem('checked')) || [];
            arr.forEach(function (checked, i) {
                $('.featuresList').eq(i).prop('checked', checked);
            });

            if (localStorage.getItem("payment_plan") === 'Yes') {
                $(".show_hide").show();
            } else {
                $(".show_hide").hide();
            }
            $('.select2').select2();
        }
    });

</script>
<script type="text/javascript" src="{{ asset('public/js/mapInput.js')}}"></script>
<script defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu6xoWPgCs5Pum_0MlSSdseLzDVN7StwQ&libraries=places&callback=initialize">
</script>
@endsection
