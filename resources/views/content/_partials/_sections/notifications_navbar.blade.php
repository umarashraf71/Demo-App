@php
    $user = auth()->user();
    $query = \App\Models\Notification::where('is_read',0)->where('to',$user->id);
    $unread_notifications = $query->orderBy('_id','desc')->get()->take(3);
    $unread_count = $query->count();
@endphp

<li class="nav-item dropdown dropdown-notification me-25">
    <a class="nav-link" href="#" data-bs-toggle="dropdown">
        <i class="ficon" data-feather="bell"></i>
        <span class="badge rounded-pill bg-danger badge-up">{{$unread_count}}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
        <li class="dropdown-menu-header">
            <div class="dropdown-header d-flex">
                <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                <div class="badge rounded-pill badge-light-primary">{{$unread_count}} New</div>
            </div>
        </li>
        <li class="scrollable-container media-list">
{{--            <a class="d-flex" href="#">--}}
{{--                <div class="list-item d-flex align-items-start">--}}
{{--                    <div class="me-1">--}}
{{--                        <div class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-3.jpg" alt="avatar" width="32" height="32"></div>--}}
{{--                    </div>--}}
{{--                    <div class="list-item-body flex-grow-1">--}}
{{--                        <p class="media-heading"><span class="fw-bolder">New message</span>&nbsp;received</p><small class="notification-text"> You have 10 unread messages</small>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </a>--}}
            @forelse($unread_notifications as $notification)
                <a class="d-flex cursor-default" href="#">
                    <div class="list-item d-flex align-items-start">
                        <div class="me-1">
                                <div class="avatar bg-light-danger">
                                    {{--<div class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-3.jpg" alt="avatar" width="32" height="32"></div>--}}
                                    <div class="avatar-content">AZ</div>
                                </div>
                        </div>
                            <div class="list-item-body flex-grow-1">
                                <p class="media-heading"><span class="fw-bolder">{{\App\Models\Notification::$type[$notification->type]}}</p>
                                <small class="notification-text"> {{$notification->message}}</small>
                                <small class="float-end notification-text"> {{\Carbon\Carbon::parse($notification->created_at)->diffForHumans()}}</small>
                            </div>
                    </div>
                </a>
            @empty
                <a class="d-flex cursor-default" href="#">
                    <div class="list-item d-flex align-items-start">
                        <div class="list-item-body flex-grow-1 text-center text-dark">
                            0 Notification found
                        </div>
                    </div>
                </a>
            @endforelse
            <div class="list-item d-flex align-items-center">
                <h6 class="fw-bolder me-auto mb-0">System Notifications</h6>
                <div class="form-check form-check-primary form-switch">
                    <input class="form-check-input" id="systemNotification" type="checkbox" checked="">
                    <label class="form-check-label" for="systemNotification"></label>
                </div>
            </div>
{{--            <a class="d-flex" href="#">--}}
{{--                <div class="list-item d-flex align-items-start">--}}
{{--                    <div class="me-1">--}}
{{--                        <div class="avatar bg-light-danger">--}}
{{--                            <div class="avatar-content"><i class="avatar-icon" data-feather="x"></i></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="list-item-body flex-grow-1">--}}
{{--                        <p class="media-heading"><span class="fw-bolder">Server down</span>&nbsp;registered</p><small class="notification-text"> USA Server is down due to high CPU usage</small>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </a>--}}
            <a class="d-flex" href="#">
                <div class="list-item d-flex align-items-start">
                    <div class="me-1">
                        <div class="avatar bg-light-success">
                            <div class="avatar-content"><i class="avatar-icon" data-feather="check"></i></div>
                        </div>
                    </div>
                    <div class="list-item-body flex-grow-1">
                        <p class="media-heading"><span class="fw-bolder">Sales report</span>&nbsp;generated</p><small class="notification-text"> Last month sales report generated</small>
                    </div>
                </div>
            </a>
{{--            <a class="d-flex" href="#">--}}
{{--                <div class="list-item d-flex align-items-start">--}}
{{--                    <div class="me-1">--}}
{{--                        <div class="avatar bg-light-warning">--}}
{{--                            <div class="avatar-content"><i class="avatar-icon" data-feather="alert-triangle"></i></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="list-item-body flex-grow-1">--}}
{{--                        <p class="media-heading"><span class="fw-bolder">High memory</span>&nbsp;usage</p><small class="notification-text"> BLR Server using high memory</small>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </a>--}}
        </li>
        <li class="dropdown-menu-footer"><a class="btn btn-primary w-100" href="{{route('notifications')}}">Read all notifications</a></li>
    </ul>
</li>
