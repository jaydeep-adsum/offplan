<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link text-center">
        <span class="brand-text font-weight-light">Real Estate Offplan</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link @if(Request::is('home*')) active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashborad
                        </p>
                    </a>
                </li>

                @if (!Auth::guest() && Auth::user()->role == 1)

                <li class="nav-item">
                    <a href="{{route('manage_agent')}}" class="nav-link @if(Request::is('manage_agent*')) active @endif ">
                        <i class="nav-icon fas fa-user-alt"></i>
                        <p>
                            Manage Users
                        </p>
                    </a>
                </li>
                    <li class="nav-item">
                        <a href="{{route('loginHistory')}}" class="nav-link @if(Request::is('login-history*')) active @endif ">
                            <i class="nav-icon fa fa-history"></i>
                            <p>
                                Login History
                            </p>
                        </a>
                    </li>
                @endif

                @foreach ($permission_menu as $item)
                    @if($item->read && $item->permissions_id == 1)
                        <li class="nav-item">
                            <a href="{{ route('add-milestones') }}" class="nav-link @if(Request::is('add-milestones*')) active @endif">
                                <i class="nav-icon fas fa-money-check"></i>
                                <p>
                                    Payment Milestones
                                </p>
                            </a>
                        </li>
                    @endif
                    @if($item->read && $item->permissions_id == 2)
                        @if((Request::is('manage-User*')) || (Request::is('pending-user*')) ||(Request::is('manage-user*')) || (Request::is('manage-user/add-user*')))
                        @php($class="menu-open")
                        @php($active="active")

                        @else
                        @php($class="")
                        @php($active="")
                        @endif
                        <li class="nav-item has-treeview {{$class}}">
                            <a href="#" class="nav-link {{$active}}">
                                <i class="nav-icon fas fa-user-circle"></i>
                                <p>
                                    Developer
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('manage-user')}}" class="nav-link @if(Request::is('manage-User*') || (Request::is('manage-user/add-user*')) || (Request::is('manage-user/edit-user*')) || (Request::is('manage-user/preview-user*'))) active @endif">
                                        <i class="nav-icon far fa-circle"></i>
                                        <p>
                                            Manage Developer
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('pending-user')}}" class="nav-link @if(Request::is('pending-user*')) active @endif">
                                        <i class="nav-icon far fa-circle"></i>
                                        <p>
                                            Pending Contracts
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if($item->read && $item->permissions_id == 3)
                        <li class="nav-item">
                            <a href="{{ route('manage-features') }}" class="nav-link @if(Request::is('manage-features*')) active @endif ">
                                <i class="nav-icon fas fa-bars"></i>
                                <p>
                                    Manage Features
                                </p>
                            </a>
                        </li>
                    @endif


                  @if($item->read && $item->permissions_id == 4)
                        @if((Request::is('manage-community*'))||(Request::is('add-community')) ||(Request::is('delete-community*'))|| (Request::is('manage-subcommunity*')))
                        @php($class="menu-open")
                        @php($active="active")

                        @else
                        @php($class="")
                        @php($active="")
                        @endif
                        <li class="nav-item has-treeview {{$class}}">
                            <a href="#" class="nav-link {{$active}}">
                                <i class="nav-icon fas fa-bars"></i>
                                <p>
                                    Manage Community
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item @if(Request::is('manage-community*')) {{$class}} @endif">
                                    <a href="{{route('manage-community')}}" class="nav-link @if(Request::is('manage-community*')) active @endif">
                                        <i class="nav-icon far fa-circle"></i>
                                        <p>
                                            Community
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item @if(Request::is('manage-subcommunity*')) {{$class}} @endif">
                                    <a href="{{route('manage-subcommunity')}}" class="nav-link @if(Request::is('manage-subcommunity*')) active @endif">
                                        <i class="nav-icon far fa-circle"></i>
                                        <p>
                                            Sub Community
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                  @endif

                @if($item->read && $item->permissions_id == 5)
                    @if((Request::is('manage_listings*')) ||(Request::is('manage-unit-status*')) ||(Request::is('add-view-unit*')) || (Request::is('edit-unit*')) || (Request::is('copy-unit*')) ||(Request::is('preview-unit*'))||(Request::is('manage-outdated-unit*'))||(Request::is('manage-soldout-unit*')) )
                        @php($class="menu-open")
                        @php($active="active")

                        @else
                        @php($class="")
                        @php($active="")
                        @endif
                    <li class="nav-item has-treeview {{$class}}">
                        <a href="#" class="nav-link {{$active}}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Manage Listings
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('manage_listings')}}" class="nav-link @if(Request::is('manage_listings*') ||Request::is('add-view-unit*') || Request::is('edit-unit*') ||Request::is('copy-unit*') || Request::is('preview-unit*')) active @endif">
                                    <i class="nav-icon far fa-circle"></i>
                                    <p>
                                        Under Construction Units
                                    </p>
                                </a>
                            </li>
                             <li class="nav-item">
                                <a href="{{route('ready_unit_list')}}" class="nav-link @if(Request::is('manage-unit-status*')|| Request::is('ready-edit-unit*') || Request::is('ready-copy-unit*')) active @endif">
                                    <i class="nav-icon far fa-circle"></i>
                                    <p>
                                        Ready Units
                                    </p>
                                </a>
                            </li>
                            @if(Auth::user()->role!=3)
                            <li class="nav-item">
                                <a href="{{route('sold_out_unit_list')}}" class="nav-link @if(Request::is('manage-soldout-unit*')) active @endif">
                                    <i class="nav-icon far fa-circle"></i>
                                    <p>
                                        Sold Out Units
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('outdated_unit_list')}}" class="nav-link @if(Request::is('manage-outdated-unit*')) active @endif">
                                    <i class="nav-icon far fa-circle"></i>
                                    <p>
                                        Outdated Projects
                                    </p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                  @endif
                @endforeach

                @if((Request::is('manage_project*')) || Request::is('manage_ready_project*') || Request::is('manage_sold_out_project*') || Request::is('manage_overdue_project*'))
                    @php($class="menu-open")
                    @php($active="active")

                    @else
                    @php($class="")
                    @php($active="")
                    @endif
                @if(Auth::user()->role!=3)
                <li class="nav-item has-treeview {{$class}}">
                    <a href="#" class="nav-link {{$active}}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Projects
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('projectIndex')}}" class="nav-link @if(Request::is('manage_project*')) active @endif">
                                <i class="nav-icon far fa-circle"></i>
                                <p>
                                    Manage Projects
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('readyProjectIndex')}}" class="nav-link @if(Request::is('manage_ready_project*')) active @endif">
                                <i class="nav-icon far fa-circle"></i>
                                <p>
                                    Ready Projects
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('soldOutProjectIndex')}}" class="nav-link @if(Request::is('manage_sold_out_project*')) active @endif">
                                <i class="nav-icon far fa-circle"></i>
                                <p>
                                    Sold Out Projects
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('overdueProjectIndex')}}" class="nav-link @if(Request::is('manage_overdue_project*')) active @endif">
                                <i class="nav-icon far fa-circle"></i>
                                <p>
                                    Overdue Projects
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if((Request::is('manage-lead-clients*')) || (Request::is('manage-lead-clients/lead_index*')) )
                @php($class="menu-open")
                @php($active="active")

                @else
                @php($class="")
                @php($active="")
                @endif
                <li class="nav-item has-treeview {{$class}}">
                    <a href="#" class="nav-link {{$active}}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Lead Client
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('lead_index')}}" class="nav-link @if(Request::is('manage-lead-clients/lead_create*') || Request::is('manage-lead-clients/lead_index*') || Request::is('manage-lead-clients/lead_edit*') || Request::is('manage-lead-clients/view_lead*')) active @endif">
                                <i class="nav-icon far fa-circle"></i>
                                <p>
                                    Manage Lead Client
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
