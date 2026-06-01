<style>

@media (max-width:991px){
    .logo{
        width: 150px;
        margin-left: 35px;
    }

}

@media (max-width:991px){
    .logo{
        width: 115px;
        margin-left: 30px;
    }

}

</style>
<div class="header">
    <div class="header-left">
        <a href="{{ route('dash') }}" class="logo">
           <img src="{{url('images/logoSAMI.jpg')}}" alt="" class="sami-logo">
        </a>
    </div>
    <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
    <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><i class="fa fa-bars"></i></a>
    {{--}}
    <div class="top-nav-search mob-view">
        <form action="javascript:;">
            <input type="text" class="form-control" placeholder="Search here">
            <a class="btn"><img src="{{ URL::asset('/assets/img/icons/search-normal.svg') }}" alt=""></a>
        </form>
    </div>
    {{--}}
    <ul class="nav user-menu float-end">
        <li class="nav-item">
            <a href="{{url('help')}}" target="_blank" class="nav-link" style="padding-top: 20px" title="Centro de ayuda">
                <img src="{{ URL::asset('/assets/img/icons/support-icon-01.svg') }}" alt="Centro de Ayuda" style="width: 32px; height: 32px;">
            </a>
        </li>
        <li class="nav-item" style="padding-top: 15px; padding-right: 10px;">
            @livewire('settings.language-switcher')
        </li>
        <li class="nav-item dropdown d-none d-md-block">
            @php
                $unreadNotifications = auth()->user()->unreadNotifications()->limit(10)->get();
                $unreadCount = auth()->user()->unreadNotifications()->count();
            @endphp

            <a href="javascript:;" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" style="padding-top: 20px">
                <img src="{{ URL::asset('/assets/img/icons/note-icon-01.svg') }}" alt="">
                @if($unreadCount > 0)
                    <span class="pulse"></span>
                    <span class="badge rounded-pill bg-danger" style="position: absolute; top: 5px; right: 5px; font-size: 10px;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span>{{ __('notifications.title') }}</span>
                    @if($unreadCount > 0)
                        <a href="javascript:void(0);" class="text-primary" onclick="markAllAsRead()" style="float: right; font-size: 12px;">{{ __('notifications.mark_all_as_read') }}</a>
                    @endif
                </div>
                <div class="drop-scroll">
                    <ul class="notification-list" id="notification-list">
                        @forelse($unreadNotifications as $notification)
                            <li class="notification-message {{ $notification->read_at ? '' : 'unread' }}" data-notification-id="{{ $notification->id }}">
                                <a href="javascript:void(0);" onclick="markAsRead('{{ $notification->id }}', '{{ $notification->data['action']['url'] ?? '#' }}')">
                                    <div class="media">
                                        <span class="avatar">
                                            <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="noti-details">
                                                <span class="noti-title">{{ $notification->data['title'] ?? __('notifications.notification') }}</span><br>
                                                <small>{{ $notification->data['message'] ?? '' }}</small>
                                                @if(isset($notification->data['steps']))
                                                    <ul style="font-size: 11px; margin-top: 5px; padding-left: 15px;">
                                                        @foreach($notification->data['steps'] as $step)
                                                            <li>{{ $step }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </p>
                                            <p class="noti-time">
                                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="notification-message">
                                <div class="text-center py-3">
                                    <p class="text-muted">{{ __('notifications.no_notifications') }}</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="{{ route('notifications.index') }}">{{ __('notifications.view_all') }}</a>
                </div>
            </div>
        </li>
        {{--}}
        <li class="nav-item dropdown d-none d-md-block">
            <a href="javascript:void(0);" id="open_msg_box" class="hasnotifications nav-link"><img
                    src="{{ URL::asset('/assets/img/icons/note-icon-02.svg') }}" alt=""><span
                    class="pulse"></span> </a>
        </li>
        {{--}}

        <li class="nav-item dropdown has-arrow user-profile-list">
            <a href="javascript:;" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                <div class="user-names">
                    <h5 class="text-base">{{auth()->user()->full_name}}</h5>
                    <span class="text-base">
                        @if(auth()->user()->getCurrentClient() && !auth()->user()->hasRole('paciente'))
                                {{auth()->user()->getCurrentClient()->name}} ({{auth()->user()->getCurrentClient()->package->name}}) -
                        @endif
                            {{auth()->user()->roles()->first()->name}}
                    </span>
                </div>
                <span class="user-img">
                    @if(!empty(auth()->user()->profile_picture))
                        <img src="{{ url('storage/'.auth()->user()->profile_picture) }}" alt="{{auth()->user()->roles()->first()->name}}"></span>
                    @else
                        <img src="{{ URL::asset('/assets/img/user-06.jpg') }}" alt="{{auth()->user()->roles()->first()->name}}">
                    @endif

                </span>
            </a>
            <div class="dropdown-menu">
                @if(auth()->user()->getCurrentClient() && !auth()->user()->hasRole('paciente'))
                @foreach(auth()->user()->clients()->whereNot('client_id',auth()->user()->getCurrentClient()->id)->get() as $client)
                    <a class="dropdown-item" href="{{route('user.change_client',$client->id)}}"><i class="fa fa-exchange"></i> {{$client->name}}</a>
                @endforeach
                @endif
                @if(auth()->user()->hasRole('paciente') && auth()->user()->patient)
                    <a class="dropdown-item" href="{{ route('patient.profile',auth()->user()->patient->id) }}"> <i class="fa fa-user"></i> {{__('patient.profile')}}</a>
                @elseif(auth()->user()->hasRole('doctor') && auth()->user()->practitioner)
                    <a class="dropdown-item" href="{{ route('practitioner.profile',auth()->user()->practitioner->id) }}"> <i class="fa fa-user-md"></i> {{__('patient.profile')}}</a>
                @endif
                {{--}}<a class="dropdown-item" href="{{ url('logout') }}">{{__('generic.logout')}}</a>{{--}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fa fa-unlock"></i> {{ __('generic.logout') }}
                    </button>
                </form>
            </div>
        </li>
        {{--}}
        <li class="nav-item ">
            <a href="{{ url('settings') }}" class="hasnotifications nav-link"><img   src="{{ URL::asset('/assets/img/icons/setting-icon-01.svg') }}" alt=""> </a>
        </li>
        {{--}}
    </ul>
    {{--}}
    <div class="dropdown mobile-user-menu float-end">
        <a href="javascript:;" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="{{ url('profile') }}">My Profile</a>
            <a class="dropdown-item" href="{{ url('edit-profile') }}">Edit Profile</a>
            <a class="dropdown-item" href="{{ url('settings') }}">Settings</a>
            <a class="dropdown-item" href="{{ url('login') }}">Logout</a>
        </div>
    </div>
   {{--}}
</div>
