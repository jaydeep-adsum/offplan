@extends('layouts.admin-app')

@section('content')
<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card"> -->
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('') }}
                </div>
             <!-- Main content -->
@if (!Auth::guest() && Auth::user()->role ==1)    
    <section class="content">
      <div class="container-fluid">

        <div class="row">

            <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{$listingCount}}<sup style="font-size: 20px"></sup></h3>

                <p>Listing</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="{{route('manage-listing')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
         
          <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{$userCount}}</h3>

                <p>Agent</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{route('manage-user')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
           

          <div class="col-lg-3 col-6">

          </div>

        </div>
      
      </div>
    </section>
     @endif
  

@endsection
