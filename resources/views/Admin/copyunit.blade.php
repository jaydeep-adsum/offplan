@extends('layouts.admin-app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .message{
        text-align: left;
        padding-left: 55px;
    }
</style>
@section('content')
    <div class="card">
        @if($response = session('response'))
            <div class="alert @if($response['status']) alert-success @else alert-danger @endif" style="margin-top:10px">
                <?php $error =explode(',', $response['message'])?>
                <ul>
                    @foreach ($error as $item)
                        <li>{{ $item  }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-header">
            <div class="container">
                <form class="form-horizontal" method="POST" id="frmTarget" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="id" value="{{$project->id}}">
                    <div class="form-row justify-content-between">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Project Name</label>
                                <select name="project" id="project" class="form-control select2" style="width: 100%">
                                    <option value="0">Select Project</option>
                                    @foreach ($projectList as $key => $item)
                                        <option value="{{$key}}" {{$project->project == $item ? 'selected' : ''}}>{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>No. Of Bedrooms</label>
                                <select class="form-control select2" name="bedrooms">
                                    <option selected disabled>Select bedrooms</option>
                                    <option value="Studio" {{$project->bedrooms == 'Studio' ? 'selected' : '' }}>Studio
                                    </option>
                                    @for ($i = 1; $i <= 20; $i++) <option value="{{$i}}"
                                        {{ $project->bedrooms == $i ? 'selected' : '' }}>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>No. Of Bathrooms</label><br>
                                <input type="number" value="{{$project->bathrooms}}" class="form-control" id="bath"
                                       name="bathrooms" min='0' placeholder="Enter Bathrooms">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Size</label>
                                <div class="input-group mb-3">
                                    <input type="number" min='0' value="{{$project->size}}" class="form-control" id="size"
                                           name="size" placeholder="Enter Size">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">Sq.Ft</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Price</label>
                                <div class="input-group mb-3">
                                    <input type="number" min='0' value="{{$project->price}}" onchange="rowcol(this)" class="form-control am" id="price"
                                           name="price" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==8) return false;" placeholder="Enter Price">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">AED</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row justify-content-between">
                        <div class="col-12 mb-3">
                            <label>Title</label>
                            <textarea class="form-control" name="title" rows="3">{{$project->title}}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Description</label>
                            <textarea class="form-control" id="description" name="description"
                                      rows="3">{!! $project->description !!}</textarea>
                        </div>
                    </div>

                    <div style="border:solid 1px #ddd; padding:10px;" id="cont">
                        <label><i class="fas fa-angle-double-right"></i> Features List</label><br>
                        <div class="grid-container row">
                            @if($featuresList)
                                <?php $feature = json_decode($project['features']); ?>
                                @foreach($featuresList as $featuresName)
                                    <div class="col-3">
                                        <input type="checkbox" name="featuresList[]"
                                               value="{{$featuresName['fname']}}" @if ($feature) @if (in_array($featuresName->fname,
                            $feature))
                                               checked="checked" @endif
                                            @endif >
                                        <label for="vehicle1"> {{$featuresName['fname']}}</label>
                                    </div>
                                @endforeach
                        </div>
                        @else
                        @endif
                    </div>


                    <hr>
                    <div class="row mb-3">
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
                            <select class="form-control select2" id="payment_plan" onchange="payment(this.value)"
                                    name="payment_plan">
                                <option selected disabled>Select Payment Plan</option>
                                <option value="Yes" {{$project->payment_plan == '0' ? 'selected' : '' }}>YES</option>
                                <option value="No" {{$project->payment_plan == '1' ? 'selected' : '' }}>NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="card show_hide"
                         style="padding: 15px 15px 15px 15px; {{$project->payment_plan == '0' ? 'display: block;' : '' }}">
                        <div class="mb-1">
                            <label>1. Amounts to be Payed</label>
                            <hr>
                        </div>
                        <div class="form-row justify-content-between">
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label>Up To Handover Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min='0' readonly="readonly" value="{{$project->up_to_handover}}"
                                               class="form-control" placeholder="Enter Pre-Handover Amount">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label>Post-Handover Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min='0' readonly="readonly" value="{{$project->post_handover}}"
                                               class="form-control" placeholder="Enter Post-Handover Amount">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-row justify-content-between">
                            <div class="col-4 mb-3">
                                <div class="form-group">
                                    <label>Pre-Handover Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min='0' name="pre_handover_amount"
                                               class="form-control" placeholder="Enter Pre-Handover Amount" value="{{$project->pre_handover_amount}}">
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
                                        <input type="number" min='0' name="handover_amount"
                                               class="form-control" placeholder="Enter Handover Amount" value="{{$project->handover_amount}}">
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
                                        <input type="number" min='0' name="post_handover_amount"
                                               class="form-control" placeholder="Enter Post-Handover Amount" value="{{$project->post_handover }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-1">
                            <label>2. Milestone Section</label>
                        </div>
                        @if ( !($project->paymentplan->isEmpty() ))
                            <div class="editRemove">
                                @foreach ($project['paymentplan'] as $key => $payment)
                                    <div class="removeMilestone" data_milestone="{{$key}}">
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6 class='text-center'>Milestone <span class='milestone_number'>{{$i=$key+1}}</span></h6>
                                            </div>
                                            <div class='col-6'>
                                                <h6 class='text-center'><a href='javascript:void(0)' onclick='deleteConfirmation(this)'>Delete</a></h6>
                                            </div>
                                        </div>
                                        <hr>
                                        <input type="hidden" name="milestone[{{$key}}][id]" class="milestone_id" value="{{$payment->id}}">
                                        <div class="form-row justify-content-between mile">
                                            <div class="col-5 mb-3">
                                                <label>Installment Terms</label>
                                                <input type="number" min='0' name="milestone[{{$key}}][installment_terms]"
                                                       value="{{$payment->installment_terms}}" class="form-control installment" placeholder="Enter Installment">
                                            </div>
                                            <div class="col-5 mb-3">
                                                <label>Payment Milestone</label>
                                                @if($milestoneData)
                                                    <select class="form-control select2 milestone" name="milestone[{{$key}}][milestones]">
                                                        <option selected disabled>Select Milestone</option>
                                                        @foreach($milestoneData as $k=>$milestone)
                                                            <option value="{{$milestone}}" {{$payment->milestone == $milestone ? 'selected' : '' }}>
                                                                {{$milestone}}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                            <div class="col-5 mb-3">
                                                <label>Percentage</label>
                                                <input type="number" min='0' name="milestone[{{$key}}][percentage]" onchange="precalc(this)" value="{{$payment->percentage}}"
                                                       class="form-control per percentage" placeholder="Enter Percentage">
                                            </div>
                                            <div class="col-5 mb-3">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" min='0' name="milestone[{{$key}}][amount]" value="{{$payment->amount}}"
                                                               class="form-control total total_amount" placeholder="Enter Amount">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">AED</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="editRemove">
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
                        @endif
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
                                        <input type="file" accept="image/*" name="image[]" id="files" class="form-control"
                                               multiple>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="image_preview" class="row">
                                                    <?php
                                                    $imagelist=json_decode($project['image']);
                                                    $id=$project['id'];
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
                                                                <img class='img-responsive' data_id="{{$i}}" src="{{asset('public/files/profile/'.$image)}}">
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
                                                    $floorplanimage=json_decode($project['floor_plan_image']);
                                                    $i=0;
                                                    $floorplan = array();
                                                    ?>
                                                    @if($floorplanimage)
                                                        @foreach($floorplanimage as $image)
                                                            <?php
                                                            $abc['File']=array("name"=>$image);
                                                            array_push($floorplan,$abc);
                                                            ?>
                                                            <div class='upload-image-floor col-2 text-center' title="{{$image}}" id ="{{$image}}">
                                                                <img class='img-responsive' data_id="{{$i}}" src="{{asset('public/files/profile/'.$image)}}">
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
                                                    $i=0;
                                                    $videos = array();
                                                    ?>
                                                    @if($videolist)
                                                        @foreach($videolist as $video)
                                                            <?php
                                                            $abc['File']=array("name"=>$video);
                                                            array_push($videos,$abc);
                                                            ?>
                                                            <div class='upload-video col-2 text-center' title="{{$video}}" id ="{{$video}}">
                                                                <video data_id="{{$i}}" class="mr-4" style="max-width:100%" controls>
                                                                    <source src="{{asset('public/files/profile/'.$video)}}" type="video/mp4">
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
                                        <input type="file" accept="application/pdf,application/vnd.ms-excel" name="pdf[]" id="pdf"
                                               class="form-control" multiple>
                                    </div>
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="pdf_preview" class="row">
                                                    <?php
                                                    $pdflist = json_decode($project['pdf']);
                                                    $i=0;
                                                    $pdfs = array();
                                                    ?>
                                                    @if($pdflist)
                                                        @foreach($pdflist as $pdf)
                                                            <?php
                                                            $abc['File']=array("name"=>$pdf);
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

                    <div class="card-footer">
                        <div class="row">
                            <button type="submit" id="submit-all" name="submit" class="btn btn-info">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.select2').select2();

        function deleteConfirmation(el)
        {
            var index = $(el).parents(".removeMilestone").remove();
            for (let j = 0; j < $(".editRemove").find('.milestone_number').length; j++) {
                const element = $(".editRemove").find('.milestone_number')[j];
                const milestone_id = $(".editRemove").find('.milestone_id')[j];
                const installment = $(".editRemove").find('.installment')[j];
                const milestone = $(".editRemove").find('.milestone')[j];
                const percentage = $(".editRemove").find('.percentage')[j];
                const total_amount = $(".editRemove").find('.total_amount')[j];
                var a = j + 1;
                $(element).text(a).parents(".removeMilestone").attr("data_milestone", a - 1);
                $(milestone_id).attr("name","milestone[" + (a-1) + "][id]");
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

    <script type="text/javascript">
        $(document).ready(function() {
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
                    var oldpos = $(ui.item[0]).find('img').attr('data_id');
                    for (let j = 0; j < $("#image_preview").find(
                        '.upload-image').length; j++) {
                        const element = $("#image_preview").find(
                            '.upload-image')[j];
                        $(element).find('img').attr('data_id', j);
                    }
                    var newpos = $(ui.item[0]).find('img').attr('data_id');

                    imgArray =  array_move(imgArray,oldpos,newpos);
                }
            });

            $("#floorplanimage_preview").sortable({
                update: function (event, ui) {
                    var oldpos1 = $(ui.item[0]).find('img').attr('data_id');
                    for (let j = 0; j < $("#floorplanimage_preview").find(
                        '.upload-image-floor').length; j++) {
                        const element = $("#floorplanimage_preview").find(
                            '.upload-image-floor')[j];
                        $(element).find('img').attr('data_id', j);
                    }
                    var newpos1 = $(ui.item[0]).find('img').attr('data_id');
                    floorplanimageArray =  array_move(floorplanimageArray,oldpos1,newpos1);

                }
            });

            $("#video_preview").sortable({
                update: function (event, ui) {
                    var oldpos2 = $(ui.item[0]).find('video').attr('data_id');
                    for (let j = 0; j < $("#video_preview").find(
                        '.upload-video').length; j++) {
                        const element = $("#video_preview").find(
                            '.upload-video')[j];
                        $(element).find('video').attr('data_id', j);
                    }
                    var newpos2 = $(ui.item[0]).find('video').attr('data_id');

                    videoArray =  array_move(videoArray,oldpos2,newpos2);
                }
            });

            $('#submit-all').click(function (event) {
                event.preventDefault()
                for (instance in CKEDITOR.instances)
                {
                    CKEDITOR.instances[instance].updateElement();
                }
                var myform = document.getElementById("frmTarget");
                var formData = new FormData(myform);
                formData:$(this).serialize();

                imgArray.forEach(function(el,i){
                    formData.append('filesList['+i+']', el);
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
                    url: "{{route('submit-copy-unit')}}",
                    data: formData,
                    success: function (data) {
                        if (data.status == 1) {
                            swal("Done!", data.message, "success");
                            var segments = location.pathname.split('/');
                            var chksegments = segments[2];
                            if(chksegments == "manage-unit-status")
                            {
                                window.location.href = "{{route('ready_unit_list')}}";
                            }
                            else if(chksegments == "manage-outdated-unit")
                            {
                                window.location.href = "{{ route('outdated_unit_list') }}";
                            }
                            else if(chksegments == "manage-soldout-unit")
                            {
                                window.location.href = "{{ route('sold_out_unit_list') }}";
                            }
                            else
                            {
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
            });
        });
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
            $(".editRemove").append(add);
            if (index !== '0') {
                $(".removeMilestone").find('a').css("visibility", "visible");
            }
            $('.select2').select2();
        });

        function rowcol(el){
            var amount = $(el).val();
            $('.per').each(function(i,sel){
                var percentage = $(sel).val();
                var total = (amount * percentage)/100;
                $(sel).parents(".mile").find(".total").val(total);

            })
        }
        function precalc(sel){
            var amount = $('#price').val();
            var percentage = $(sel).val();
            var total = (amount * percentage)/100 ;
            $(sel).parents(".mile").find(".total").val(total);
        }
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

    <script type="text/javascript" src="{{ asset('public/js/mapInput.js')}}"></script>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu6xoWPgCs5Pum_0MlSSdseLzDVN7StwQ&libraries=places&callback=initialize">
    </script>

@endsection
