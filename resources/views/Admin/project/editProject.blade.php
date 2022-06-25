@extends('layouts.admin-app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .message {
        text-align: left;
        padding-left: 55px;
    }

</style>
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

                <input type="hidden" name="id" value="{{$project->id}}">

                <div class="form-row justify-content-between">
                    <div class="col-5">
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" name="project" value="{{$project->project}}" id="project" class="form-control"
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
                                <option value="{{$key}}" {{$project['developer']['id'] == $key ? 'selected' : $company }}>{{$company}}</option>
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
                                <option value="{{$item}}" {{$item == $project->property ? 'selected' : $item }}>{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <label>Select Completion Status</label>
                            <select name="completion_status" class="form-control select2" id="completion_status" onchange="completionStatusHideShow(this.value)">
                                <option value="0" selected disabled>Select Completion Status</option>
                                <option value="1" {{$project->completion_status == '1' ? 'selected' : '' }}>Ready</option>
                                <option value="2" {{$project->completion_status == '2' ? 'selected' : '' }}>Quarter and Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row justify-content-between completion_status_hide_show" style="{{$project->completion_status == '1' ? 'display: none;' : ''}}">
                    <div class="col-5">
                        <div class="form-group">
                            <label>Select Quater</label>
                            <select class="form-control select2" id="quarter" name="quarter" style="width: 100%">
                                <option selected disabled>Select Quarter</option>
                                <option value="Q1" {{$project->quarter == 'Q1' ? 'selected' : '' }}>Q1</option>
                                <option value="Q2" {{$project->quarter == 'Q2' ? 'selected' : '' }}>Q2</option>
                                <option value="Q3" {{$project->quarter == 'Q3' ? 'selected' : '' }}>Q3</option>
                                <option value="Q4" {{$project->quarter == 'Q4' ? 'selected' : '' }}>Q4</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <label>Select Handover Year</label>
                            <input type="number" name="handover_year" value="{{$project->handover_year}}" min='0' id="handover_year" class="form-control"
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
                                    <option value="{{$communityData->id}}" {!! ($project->community == $communityData->id) ? 'selected' :  "" !!}>{{$communityData->name}}</option>
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
                                    @if($subcommunity)
                                    @foreach ($subcommunity as $item)
                                        <option value="{{$item->id}}"{!! ($project->subcommunity == $item->id) ? 'selected' :  "" !!}>{{$item->name}}</option>
                                    @endforeach
                                @endif
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
                        <input type="text" id="address-input" name="location" value="{{$project['location']}}" class="form-control map-input" placeholder="Enter Location">
                        <input type="hidden" name="latitude" id="address-latitude" value="{{$project['latitude']}}" />
                        <input type="hidden" name="longitude" id="address-longitude" value="{{$project['longitude']}}" />
                    </div>
                </div>
                <div id="address-map-container" class="mb-2" style="width:100%; height:400px; ">
                    <div style="width: 100%; height: 100%" id="address-map">
                    </div>
                </div>

                <div style="border:solid 1px #ddd; padding:10px;" class="form-group new-bedrooms">
                    <label><i class="fas fa-angle-double-right"></i>Availability with Price Range</label><br>
                    @if($project['projectBedrooms']->isEmpty())
                        <div data_contact="0" class="form-group row justify-content-between contact">
                            <div class="col-3">
                                <label for="bed_rooms">Bedrooms</label>
                                <select name="bedrooms[0][bed_rooms]" class="form-control select2 bed_rooms" id="bed_rooms">
                                    <option value="" selected disabled>Select Bedrooms</option>
                                    <option value="Studio">Studio</option>
                                    @for ($i = 1; $i <= 20; $i+=0.5) 
                                        <option value="{{$i}}">{{$i}}</option>
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
                    @else
                        @foreach ($project['projectBedrooms'] as $key => $item)
                            <div data_contact="{{$key}}" class="form-group row justify-content-between contact">
                                <input type="hidden" name="bedrooms[{{$key}}][id]" id="bedrooms_id" value="{{$item->id}}">
                                <div class="col-3">
                                    <label for="bed_rooms">Bedrooms</label>
                                    <select name="bedrooms[{{$key}}][bed_rooms]" class="form-control select2 bed_rooms">
                                        <option value="" selected disabled>Select Bedrooms</option>
                                        <option value="Studio" {{$item->bed_rooms == "Studio" ? 'selected' : ''}}>Studio</option>
                                        @for ($i = 1; $i <= 20; $i+=0.5) 
                                            <option value="{{$i}}" {{$item->bed_rooms == $i ? 'selected' : ''}}>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label for="min_price">Min Price</label>
                                    <input type="number" name="bedrooms[{{$key}}][min_price]" placeholder="Enter Min Price"
                                        class="form-control min_price" value="{{$item->min_price}}">
                                </div>
                                <div class="col-3">
                                    <label for="max_price">Max Price</label>
                                    <input type="number" name="bedrooms[{{$key}}][max_price]" value="{{$item->max_price}}" placeholder="Enter Max Price" class="form-control max_price">
                                </div>
                                <div class="col-1" style="align-self: end;">
                                    <span class="card-header-right-span">
                                        @if($key)
                                            <button class="btn btn-danger" style="padding:9px 11px;" onclick="deleteConfirmation({{$item->id}}, this)">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-info add-bedrooms" style="padding: 9px 11px;">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="form-row justify-content-between">
                    <div class="col-3 mb-3">
                        <label>Select Payment Plan</label>
                        <select class="form-control select2" id="payment_plan" onchange="payment(this.value)"
                            name="payment_plan">
                            <option selected disabled>Select Payment Plan</option>
                            <option value="Yes" {{$project->payment_plan == '1' ? 'selected' : '' }}>YES</option>
                            <option value="No" {{$project->payment_plan == '0' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div class="col-9 mb-3">
                        <label>Comments</label>
                        <input type="text" name="payment_plan_comments" value="{{$project->payment_plan_comments}}" class="form-control" id="payment_plan_comments" placeholder="Enter Comments">
                    </div>
                </div>

                <button type="button" class="btn float-right btn-info font-weight-bold more_details_button_hide_show">
                    More Details....
                </button>
                <div style="clear:both"></div>

                <div class="show_more_details">
                    <div class="card removeDisplayNone" style="padding: 15px 15px 15px 15px; {{$project->payment_plan == '0' ? 'display: none;' : '' }}">
                        @if ( !($project->paymentPlanDetails->isEmpty() ))
                        @foreach ($project['paymentPlanDetails'] as $key => $payment)
                        <div class="mb-1">
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-center">Milestone {{$i=$key+1}}</h6>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-center"><a href="{{ route('deleteProjectmilestone', ['id' => $payment['id']]) }}">Delete</a></h6>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <input type="hidden" name="milestone[{{$key}}][id]" value="{{$payment->id}}">
                        <div data_milestone="{{$key}}" class="form-row justify-content-between mile">
                            <div class="col-4 mb-3">
                                <label>Installment Terms</label>
                                <input type="number" min='0' name="milestone[{{$key}}][installment_terms]"
                                    value="{{$payment->installment_terms}}" class="form-control" placeholder="Enter Installment">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Payment Milestone</label>
                                @if($milestoneData)
                                <select class="form-control select2" name="milestone[{{$key}}][milestones]" style="width: 100%">
                                    <option selected disabled>Select Milestone</option>
                                    @foreach($milestoneData as $k=>$milestone)
                                        <option value="{{$milestone}}" {{$payment->milestone == $milestone ? 'selected' : '' }}>{{$milestone}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="col-4 mb-3">
                                <label>Percentage</label>
                                <input type="number" min='0' name="milestone[{{$key}}][percentage]" value="{{$payment->percentage}}"
                                     class="form-control per" placeholder="Enter Percentage">
                            </div>
                        </div>
                        @endforeach
                        @else

                        <div class="mb-1">
                            <hr><h6 class='text-center'>Milestone 1</h6><hr>
                        </div>
                        <div class="form-row justify-content-between mile">
                            <div class="col-4 mb-3">
                                <label>Installment Terms</label>
                                <input type="number" min='0' name="milestone[0][installment_terms]"
                                    class="form-control" placeholder="Enter Installment">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Payment Milestone</label>
                                @if($milestoneData)
                                <select class="form-control select2" name="milestone[0][milestones]" style="width: 100%">
                                    <option selected disabled>Select Milestone</option>
                                    @foreach($milestoneData as $key=>$milestone)
                                    <option value="{{$milestone}}">{{$milestone}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="col-4 mb-3 abc">
                                <label>Percentage</label>
                                <input type="number" min='0' name="milestone[0][percentage]" class="form-control per" placeholder="Enter Percentage" >
                            </div>
                        </div>
                        @endif
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
                                    <input type="number" min='0' class="form-control" value="{{$project->commission ? $project->commission : ""}}" id="commission" name="commission"
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
                                <input type="text" class="form-control" value="{{$project->rera_permit_no}}"  id="rera_project_number" name="rera_permit_no"
                                    placeholder="Enter RERA Number">
                            </div>
                        </div>
                    </div>
                    <div class="form-row justify-content-between">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Construction Status By RERA</label>
                                <div class="input-group mb-3 ">
                                    <input type="number" min='0' class="form-control" value="{{$project->construction_status ? $project->construction_status : ""}}" id="construction_status"
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
                                    <input type="date" class="form-control" value="{{$project->construction_date}}" id="construction_date" name="construction_date">
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
                            <textarea class="form-control" id="description" name="description" rows="3">{{$project->description}}</textarea>
                        </div>
                    </div>
    
                    <div style="border:solid 1px #ddd; padding:10px;" id="cont">
                        <label><i class="fas fa-angle-double-right"></i>Features List</label><br>
                        <div class="grid-container row">
                            @if($featuresList)
                            <?php $feature = json_decode($project['features']); ?>
                            @foreach($featuresList as $key=>$featuresName)
                            <div class="col-3">
                                <input type="checkbox" id="featuresList{{$key}}" class="featuresList" name="featuresList[]"
                                    value="{{$featuresName['fname']}}" @if ($feature) @if (in_array($featuresName->fname,
                                    $feature))
                                    checked="checked" @endif
                                    @endif >
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
                                            <div id="image_preview" class="row">
                                                <?php 
                                                    $imagelist = json_decode($project['image']);
                                                    $id = $project['id'];  
                                                    $i = 0;
                                                    $test = array();
                                                ?> 
                                                @if($imagelist)
                                                    @foreach($imagelist as $image)
                                                        <?php
                                                            $abc['File'] = array("name"=>$image);
                                                            array_push($test,$abc);
                                                        ?>
                                                        <div class='upload-image col-2 text-center' title="{{$image}}" id ="{{$image}}">
                                                            <img class='img-responsive' data_id="{{$i}}" src="{{asset('public/projectFiles/images/'.$image)}}">
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
                                            <div id="floorplanimage_preview" class="row">
                                                <?php 
                                                    $floorplanimage = json_decode($project['floor_plan_image']);
                                                    $i = 0;
                                                    $floorplan = array();
                                                ?> 
                                                @if($floorplanimage)
                                                    @foreach($floorplanimage as $image)
                                                        <?php
                                                            $abc['File'] = array("name" => $image);
                                                            array_push($floorplan,$abc);
                                                        ?>
                                                        <div class='upload-image-floor col-2 text-center' title="{{$image}}" id ="{{$image}}">
                                                            <img class='img-responsive' data_id="{{$i}}" src="{{asset('public/projectFiles/floor_plan_image/'.$image)}}">
                                                            <span class="remove btn btn-danger" onclick="removefloor(this)">&times; </span>
                                                        </div>
                                                        <?php $i++;?>
                                                    @endforeach
                                                @endif
                                            </div>
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
                                            <div id="video_preview" class="row">
                                                <?php 
                                                    $videolist = json_decode($project['video']);
                                                    $i = 0;
                                                    $videos = array();
                                                ?> 
                                                @if($videolist)
                                                    @foreach($videolist as $video)
                                                        <?php
                                                        $abc['File'] = array("name"=>$video);
                                                        array_push($videos,$abc);
                                                        ?>
                                                        <div class='upload-video col-2 text-center' title="{{$video}}" id ="{{$video}}">
                                                            <video data_id="{{$i}}" class="mr-4" style="max-width:100%" controls>
                                                                <source src="{{asset('public/projectFiles/video/'.$video)}}" type="video/mp4">
                                                            </video>
                                                            <span class="remove btn btn-danger" onclick="removevideo(this)">&times; </span>
                                                        </div>
                                                        <?php $i++;?>
                                                    @endforeach
                                                @endif
                                            </div>
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
                                            <div id="pdf_preview" class="row">
                                                <?php 
                                                    $pdflist = json_decode($project['pdf']);
                                                    $i = 0;
                                                    $pdfs = array();
                                                ?> 
                                                @if($pdflist)
                                                    @foreach($pdflist as $pdf)
                                                        <?php
                                                        $abc['File'] = array("name"=>$pdf);
                                                        array_push($pdfs,$abc);
                                                        ?>
                                                        <div class='upload-pdf col-2 text-center' title="{{$pdf}}" id ="{{$pdf}}">
                                                            <p data_id="{{$i}}" data_id>{{$pdf}}</p>
                                                            <span class="remove btn btn-danger" onclick="removepdf(this)">&times; </span>
                                                        </div>
                                                        <?php $i++;?>
                                                    @endforeach
                                                @endif
                                            </div>
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

    var lastcount = $("#image_preview div img").last().attr('data_id');
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
            if (!f.type.match('image.*')) {
                    continue;
            }
            html = "<div class='upload-image col-2 text-center'><img class='img-responsive' data_id=" + (lastcount) + " src='" + URL.createObjectURL(document.getElementById("files").files[i]) +"'><span class=\"remove btn btn-danger\" onclick='removeitem(this)'>&times; </span></div>"
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

<!-- start script for upload second image-->
<script type="text/javascript">
    var lastfloorcount = $("#floorplanimage_preview div img").last().attr('data_id');
    var floorplanimageArray = [];
    lastfloorcount=parseInt(lastfloorcount)+1; 

    "<?php foreach($floorplan as $value) { ?>"
    var f = new File([""], "<?php echo $value['File']['name']; ?>");
    floorplanimageArray.push(f);
    "<?php } ?>"

    
    document.getElementById("floorplanimage").onchange = function (event) {
        if(isNaN(lastfloorcount)){
            lastfloorcount=0;
        }
        var files = event.target.files;
        for (var i = 0; i < files.length; i++) {
            var f = files[i];
            if (!f.type.match('image.*')) {
                    continue;
            }
            html = "<div class='upload-image-floor col-2 text-center'><img class='img-responsive' data_id=" + (lastfloorcount) + " src='" + URL.createObjectURL(document.getElementById("floorplanimage").files[i]) +"'><span class=\"remove btn btn-danger\" onclick='removefloor(this)'>&times; </span></div>"
            $("#floorplanimage_preview").append(html);

            floorplanimageArray.push(document.getElementById("floorplanimage").files[i]);
            lastfloorcount++;
        }
    }

    function removefloor(el) {
        var index = $(el).parent(".upload-image-floor").find("img").attr("data_id");

        floorplanimageArray.splice(index, 1);
        $(el).parent(".upload-image-floor").remove();
        for (let j = 0; j < $("#floorplanimage_preview").find('.upload-image-floor').length; j++) {
            const element = $("#floorplanimage_preview").find('.upload-image-floor')[j];
            $(element).find('img').attr('data_id', j);
        }
    }
</script>
<!--end script for upload second image-->

<!-- start script for upload video-->
<script type="text/javascript">
    var lastvideocount = $("#video_preview div video").last().attr('data_id');
    var videoArray = [];
    lastvideocount=parseInt(lastvideocount)+1; 

    "<?php foreach($videos as $value) { ?>"
    var f = new File([""], "<?php echo $value['File']['name']; ?>");
    videoArray.push(f);
    "<?php } ?>"

    
    document.getElementById("in_video").onchange = function (event) {
        if(isNaN(lastvideocount)){
            lastvideocount=0;
        }
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
<!--end script for upload video-->

<!-- start script for upload pdf-->
<script type="text/javascript">
    var lastpdfcount = $("#pdf_preview div p").last().attr('data_id');
    var pdfArray = [];
    lastpdfcount=parseInt(lastpdfcount)+1; 

    "<?php foreach($pdfs as $value) { ?>"
    var f = new File([""], "<?php echo $value['File']['name']; ?>");
    pdfArray.push(f);
    "<?php } ?>"

    
    document.getElementById("pdf").onchange = function (event) {
        if(isNaN(lastpdfcount)){
            lastpdfcount=0;
        }
        var files = event.target.files;
        for (var i = 0; i < files.length; i++) {
            var f = files[i];
            if (!f.type.match('pdf.*')) {
                    continue;
            }
            html = '<div class="upload-pdf col-2 text-center"><p data_id = ' + (lastpdfcount) + ' > ' + f.name +
                ' </p><span class=\"remove btn btn-danger\" onclick="removepdf(this)">&times; </span></div>'
            $("#pdf_preview").append(html);

            pdfArray.push(document.getElementById("pdf").files[i]);
            lastpdfcount++;
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
            $(".removeDisplayNone").css('display','block');
        } else {
            $(".show_hide").hide();
            $(".removeDisplayNone").css('display','none');
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
    $(".add-row").click(function () {
        var index = $(".mile").last().attr("data_milestone") ?? 0;
        index++;

        var add =
            "<hr><h6 class='text-center'>Milestone " + (index + 1) + "</h6><hr><div data_milestone=" + index +
            " class='form-row justify-content-between mile'><div class='col-4 mb-3'><label>Installment Terms</label><input type='number' min='0' name='milestone[" +
            index +
            "][installment_terms]' class='form-control' placeholder='Enter Installment'> </div><div class='col-4 mb-3'> <label>Payment Milestone</label>@if($milestoneData)<select class='form-control select2' name='milestone[" +
            index +
            "][milestones]'  style='width: 100%'> <option selected disabled>Select Milestone</option> @foreach($milestoneData as $key=>$milestone)<option value='{{$milestone}}'>{{$milestone}}</option>@endforeach </select> @endif</div><div class='col-4 mb-3'><label>Percentage</label>  <input type='number' min='0' name='milestone[" +
            index +
            "][percentage]' class='form-control per' placeholder='Enter Percentage'></div></div>"
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
                url: "{{route('editProjectSubmit')}}",
                data: formData,

                success: function (data) {
                    if (data.status == 1) {
                        swal("Done!", data.message, "success");
                        // var segments = location.pathname.split('/');
                        // var chksegments = segments[3];
                        // if (chksegments == "manage-unit-status") {
                        //     window.location.href = "{{route('ready_unit_list')}}";
                        // } else {
                            // window.location.href = "{{route('projectIndex')}}";
                            location.reload();
                        // }
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
    });

    function deleteConfirmation(id, el) {
        swal({
            title: "Delete?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {
                $.ajax({
                    url: "{{route('bedroomsDelete')}}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (results) {
                        if (results.status) {
                            swal({
                                title: "Done!",
                                text: results.message,
                                type: "success"
                            }).then(function() {
                                var index = $(el).parents(".contact").remove();
                            });
                        } else {
                            swal("Error!", results.message, "error");
                        }
                    }
                });
            } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })
    }
</script>
<script type="text/javascript" src="{{ asset('public/js/mapInput.js')}}"></script>
<script defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu6xoWPgCs5Pum_0MlSSdseLzDVN7StwQ&libraries=places&callback=initialize">
</script>
@endsection
