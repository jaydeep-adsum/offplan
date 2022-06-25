<html>

<head>
    <title>Regen Real Estate</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="{{ asset('public/site/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/site/fa_icon/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('public/site/css/owl.carousel.min.css')}}">
    <link href="{{ asset('public/site/css/owl.theme.default.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/site/css/final-regen.css')}}">
    <style type="text/css">
        #map {
            width: 100%;
            height: 400px;
        }
    </style>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu6xoWPgCs5Pum_0MlSSdseLzDVN7StwQ&libraries=places&callback=initMap">
    </script>
    <script type="text/javascript">
        function initMap() {
            var lat=parseFloat( '{{$manage_listings['latitude']}}')
            var lng=parseFloat('{{$manage_listings['longitude']}}');

            const myLatLng = { lat: lat, lng: lng };
            console.log(myLatLng);

            var map = new google.maps.Map(document.getElementById('map'), {
                center: myLatLng,
                zoom: 15
            });

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: '{{$manage_listings['location']}}',
                draggable: true
            });

            var infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(marker, 'click', function() {
                var iwContent = '<div id="iw_container">' +
                '<div class="iw_title"><b>Location</b> : {{$manage_listings['location']}}</div></div>';
                infowindow.setContent(iwContent);
                infowindow.open(map, marker);
            });
        }
    </script>
</head>

