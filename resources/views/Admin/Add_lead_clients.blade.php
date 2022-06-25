@extends('layouts.admin-app')
<link rel="stylesheet" href="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1569006288/BBBootstrap/choices.min.css?version=7.0.0">
<script src="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1569006273/BBBootstrap/choices.min.js?version=7.0.0"></script>

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        @if($response = session('response'))
        <div class="alert @if($response['status']) alert-success @else alert-danger @endif"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ $response['message'] }}
        </div>
        @endif
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Add Client</h3>
            </div>
            <form class="form-horizontal" method="POST" action="{{route('lead_store')}}"
                enctype="multipart/form-data">
                <div class="card-body">@csrf
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" style="width: 45%" class="form-control"
                                placeholder="Enter Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Phone</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" style="width: 45%" name="phone"
                                placeholder="Enter phone" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" style="width: 45%" name="email"
                                placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Note</label>
                        <div class="col-sm-10">
                            <textarea rows="5" class="form-control" style="width: 45%" name="note"
                                placeholder="Enter note" required></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Project</label>
                        <div class="col-sm-10 example">
                            <select id="choices-multiple-remove-button" placeholder="Select Project" name="project_id[]" multiple>
                                @foreach ($data as $key=>$project_title)
                                    <option value="{{$key}}">{{$key}} - {{$project_title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
     $(document).ready(function(){
        var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
            removeItemButton: true,
        });
    });
</script>
@endsection
