@extends('frontend.layouts.app')
@section('title' , 'Notification Detail')
@section('content')
<div class="notification-detail">
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <img src="{{asset('img/notification.png')}}" alt="" style="width: 220px">
                <h6>{{$notification->data['title']}}</h6>
                <p class="mb-1">{{$notification->data['message']}}</p>
                <p class="text-muted mb-3"><small>{{Carbon\Carbon::parse($notification->created_at)->format('Y-m-d H:i:s A') }}</small></p>
                <a href="{{$notification->data['web_link']}}" class="btn btn-theme btn-sm">Continue</a>
            </div>
        </div>
    </div>
</div>
@endsection