<body>
    <?php $url= Request::url();?>
    <!--Header Start-->
    <section class="header my-xl-3  my-lg-3 my-md-3 ">
        <div class="container ">
            <div class="bg-white py-xl-2 py-lg-2 py-md-2 py-2 px-3">
                <div class="row">
                    <div class="col-xl col-lg-10 col-md-6 col-12">
                        <div>
                            <div style="float: left;margin-right:10px;">
                                <img src="{{ asset('public/files/logo.png')}} ">
                            </div>
                            <div>
                                <h5 class="heading">
                                    {{$manage_listings->title}} </h5>
                                <h6 style="color:#747474">
                                    @if (!empty($subcommunity[0]->name))
                                        {{$subcommunity[0]->name}},
                                    @endif
                                    @if (!empty($community[0]->name))
                                        {{$community[0]->name}},
                                    @endif
                                    Dubai, UAE
                                </h6>
                                <h6 style="color:#747474">
                                    Property Type : {{$manage_listings['property']}}
                                </h6>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    <!--<div class="col-xl-auto col-lg-auto col-md-6 col-12 text-right">-->
                    <!--    <h5 class="ruppes">AED-->
                    <!--        {{number_format($manage_listings['price'],2, '.', ',')}}-->
                    <!--    </h5>-->
                    <!--    <h6>AED-->
                    <!--        {{number_format((float)$manage_listings['price']/$manage_listings['size'],2, '.', ',')}} per-->
                    <!--        Sq.Ft-->
                    <!--    </h6>-->
                    <!--</div>-->
                    <div class="col-xl-auto col-lg-auto col-md-6 col-12 text-right">
                        @if($manage_listings['price'])
                        <h5 class="ruppes">AED
                            {{number_format($manage_listings['price'],2, '.', ',')}}
                        </h5>
                        @endif
                        @if($manage_listings['size'])
                        <h6 class="ruppes">Size : -
                            {{$manage_listings['size']}} Sq.Ft
                        </h6>
                        @endif
                        @if($manage_listings['price'] && $manage_listings['size'])
                        <h6>AED
                            {{number_format((float)$manage_listings['price']/$manage_listings['size'],2, '.', ',')}} per
                            Sq.Ft
                        </h6>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Header End-->

    <!--Slider start-->
    <section class="">
        <div class="container">
            <div class="row mt-2 mt-md-0">
                <div class="col-xl-9 col-lg-9 col-md-12 col-12">
                    <div class="owl-carousel owl-theme slider">
                        @if(!empty(json_decode($manage_listings->image)))
                        @foreach(json_decode($manage_listings->image) as $image)
                        <div>
                            <img src="{{asset('public/files/profile/'.$image)}}" class="slider-img" />
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-12 scol-12 mt-xl-0 mt-lg-0 mt-md-2 mt-2 d-sm-none d-md-flex d-flex">
                    <div class="card side_card" style="background-color: #f7f7f7">
                        <div class="text-center py-xl-3 py-lg-3 py-3">
                            <img src="{{isset($user_data->image)?asset('public/files/profile/'.json_decode($user_data->image)):asset('public/files/logo.png')}}" class="card-img-top card-image" alt="...">
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <h6 class="m-0">Company Name</h6>
                                <p class="m-0">Regen Real Estate Broker</p>
                            </li>
                            <li class="list-group-item">
                                <h6 class="m-0">Agent Name</h6>
                                <p class="m-0">{{ $user_data['name'] }}</p>
                            </li>
                            <li class="list-group-item">
                                <h6 class="m-0">Mobile Number</h6>
                                <p class="m-0">+{{ $user_data['phone'] }}</p>
                            </li>
                            <li class="list-group-item">
                                <h6 class="m-0">E-mail</h6>
                                <p class="m-0">{{ $user_data['email'] }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row d-sm-flex d-md-none d-none  m-0 mt-2">
                <div class="col-3 text-center pt-3 ">
                    <img src="{{ asset('public/files/logo.png')}}" class="card-img-top card-image" alt="...">
                </div>
                <div class="col-9">
                    <table class="table">
                        <tr>
                            <th width="30%">Comapny Name</th>
                            <td>Regen Real Estate Broker</td>
                        </tr>
                        <tr>
                            <th width="30%">Agent Name</th>
                            <td>{{ $user_data['name'] }}</td>
                        </tr>
                        <tr>
                            <th width="30%">Mobile Number</th>
                            <td>+{{ $user_data['phone'] }}</td>
                        </tr>
                        <tr>
                            <th width="30%">E-mail</th>
                            <td>{{ $user_data['email'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row  my-xl-3 my-lg-3 my-md-3 my-3 ">
                <div class="col-xl-9 col-lg-9 col-md-9 col-12 ">
                    <div class="geryC border m-0 row">
                        <div class="col text-center category">
                            <h5 class="cat">Handover</h5>
                            <h6 class="cat1" style="color:#747474">
                                @if ($manage_listings->quarter && $manage_listings->handover_year)
                                {{ $manage_listings->quarter}},{{$manage_listings->handover_year}}
                                @else
                                -
                                @endif
                            </h6>
                        </div>
                        <div class="col text-center category">
                            <h5 class="cat">Bedroom</h5>
                            <h6 class="cat1" style="color:#747474">
                                @if($manage_listings->bedrooms)
                                    {{$manage_listings['bedrooms']}}
                                @else
                                -
                                @endif
                            </h6>
                        </div>
                        <div class="col text-center category">
                            <h5 class="cat">Bathroom</h5>
                            <h6 class="cat1" style="color:#747474">
                                @if($manage_listings->bathrooms)
                                    {{$manage_listings['bathrooms']}}
                                @else
                                -
                                @endif
                            </h6>
                        </div>
                        <div class="col text-center category">
                            <h5 class="cat">Construction Status By RERA</h5>
                            <h6 class="cat1" style="color:#747474">
                                @if($manage_listings->construction_status)
                                    {{$manage_listings['construction_status']}}%
                                @else
                                -
                                @endif

                                 @if ($manage_listings->construction_date)
                                - {{ date('d-M-Y', strtotime($manage_listings->construction_date))  }}
                                @else
                                -
                                @endif
                            </h6>
                        </div>
                        <div class="col text-center category">
                            <h5 class="cat">Area</h5>
                            <h6 class="cat1" style="color:#747474">
                                @if($manage_listings->size)
                                    {{$manage_listings['size']}} Sq.Ft
                                @else
                                -
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                    <div class="geryC border d-flex h-100 justify-content-between px-2 py-xl-0 py-lg-0 py-md-1 py-1 ">
                        <div class=" icon-div text px-0">
                            <a class="button secondary" title="share it on facebook" rel="nofollow" href="javascript:"
                                target="_blank"
                                onclick="return popitup('http://www.facebook.com/sharer.php?s=100&p[images][0]=?c_id=3216&l_id=1576257627487035&aid=1483396&id=0&wmt=&agft=&image=&wmdummy=1&phdummy=0&p[title]=Elegant 1 Bedroom Apartment I 2 Years Payment Plan I Limited Units Left I Ready to Move -In+&p[summary]=&p[url]={{$url}}')">
                                <img src="{{asset('public/site/image/facebook.png')}}" class="icon">
                            </a>
                        </div>
                        <div class=" icon-div text px-0">
                            <a href="https://twitter.com/share?url={{ $url }}&text=share it on twitter" rel="me"
                                title="Twitter" target="_blank">
                                <img src="{{asset('public/site/image/twitter.png')}}" class="icon"></a>
                        </div>
                        <div class=" icon-div text px-0">
                            <a target="_blank" class="button secondary addthis_button_preferred_2" title="share this"
                                rel="nofollow" href="javascript:"
                                onclick="return popitup('http://www.linkedin.com/shareArticle?mini=true&amp;url={{$url}}')">
                                <img src="{{asset('public/site/image/link.png')}}" class="icon"> </a>
                        </div>
                        <div class=" icon-div text px-0">
                            <a data-toggle="modal" data-target="#emailToFriendModal" href="#" class="button secondary"
                                title="Email property details to a friend" rel="nofollow">
                                <img src="{{asset('public/site/image/mail.png')}}" class="icon"></a>
                        </div>
                        <div class=" icon-div text px-0">

                            <a rel="nofollow" href="javascript:"
                                onclick="return popitup('https://web.whatsapp.com/send?text={{$url}}')"
                                class="button secondary addthis_button_preferred_2"
                                title="WhatsApp property details to a friend" rel="nofollow">
                                <img src="{{asset('public/site/image/whatsapp.png')}}" class="icon"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Slider End-->

    <!--Description Start-->
    @if ($manage_listings->description)
    <section class="mb-xl-3 mb-lg-3 mb-md-3 mb-3">
        <div class="container  ">
            <div class="bg-white px-3 py-xl-2 py-lg-2 py-2">
                <h5 class="description mb-xl-3 mb-lg-3 mb-2">Description</h5>
                <div>
                    @if ($manage_listings->description)
                    {!! $manage_listings->description !!}
                    @else

                    @endif
                </div>
            </div>

        </div>
    </section>
    @endif
    <!--Description End-->

    <!--property-feature start-->
    @if (!empty($manage_listings['features']))
    <section class="property-feature mb-xl-3 mb-lg-3 mb-md-3 mb-3">
        <div class="container  ">
            <div class="bg-white px-3 py-xl-2 py-lg-2 py-md-2 py-2">
                <h5 class=" pro-feature mb-xl-3 mb-lg-3 mb-md-3 mb-3">Property Features</h5>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                        <ul class="fUL" style="">
                            @if (!empty($manage_listings['features']))
                                @foreach(json_decode($manage_listings['features']) as $feature)
                                <li>
                                    <span><i class="fas fa-chevron-circle-right arrow mr-2"></i></span>{{$feature}}
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--property-feature end-->

    <!--Amount start-->
      <section class="mb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header header">
                            <h5 class="amount desc m-0">Amount to be paid</h5>
                        </div>
                        <div class="card-body p-0" style="overflow-x:auto;">
                            <table class="table table-striped table-bordered m-0">
                                <tr>
                                    <td>Pre Handover Amount</td>
                                    <td>
                                        @if ($manage_listings->pre_handover_amount)
                                        AED {{number_format($manage_listings->pre_handover_amount,2, '.', ',')}}
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Handover Amount</td>
                                    <td>
                                        @if ($manage_listings->handover_amount)
                                        AED {{number_format($manage_listings->handover_amount,2, '.', ',')}}
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Post Handover Amount</td>
                                    <td>
                                        @if ($manage_listings->post_handover)
                                        AED {{number_format($manage_listings->post_handover,2, '.', ',')}}
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Amount end-->

    <!--payment start-->
    @if ( !($manage_listings->paymentplan->isEmpty() ))
    <section class="mb-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header header">
                            <h5 class="payment desc m-0">Payment Details</h5>
                        </div>
                        <div class="card-body" style="overflow-x:auto;">
                            @foreach ($manage_listings['paymentplan'] as $key=>$payment)
                                {{ $payment->percentage }}%
                                On
                                @if($payment->installment_terms != 0)
                                    {{ $payment->installment_terms }}
                                @endif
                                {{ $payment->milestone }}
                                @if($payment->milestone == "Handover")
                                    {{ $manage_listings->quarter}} {{$manage_listings->handover_year}}
                                @endif
                                <br>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--payment end-->

    <!--floor plan start-->
    @if(!empty(json_decode($manage_listings->floor_plan_image)))
    <section class="mb-3">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5 class="floor">Floor Plan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 owl-carousel owl-theme slider">
                            @if(!empty(json_decode($manage_listings->floor_plan_image)))
                                @foreach(json_decode($manage_listings->floor_plan_image) as $image)
                                <div>
                                    <img src="{{asset('public/files/profile/'.$image)}}" class="w-100" />
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--floor plan end-->

    <!--Video start-->
    @if ($manage_listings->video)
    <section class="mb-3">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5 class="video">Video</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 owl-carousel owl-theme slider">
                            @if ($manage_listings->video)
                                @foreach (json_decode($manage_listings->video) as $items)
                                    <video controls class="w-100">
                                        <source src="{{asset('public/files/profile/'.$items)}}" type="video/mp4">
                                    </video>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--Video end-->

    <!--Location Start-->
    <section class="mb-xl-3 mb-lg-3 mb-md-3 mb-3">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12 map">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </section>
    <!--Location End-->

    <div id="emailToFriendModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
           <div class="modal-content">
              <div class="modal-header">
                 <h4 class="modal-title">Share Email Property to a Friend</h4>
                  <button type="button" class="close" data-dismiss="modal" style="color:rgb(255,0,0 );" > &times;</button>
              </div>
              <div class="modal-body">
                 <form class="fill-up" enctype="multipart/form-data" id="email-to-friend" action="{{route('mail-send',['id' => $manage_listings['id']])}}" method="post"> @csrf
                     <div class="row">
                    <div class="form-group col-6">
                       <label for="ListingToFriend_name" class="required">Name <span class="required">*</span></label>
                       <input class="form-control" required="1" name="ListingToFriend[name]" id="ListingToFriend_name" type="text" maxlength="255" />                         </div>
                    <div class="form-group col-6">
                       <label for="ListingToFriend_email" class="required">Email <span class="required">*</span></label>
                        <input type="email" value="" require="required" class='form-control' name="ListingToFriend[friend_email]" id="ListingToFriend_email" />
                        </div>
                    <div class="form-group col-6">
                       <label for="ListingToFriend_friend_name" class="required">from Name <span class="required">*</span></label>
                        <input class="form-control" required="1" name="ListingToFriend[fromname]" id="ListingToFriend_friend_name" type="text" maxlength="255" />
                    </div>
                    <div class="form-group col-6">
                       <label for="ListingToFriend_friend_email" class="required">from Email <span class="required">*</span></label>
                       <input type="email" value="" require="required" class='form-control' name="ListingToFriend[email]" id="ListingToFriend_friend_email" />
                     </div>
                 </div>
                 <div class="form-group">
                    <label for="ListingToFriend_message">Message</label>
                    <textarea class="form-control" rows="6" cols="36" required="1" name="ListingToFriend[message]" id="ListingToFriend_message">Checkout this property

                 {{$url}}
                    </textarea>
                </div>
                 <div class="form-group row">
                    <div class="col-xs-12">
                    </div>
                 </div>
              </div>
              <div class="modal-footer">
                 <button type="submit" type="submit" class="btn btn-success"><i class="icon-ok"></i> Send Message</button>
              </div>
              </form>      </div>
        </div>
    </div>
    <div class="container">
        <p><b>Note :</b> Kindly note that Images in Listing are Indicative for Typically similar Units,
            Location Pin (GPS) is often not accurately picked up by the Portal for the exact Units.</p>
    </div>
    <!--Footer Start-->
    <section class="header">
        <div class="container ">
            <div class="bg-white py-xl-2 py-lg-2 py-md-2 py-2 px-3">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                        <div class="row">
                            <div class="logo col-xl-2 col-lg-2 col-md-2 col-2">
                                <img src="{{ asset('public/files/logo.png')}}">
                            </div>
                            <div class="col-xl-10 col-lg-10 col-md-10 col-10 pl-xl-3 pl-lg-4 pl-md-5 pl-5">
                                <h5 class="footer">Regen Real Estate Brokers</h5>
                                <h6 class="footer1">Office 110, CBD Building, Sheikh Zayed Road,<br>
                                    P.O Box 11562 - Dubai,<br>
                                    United Arab Emirates.
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-12 text-right">
                        <h6 class="footer1"><a>www.regenbrokers.com</a></h6>
                        <h6 class="footer1"><a>inquiries@regenbrokers.com</a></h6>
                        <h6 class="footer1">+97143175300</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Footer End-->


    <script src="{{ asset('public/site/fa_icon/js/all.js')}}"></script>
    <script src="{{ asset('public/site/js/jquery.min.js')}}"></script>
    <script src="{{ asset('public/site/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('public/site/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('public/site/js/owl.carousel.min.js')}}"></script>

    <script>
        $(".owl-carousel").owlCarousel({
            loop: true,
            nav: true,
            items: 1,
        });


        function popitup(url) {
            var left = (screen.width / 2) - (750 / 2);
            var top = (screen.height / 2) - (420 / 2);
            newwindow = window.open(url, 'name', 'height=420, width=750, top=' + top + ', left=' + left);
            if (window.focus) {
                newwindow.focus()
            }
            return false;
        }

    </script>

</body>

</html>
