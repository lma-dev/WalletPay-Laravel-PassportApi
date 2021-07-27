<?php

namespace App\Http\Controllers\Backend;

use App\User;
use App\Wallet;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use App\Http\Requests\StoreUser;
use Yajra\Datatables\Datatables;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return view('backend.user.index');
    }

    public function ssd()
    {

            $data = User::query();
            return DataTables::of($data)

            ->editColumn('user_agent' , function($each){

            if($each->user_agent){
                $agent = new Agent();
                $agent->setUserAgent($each->user_agent);
                $device = $agent->device();
                $platform = $agent->platform();
                $browser = $agent->browser();

                return '<table class="table table-bordered">
                            <tbody>
                                <tr><td>Device</td><td>'. $device .'</td></tr>
                                <tr><td>PlatForm</td><td>'. $platform .'</td></tr>
                                <tr><td>Browser</td><td>'. $browser .'</td></tr>
                            </tbody>
                        </table>';
                        }
                return '-';

            })

            ->editColumn('created_at' , function($each){
                return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at' , function($each){
                return Carbon::parse($each->update_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('action',function($each){
                $edit_icon ='<a href="'.route('admin.user.edit' , $each->id).'" class="text-warning"><i class="fas fa-edit"></i></a>';
                $delete_icon = '<a href="#" class="text-danger delete" data-id="'.$each->id.' "><i class="fas fa-trash-alt"></i></a>';

                return '<div class="action-icon">' . $edit_icon . $delete_icon . '</div>';
            })
            ->rawColumns(['user_agent' , 'action'])
            ->make(true);
            }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('backend.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->save();

             Wallet::firstOrCreate(
                [
                    'user_id' =>  $user->id
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount'    => 0 ,
                ]
            );
            DB::commit();
            return redirect()->route('admin.user.index')->with('create','Successfully Created');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withError(['fail' => 'Something wrong.' .$e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $user = User::findorFail($id);
        return view('backend.user.edit' , compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, $id)
    {
        DB::beginTransaction();
        try {
        $user = User:: findorfail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->phone = $request->phone;
        $user->update();

        Wallet::firstOrCreate(
            [
                'user_id' =>  $user->id
            ],
            [
                'account_number' => UUIDGenerate::accountNumber(),
                'amount'    => 0 ,
            ]
        );
            DB::commit();
            return redirect()->route('admin.user.index')->with('update','Successfully Updated');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withError(['fail' => 'Something wrong.' .$e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = User::findorFail($id);
      $user->delete();

      return 'success';
    }
}
