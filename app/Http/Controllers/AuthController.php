<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Admin;
use Hash, Session, Mail;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request){
		// Admin::create([
		// 	'user_name' => 'Rishab',
		// 	'email' => 'rishabtest01@yopmail.com',
		// 	'password' => Hash::make('123'),
		// ]);
    	if(Auth::guard('admin')->check()) {
            return redirect('admin/home');
        }
        if($request->isMethod('post')){
    		$data = $request->all();
    		// echo '<pre>'; print_r($data); die;

    		$credentials = array(
    				'email'=>$data['email'],
    				'password'=>$data['password']
    			);

    		if(Auth::guard('admin')->attempt($credentials)){
    			return redirect('admin/home')->with('success','Admin login successfully');
    		} else{
    			return redirect('/admin/login')->with('error','Invalid email and password combination');
    		}
    	}
    	return view('login');
    }


    public function dashboard(){
    	return view('dashboard');
    }


    public function logout(){
		Auth::guard('admin')->logout();
    	Session::flush();
    	return redirect('/')->with('success','You logged out successfully');
    }

    public function forgot_password(Request $request){
		if($request->isMethod('post')){
			$data = $request->all();

			$email = $data['email'];
			$user = Admin::where('email',$email)->first();
			$project_name = 'New Project';
			if(empty($user)){
				return redirect()->back()->with('error','Invalid email-id');
			} else{
				$user_id 	= base64_encode($user->id);
				$user_name 	= ucfirst($user->first_name);
				$random_no 	= rand(111111, 999999);
	            $code 		= $random_no.time();
	            $security_code = base64_encode(convert_uuencode($code));

	            $user->security_code = $security_code;
	            $user->save();

				$set_password_url = url('set-password/'.$security_code.'/'.$user_id);
				// dd($set_password_url);
				try{
					// dd($email);
                    if(!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {   
                        Mail::send('emails.user_forgot_password',['name'=>$user_name,'email'=>$email,'set_password_url'=>$set_password_url],function($message) use($email,$project_name){
                            $message->to($email,$project_name)->subject('Forgot password');
                        });
                    }
                }catch(Exception $e){
                }
                return redirect('/')->with('success','Email sent successfully,on registered email.');
				
			}
		}
		return view('forgotPassword');
	}

	public function set_password(Request $request, $security_code, $user_id){

		if(!Auth::guard('admin')->check()){
			$user_id = base64_decode($user_id);
			$user = Admin::where(['id'=>$user_id,'security_code'=>$security_code])->first();
			
			if(!empty($user->security_code)){
				$email = $user->email;
				if($request->isMethod('post')){
					$data = $request->all();
					// dd($data);
					if(!empty($data['new_pw']) && !empty($data['confirm_pw'])){
						if($data['new_pw'] == $data['confirm_pw']){
							$hash_password = Hash::make($data['new_pw']);
							$password      = str_replace("$2y$","$2a$",$hash_password);
							$user->security_code = null;
							$user->password = $password;
							if($user->save()){
								return redirect('/admin/login')->with('success','Password changed successfully');	
							} else{
								return redirect()->back()->with('error','Something went wrong,Please try again later.');	
							}

						} else{
							return redirect()->back()->with('error',"Password and confirm password doesn't matched");	
						}
					} else{
						return redirect()->back()->with('error','Please enter password to change');
					}
				}
				return view('changePassword', compact('email'));
			} else{
				return redirect('/admin/login')->with('error','Link expired');
			}
		} else{
			return redirect('/')->with('error','Please logout your profile');
		}
	}

	public function reset_password(Request $request){
		
		$admin_id = Auth::guard('admin')->user()->id;
        $admins    = Admin::where('id',$admin_id)->first();

    	if($request->isMethod('post')){
    		$data = $request->all();
    		// echo "<pre>"; print_r($data); die;

			if($data['new_password'] != $data['confirm_password']){
				return redirect()->back()->with('error',"Password and confirm password doesn't matched");
			}

			$credentials = array(
						'email'=>$admins->email,
						'password'=>$data['old_password']
					);
			if(Auth::guard('admin')->attempt($credentials)){

				$admins->password = Hash::make($data['new_password']);
				if($admins->save()){
					return redirect()->back()->with('success',"Password changed successfully");		
				} else{
					return redirect()->back()->with('error',COMMON_ERROR);		
				}

			} else{
				return redirect()->back()->with('error',"Incorrect current password");	
			}
    	}
		$label = 'Reset Password'; 
		return view('Admin.Profile.resetPassword', compact('label'));
	}

	public function my_profile(Request $request){
		
		$admin_id = Auth::guard('admin')->user()->id;
        $profile    = Admin::where('id',$admin_id)->first();

		if($request->isMethod('post')){
			$data = $request->all();
			$profile->user_name 	= $data['user_name']; 
			$profile->email 		= $data['email']; 
			if(!empty($data['mobile_number'])){
				$profile->mobile_number = $data['mobile_number']; 
			}
			if(!empty($data['profile_image'])){
				if(!empty($_FILES['profile_image']['name'])){
	    			$info = pathinfo($_FILES['profile_image']['name']);
	    			$extension = $info['extension'];
	    			$random = rand(0000000,9999999);
	    			$new_name = $random.'.'.$extension;

	    			if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
	    				$file_path = base_path().'/'.AdminProfileBasePath;
	    				move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path.'/'.$new_name);

	    				$profile->profile_image = $new_name;
	    			}
	    		}
			}
    		if($profile->save()){
					return redirect()->back()->with('success',"Profile Updated successfully");		
				} else{
					return redirect()->back()->with('error','Something went wrong, Please try again later.');		
				}
		}
		
		$label = 'My Porfile'; 
		return view('Admin.Profile.profile', compact('label','profile'));	
	}
}
