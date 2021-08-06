@extends('frontend.layouts.app')
@section('title' , 'Scan And Pay')
@section('content')
<div class="receive-qr">
    <div class="card my-card">
        <div class="card-body text-center">
            <div class="text-center">
                <img src="{{ asset('img/scan-and-pay.png') }}" alt="" style="width:220px">
            </div>
            <p class="mb-3"> Click button ,put QR code in the frame & pay. </p>
            <button class="btn btn-theme btn-sm"  data-toggle="modal" data-target="#scanModal">Scan</button>

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
{{-- <script src="{{ asset('frontend/js/qr-scanner.umd.m }}in.js')}}"></script> --}}
{{-- <script type="text/javascript" src="{{asset('frontend/js/instascan.min.js')}}"></script> --}}
<script>
    $(document).ready(function(){
        // var videoElem =document.getElementById('scanner');
        // const qrScanner = new QrScanner(videoElem, function(result){
        //     if(result){
        //         qrScanner.stop();
        //         $('#scanModal').modal('hide')
        //     }
        //     console.log(result);
        // });

        $('#scanModal').on('show.bs.modal', function (event) {
            // qrScanner.start();
        });

        $('#scanModal').on('hidden.bs.modal', function (event) {
            // qrScanner.stop();
            this.scanner.stop();
        });

    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
      scanner.addListener('scan', function (content) {
        console.log(content);
      });

        // scanner.addListener('active', () => {
        //     scanner.video.classList.remove('inactive');
        //     scanner.video.classList.add('active');
        // });

        // scanner.addListener('inactive', () => {
        //     scanner.video.classList.remove('active');
        //     scanner.video.classList.add('inactive');
        // });

        // this.emit('inactive');

      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[0]);
        // }if(result){
        //  qrScanner.stop();
        //   $('#scanModal').modal('hide')
        } else {
          console.error('No cameras found.');
        }
      }).catch(function (e) {
        console.error(e);
      });
    });
</script>
@endsection
