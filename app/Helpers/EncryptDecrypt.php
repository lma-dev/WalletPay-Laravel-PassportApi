<?php

use Hashids\Hashids;

function idtohash($id){
    $hashids = new Hashids('walletpay!@#' , 8); //('hashpassword' , 'total length you want to encrypt')

   return  $hashids->encode($id);
}
function hashtoid($hash){
    $hashids = new Hashids('walletpay!@#' , 8); //('hashpassword' , 'total length you want to encrypt')

    return $hashids->decode($hash)[0];
}
?>
