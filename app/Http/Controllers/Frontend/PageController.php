<?php

namespace App\Http\Controllers\Frontend;

use App\User;
use App\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdatePassword;
use App\Http\Requests\TransferFormValidate;

class PageController extends Controller
{
   public function home(){
       $user = Auth::guard('web')->user();
    return view('frontend.home' , compact('user'));
   }
   public function profile(){
       $user = Auth::guard('web')->user();
    return view('frontend.profile' , compact('user'));
   }

   public function updatePassword(){
    return view('frontend.update_password');
   }

   public function updatePasswordStore(UpdatePassword $request){
    $old_password = $request->old_password;
    $new_password = $request->new_password;
    $user = Auth::guard('web')->user();

    if (Hash::check( $old_password, $user->password)) {
        $user->password = Hash::make($new_password);
        $user->update();

      return redirect()->route('profile')->with('update' , 'Successfully Updated.');
    }
    return back()->withErrors(['old_password' => 'The old password is not correct'])->withInput();
   }

   public function wallet(){
       $authUser = Auth()->guard('web')->user();

    return view('frontend.wallet', compact('authUser'));
   }

    public function transfer(){
        $authUser = Auth()->guard('web')->user();
        return view('frontend.transfer' , compact('authUser'));
    }

    public function transferConfirm(TransferFormValidate $request){

        $authUser = Auth()->guard('web')->user();
        $from_account=$authUser;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value=$request->hash_value;
        $str= $to_phone.$amount.$description;
        $hash_value2= hash_hmac('sha256', $str , 'walletpay!@#');

        if ($hash_value!==$hash_value2) {
            return back()->withErrors(['amount' => 'The given data is invalid.'])->withInput();
        }

        if($amount <1000){
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMK.'])->withInput();
        }

        $to_account = User::where('phone' , $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['to_phone' => '(To) Account is Invalid'])->withInput();
        }

        if($from_account->phone==$to_phone){
            return back()->withErrors(['to_phone' => '(To) Account is Invalid'])->withInput();
        }

        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['to_phone' => 'The given data is invalid'])->withInput();
        }

        if($from_account->wallet->amount < $amount){
            return back()->withErrors(['amount' => 'The amount is not enough'])->withInput();
        }

        return view('frontend.transfer_confirm' , compact('from_account' , 'to_account','amount','description','hash_value'));
    }

    public function transferComplete(TransferFormValidate $request){

        $authUser = Auth()->guard('web')->user();
        $from_account=$authUser;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value=$request->hash_value;
        $str= $to_phone.$amount.$description;
        $hash_value2= hash_hmac('sha256', $str , 'walletpay!@#');

        if ($hash_value!==$hash_value2) {
            return back()->withErrors(['amount' => 'The given data is invalid.'])->withInput();
        }

        if($amount <1000){
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMK.'])->withInput();
        }

        $to_account = User::where('phone' , $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['to_phone' => '(To) Account is Invalid'])->withInput();
        }

        if($from_account->phone==$to_phone){
            return back()->withErrors(['to_phone' => '(To) Account is Invalid'])->withInput();
        }

        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['to_phone' => 'The given data is invalid'])->withInput();
        }

        if($from_account->wallet->amount < $amount){
            return back()->withErrors(['amount' => 'The amount is not enough'])->withInput();
        }

        DB::beginTransaction();
        try {
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount', $amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();
            $from_account_transaction = new Transaction();
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();


            DB::commit();
            return redirect('/transaction/'.$from_account_transaction->trx_id)->with('transfer_success' , 'Successfully transfered.');
        } catch (\Exception $error) {
            DB::rollBack();
            return back()->withErrors(['fail' => 'something wrong' , $error->getMessage()])->withInput();
        }
    }

    public function transactionDetail($trx_id){
        $authUser = Auth()->guard('web')->user();
        $transaction = Transaction::with('user','source')->where('user_id',$authUser->id)->where('trx_id',$trx_id)->first();
        return view('frontend.transaction_detail' ,compact('transaction'));
    }

    public function transaction(Request $request){
        $authUser = Auth()->guard('web')->user();
        $transactions = Transaction::with('user','source')->orderBy('created_at' , 'DESC')->where('user_id' , $authUser->id);
        if($request->type){
            $transaction = $transactions->where('type', $request->type);
        }
        if($request->date){
            $transaction = $transactions->whereDate('created_at', $request->date);
        }
        $transactions = $transactions->paginate(5);
        return view('frontend.transaction' ,compact('transactions'));
    }

    public function toAccountVerify(Request $request){
        $authUser = Auth()->guard('web')->user();
        if($authUser->phone!=$request->phone){
            $user = User::where('phone' , $request->phone)->first();
            if($user){
                return response()->json([
                    'status' => 'success',
                    'message' => 'success',
                    'data' => $user
                ]);
            }
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'Invalid Data',
        ]);
    }

    public function passwordCheck(Request $request){
        if(!$request->password){
            return response()->json([
                'status' => 'fail',
                'message' => 'Please fill your password.',
            ]);
        }
        $authUser = Auth()->guard('web')->user();
        if(Hash::check($request->password , $authUser->password)){
            return response()->json([
                'status' => 'success',
                'message' => 'The password is correct',
            ]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'The password is incorrect',
        ]);
    }

    public function transferHash(Request $request){
       $str= $request->to_phone.$request->amount.$request->description;
       $hash_value= hash_hmac('sha256', $str , 'walletpay!@#');
       return response()->json([
        'status' => 'success',
        'data' => $hash_value,
         ]);
    }

    public function receiveQR(){
        $authUser= auth()->guard('web')->user();
        return view('frontend.receive_qr',compact('authUser'));
    }

    public function scanAndPay(){
        return view('frontend.scan_and_pay');
    }
}
