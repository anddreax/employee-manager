<?php

namespace App\Http\Controllers;

use App\Mail\MailRecovery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class usersController extends Controller
{
    public function new(Request $request){
        //Array Asociativo que genera la respuesta
        $response = ['status'=>1, 'msg'=>''];
        $data = $request->getContent(); //recogemos datos
        $data = json_decode($data); //descodificamos los datos
        $user = new User();
        $user->name  = $data->name;
        $user->email  = $data->email;
        $user->password  = Hash::make($data->password);
        $user->salary  = $data->salary;
        $user->description  = $data->description;

        try{
            if($data->role == 'executive' || $data->role == 'human-resources' || $data->role == 'employee'){
                $user->role  = $data->role;
                $user->save();
                $response['msg'] = "User Save";
            }else{
                $response['msg'] = 'Role not found';
            }
        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }

        return response()->json($response);
    }

    public function login(Request $request){
        $response = ['status'=>1, 'msg'=>''];
        $data = $request->getContent();
        $data = json_decode($data);


        try{
            $user = User::where('email', $data->email)->first();

            if(isset($data->email) && isset($data->password)){
                if($user){
                    if(Hash::check($data->password, $user->password)){
                        $apitoken =  Hash::make(now().$user->id);
                        $user->api_token = $apitoken;
                        $user->save();
                        $response['msg'] = "User Save, your token is: " . $apitoken;

                    }else{
                        $response['msg']='Password is not correct';
                    }
                }else{
                    $response['msg']='User is not correct';
                }
            }else{
                $response['msg']='Data missing';
            }

        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }
        return response()->json($response);
    }

    public function recoverypass(Request $request){
        $response = ['status'=>1, 'msg'=>''];
        $data = $request->getContent();
        $data = json_decode($data);
        try{
            if($data->email){
                $email = $data->email;
                $user = User::where('email', $email)->first();
                if($user){
                    $password = Str::random(8);
                    Mail::to($user)->send(new MailRecovery($password));
                    $user->password = Hash::make($password);
                    $user->save();
                    $response ['msg'] = 'Email sent';
                }else{
                    $response ['msg'] = 'User not found';
                }
            }else{
                $response ['msg'] = 'Enter an email';
            }
        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }
        return response()->json($response);
    }

    public function list(Request $req){
        $response = ['status'=>1, 'msg'=>''];
        try{
            $user = $req->user;
            if($user->role == 'executive'){
                $users = User::where('role', '<>', 'executive')->get();
               $response['msg'] = $users;
            }else if($user->role == 'human-resources'){
                $users = User::where('role', 'employee');
                $response['msg'] = $users;
            }

        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }
        return response()->json($response);
    }

    public function listbyID(Request $request, $id){
        $response = ['status'=>1, 'msg'=>''];
        try{
            $user = User::find($id);
            $logedUser = $request->user;
            if($user){
                if($logedUser->id == $id){
                    $response['msg'] = $user;
                }else if($logedUser->role == $user->role){
                    $response['msg'] = "Not permissions";
                }else if($logedUser->role != $user->role && $user->role == 'executive'){
                    $response['msg'] = "Not permissions";
                }else{
                    $response['msg'] = $user;
                }

            }else{
                $response['msg'] = 'User not found';
            }
        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }
        return response()->json($response);
    }

    public function profile(Request $request){
        $response = ['status'=>1, 'msg'=>''];
        try{
            $response['msg'] = $request->user;
        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }
        return response()->json($response);
    }

    public function changeuser(Request $request, $id){
        $response = ['status'=>1, 'msg'=>''];
        $data = $request->getContent();
        $data = json_decode($data);
        try{
            $user = User::find($id);
            $logedUser = $request->user;
            if($user){
                if(isset($data->name)){
                    $user->name  = $data->name;
                }if(isset($data->email)){
                    $user->email  = $data->email;
                }if(isset($data->password)){
                    $user->password  = $data->password;
                }if(isset($data->salary)){
                    $user->salary  = $data->salary;
                }if(isset($data->description)){
                    $user->description  = $data->description;
                }
            }

            if($logedUser->id == $id){
                $user->save();
                $response['msg'] = "User Save";
            }else if($logedUser->role == $user->role){
                $response['msg'] = "Not permissions";
            }else if($logedUser->role != $user->role && $user->role == 'executive'){
                $response['msg'] = "Not permissions";
            }else{
                $user->save();
                $response['msg'] = "User Save";
            }

        }catch(\Exception $e){
            $response['msg'] = $e->getMessage();
            $response['satus'] = 0;
        }
        return response()->json($response);
    }
}
