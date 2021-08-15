<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

   public function register(Request $request){
    $request->validate( [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
        'phone' => 'required|unique:users,phone',
    ]);
    $user=new User();
    $user->name=$request->name;
    $user->email=$request->email;
    $user->phone=$request->phone;
    $user->password=Hash::make($request->name);
    $user->ip = $request->ip();
    $user->user_agent = $request->server('HTTP_USER_AGENT');
    $user->login_at = date('Y-m-d H:i:s');
    $user->save();

    $token = $user->createToken('Wallet Pay')->accessToken;
    return success('Succesfully registered.',['token'=>$token]);
   }

   public function login(Request $request){
    $request->validate([
        'phone' => ['required','string'],
        'password' => ['required', 'string']
    ]);
    if(Auth::attempt(['phone'=> $request->phone,'password'=>$request->password])){
        $user=auth()->user();
        $user->ip = $request->ip();
        $user->user_agent = $request->server('HTTP_USER_AGENT');
        $user->login_at = date('Y-m-d H:i:s');
        $user->update();

        $token = $user->createToken('Wallet Pay')->accessToken;
        return success('Succesfully Login.',['token'=>$token]);
    }
    return fail('These credentials do not match our records.',null);
   }

   public function logout(){
   $user = auth()->user();
   $user->token()->revoke();
    return success('Successfully logout',null);
   }
}
