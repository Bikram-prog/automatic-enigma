<?php

namespace App\Http\Controllers;
use App\BuyerUser;
use App\BuyerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class BuyerLoginController extends Controller
{
    public function viewSignup(){
        return view('buyersignup');
    }
    public function buyerSignup(Request $request){
      $register_info = new BuyerUser(); 
        $register_info->buyer_f_name = request('name');
        $register_info->buyer_email  = request('email');
        $register_info->buyer_password = request('password');
        $register_info->buyer_sex = request('gender');

        if(request('password') == request('repassword')){
            $register_info->save();

         return redirect('/buyerlogin')->with(['regsucmsg' => 'You have successfully registered.']);
      }else{
    	 return redirect('/buyersignup')->with('errrmessage','Please correct your information');
      } 	
    }

    public function viewLogin(){
        return view('buyerlogin');
    }

    public function buyerLogin(){
        
        $login_info = new BuyerUser();
        $login_info->buyer_email = request('email');
        $login_info->buyer_password = request('psw');
        $data = DB::select('select * from buyer_users where buyer_email  =? and buyer_password =?',[request('email'),request('psw')]);
        if(count($data) == '1'){
	         Session::put('BuyerEmail', $data[0]->buyer_email);
	         Session::put('BuyerFullName', $data[0]->buyer_f_name);
             Session::put('BuyerIds', $data[0]->buyer_id );
        	return redirect('/');
        }
}

    

    public function logoutUser() {
        Session::flush();
        \Cart::clear();
        return redirect('/');
    }

}
