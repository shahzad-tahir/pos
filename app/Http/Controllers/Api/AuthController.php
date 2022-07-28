<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ResponseController
{
    use Common;

    //create user
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:contacts',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'mobile' => 'required|max:20'
        ]);

        if($validator->fails()){
            return $this->sendError(0,"Something went wrong! Please try again", $validator->errors()->all());
        }

        $request['password'] = Hash::make($request['password']);
        $user = Contact::create([
            'business_id' => 1,
            'type' => 'customer',
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password
        ]);

        if($user){
            $contact = Contact::find($user->id);
            $contact['token'] =  $user->createToken('token')->accessToken;
            $result['user'] = $contact;
            $result['categories'] = $this->getCategories();
            $result['products'] = $this->getTrendingProducts();
            $message = "Registration successful";
            return $this->sendResponse(1,$message,$result);
        }

        $error = "Something went wrong! Please try again";
        return $this->sendError(0,$error,null ,401);

    }

    //login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $result = [];

        $credentials = request(['email', 'password']);
        if (!Auth::guard('contact')->attempt($credentials)) {
            $error = "Invalid credentials! Please try again";
            return $this->sendError(0, $error, null, 401);
        }
        $user = Auth::guard('contact')->user();
        $user['token'] = $user->createToken('token')->accessToken;
        $result['user'] = $user;
        $result['categories'] = $this->getCategories();
        $result['products'] = $this->getTrendingProducts(1, ['limit' => 10]);
        $message = "Login successful";
        return $this->sendResponse(1, $message, $result);
    }

    public function updateProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'mobile' => 'required',
            'user_id' => 'required|exists:contacts,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $user = Contact::find($request->user_id);
        $request['custom_field3'] = $request->latitude;
        $request['custom_field4'] = $request->longitude;
        $user->update($request->except(['profile_pic','password','user_id','latitude', 'longitude']));

        if($request->has('profile_pic')){
            $format = '.png';
            $entityBody = $request->file('profile_pic');// file_get_contents('php://input');

            $imageName = 'contact_'. time() . $format;
            $directory = "/uploads/img/";
            $path = base_path() . "/public" . $directory;

            $entityBody->move($path, $imageName);

            $response = $imageName;

            $user->profile_pic = $response;
            $user->save();
        }

        $message = 'Profile updated successfully';
        return $this->sendResponse(1,$message,$user);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $message = 'Successfully logged out.';
        return $this->sendResponse(1,$message,null);
    }

    //getuser
    public function getUser(Request $request)
    {
        $user = $request->user_id;
        if($user){
            $user = Contact::find($user);
            return $this->sendResponse(1,'Showing User Profile',$user);
        }
        $error = 'user not found';
        return $this->sendResponse(0,$error,null);
    }
}
