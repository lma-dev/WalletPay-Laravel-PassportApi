@extends('frontend.layouts.app')
@section('title' , 'Wallet Pay')
@section('content')
<div class="home">
    <div class="row">
        <div class="col-12">
            <div class="profile mb-3">
                <img src="https://ui-avatars.com/api/?background=5842E3&color=fff&name={{$user->name}}" alt="">
                <h6>{{ $user->name }}</h6>
                <p class="text-muted">{{ $user->wallet ? number_format($user->wallet->amount) : 0 }}MMK</p>
            </div>
        </div>
        <div class="col-6">
            <div class="card shortcut-box mb-3">
                <div class="card-body p-3">
                   <img src="{{ asset('img/qr-code-scan.png') }}" alt="">
                   <span>Scan & Pay</span>
                </div>
            </div>
        </div>
            <div class="col-6">
                <div class="card shortcut-box mb-3">
                    <div class="card-body p-3">
                       <img src="{{ asset('img/qr-code.png') }}" alt="">
                       <span>Receive QR</span>
                    </div>
                </div>
            </div>
             <div class="col-12">
                <div class="card shortcut-box mb-3 function-box">
                    <div class="card-body pr-0">

                        <a href="{{url('transfer')}}" class="d-flex justify-content-between">
                                <span> <img src="{{ asset('img/money-transfer.png') }}" alt=""> Transfer</span>
                                <span  class="mr-3"> <i class="fas fa-angle-right"></i> </span>
                        </a>

                        <hr>
                        <a href="{{url('wallet')}}" class="d-flex justify-content-between logout">
                            <span> <img src="{{ asset('img/wallet.png') }}" alt=""> Wallet</span>
                            <span  class="mr-3"> <i class="fas fa-angle-right"></i> </span>
                        </a>
                        <hr>
                        <a href="{{url('transaction')}}" class="d-flex justify-content-between logout">
                            <span> <img src="{{ asset('img/transaction.png') }}" alt=""> Transactions</span>
                            <span  class="mr-3"> <i class="fas fa-angle-right"></i> </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
