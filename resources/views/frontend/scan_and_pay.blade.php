@extends('frontend.layouts.app')
@section('title' , 'Scan And Pay')
@section('content')
<div class="receive-qr">
    <div class="card my-card">
        <div class="card-body text-center">
            @include('frontend.layouts.flash')
            <div class="text-center">
                <img src="{{ asset('img/scan-and-pay.png') }}" alt="" style="width:220px">
            </div>
            <p class="mb-3"> Click button ,put QR code in the frame & pay. </p>
            <button class="btn btn-theme btn-sm scan-btn"  data-toggle="modal" data-target="#scanModal">Scan</button>

            <!-- Scan Modal -->
            <div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="scanModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Scan & Pay</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <video id="preview" width="100%" height="240px"></video>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('frontend/js/instascan.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $('.scan-btn').on('click', function(){
                let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
                scanner.addListener('scan', function (content) {
                    // console.log(content);
                    var to_phone= content;
                    window.location.replace(`scan-and-pay-form?to+phone=${to_phone}`);
                });

                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                    } else {
                    console.error('No cameras found.');
                    }
                }).catch(function (e) {
                    console.error(e);
                });
            });
    });
</script>
@endsection
