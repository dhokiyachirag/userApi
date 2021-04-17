<?php
namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function edit_user(Request $request)
    {
        $data =$request->all();// $request->only('id','name', 'email', 'password','contact_number','password_confirmation');
        
        $validator = Validator::make($data, [
            'name' => 'string',
            'email' => 'email|unique:users',
            'password' => 'string|min:6|max:50',
            'password_confirmation'=>'same:password',
            'contact_number'=> 'numeric',
            'id'=> 'numeric'
        ]);

        if ($validator->fails()) { 
   
            return response()->json(['status' => true,'message'=>$validator->errors()->first()]);             
        }

        $user = JWTAuth::authenticate($request->token);
        
        //if admin editing other user record
        if($user->is_admin == 1){
            $user=User::find($request->id);
            if(!$user)
                return response()->json(['status' => true,'message'=>'Invalid User id']);
        }
        
        $user->name =  isset($request->name) ? $request->name : $user->name;
        $user->email =  isset($request->email) ? $request->email : $user->email;
        $user->password =  isset($request->password) ? bcrypt($request->password) : $user->password;
        $user->contact_number = isset($request->contact_number) ? $request->contact_number : $user->contact_number;
        $user->save();
        
        return response()->json(['status' => true,'message'=>'User details updated sucessusfully']);
        
    }
    public function get_user(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        if($user->is_admin == 1){
            $data = User::all();
        }else{
            $data = $user;
        }
       
        return response()->json(['status' => true,'message'=>$data]);
    }
    public function approve_user(Request $request)
    {
        $data = $request->only('id');
        $validator = Validator::make($data, [
            'id'=> 'required|numeric'
        ]);

        $user = JWTAuth::authenticate($request->token);

        if($user->is_admin == 1){
            $user = User::find($request->id);
            if($user){
                $user->is_approve = 1;
                $user->save();
                $data = "User with id: ".$request->id." approve suceessfully";
            }else{
                $data = "User with id: ".$request->id." not found"; 
            }
            
        }else{
            $data = "Please login with admin credentials to approve user";
        }
        return response()->json(['status' => true,'message'=>$data]);
    }
    public function delete_user(Request $request){

        $data = $request->only('id');
        $validator = Validator::make($data, [
            'id'=> 'required|numeric'
        ]);

        $user = JWTAuth::authenticate($request->token);

        if($user->is_admin == 1){
            $user = User::find($request->id);
            if($user && $user->delete()){
                $data = "User with id: ".$request->id." deleted successfully";
            }else{
                $data = "User with id: ".$request->id." not found"; 
            }
            
        }else{
            $data = "Please login with admin credentials to delete user";
        }
        return response()->json(['status' => true,'message'=>$data]);
    }
}