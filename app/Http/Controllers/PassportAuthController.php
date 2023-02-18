<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\MasterTrait;

class PassportAuthController extends Controller
{
    use MasterTrait;
    /**
     * Registration
     */
    public function register(Request $request)
    {
        /* $imageName=null;
        if ($request->hasFile('image')) {
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(asset('storage/uploads/users'), $imageName);
        } */
         $validator = Validator::make( $request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|confirmed|min:6',            
			//'birth_date'=>'required',
            'gender'=>'required',
            'password_confirmation' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        }
        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            // 'birth_date' => $request->birth_date,
            'gender' => $request->gender,
           // 'image'=>'uploads/users/'.$imageName,
            //'birth_date' => Carbon::parse($request->birthday)->format('Y-m-d'),
        ]);
        //dd($users);
        $user = Auth::user();
        $token = $users->createToken('APIToken')->accessToken;
        return response()->json(['status'=>true,'message'=>trans('register successfully '),'data'=>$users , 'token' => $token], 200);
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,
           
        ];
        
        if (auth()->attempt(['email'=>$data['email'],'password'=>$data['password']])) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;

            return response()->json(['status'=>true,'token' => $token,'token_type'=>'Bearer','data' => auth()->user()], 200);
        } else {
            return response()->json(['status'=>false, "message" => trans( 'These credentials do not match our records')], 203);
        }
    }   
    public function getUserProfile(Request $request)
    {
        $user = Auth::User();
        $data = User::with('address')->where('id', $user->id)->get();
        if ($user->id)
        {
           /* $address = Address::where('user_id', $user->id)->first();

	        $user->address = ($address['address']) ? $address['address'] : "";
            $user->landmark = ($address['landmark']) ? $address['landmark'] : "";
            $user->city = ($address['city']) ? $address['city'] : "";
            $user->state = ($address['state']) ? $address['state'] : "";
            */
            return response()->json(['status'=>true, 'data' => $data], 200);
        }
         
        else
        {
            return response()->json(['status'=>false,"message" => trans('profile not found')], 203);
        }
     
    }
    public function profileUpdate(Request $request)
    {
        $user = Auth::User();
        
                /* $imageName=null;
                if ($request->hasFile('image')) {
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                } */
        
        $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:users,email,'.$user->id,
                'gender' => 'required',
                'address' => 'required',
                'landmark' => 'required',
                'city' => 'required',
                'state' => 'required'
            ]);

            if ($validator->fails()) {
                return response(['status'=>false, 'message' => $validator->errors()->first()], 203);
            }  
        $users = User::find($user->id);
        if($users)
        {
            $users->name = $request->get('name');
            $users->email = $request->get('email');
            $users->gender = $request->get('gender');
            //$users->image = $imageName;
            $users->save();
            
            $address = [
                'address' => $request->get('address'),
                'landmark' => $request->get('landmark'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
            ];
            $ifExistAddress = Address::where('user_id', $user->id)->first();
            if(!empty($ifExistAddress)){
                Address::where('id', $ifExistAddress['id'])
                    ->update($address);
            }else{
                $address['user_id'] = Auth::user()->id;
                $address['address'] = $request->address ? $request->address : null;
                $address['landmark'] = $request->landmark ? $request->landmark : null;;
                $address['city'] = $request->city ? $request->city : null;;
                $address['state'] = $request->state ? $request->state : null;;
                
                $this->lastInsertedData("App\Models\Address", $address);
            }
            $data = User::with('address')->where('id', $user->id)->get();
            //return response()->json(['status'=>true, 'data' => $data], 200);
            return response()->json(['status'=>true,'message'=>trans('Profile updated successfully'), 'data' => $data], 200);
        } 
        else
        {
            return response(['status'=>false, 'message'=>'User not found '],203);
        }
    }
 
     public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response([ 'status'=>false,'message' => $validator->errors()->first()], 203);
        }
        #Match The Old Password
        $data = $request->all();
        if(!\Hash::check($data['old_password'], auth()->user()->password)){

            return response(['status'=>false, 'message'=>'The current password is incorrect '],203);

        }
        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return response()->json(['status'=>true,'message'=>trans('Password changed successfully')], 200);
    }

    public function logout(Request $request)
    { 
        $accessToken = Auth::user()->token();
        $token= $request->user()->tokens->find($accessToken);
        $token->revoke();
        return response()->json([
            'status'=>true,
            'message' => 'Successfully logged out'
        ]);
    }



}
