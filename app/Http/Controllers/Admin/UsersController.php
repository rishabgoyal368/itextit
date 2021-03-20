<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    protected $label;
    public function __construct()
    {
        // $this->middleware('auth:admin');
        $this->label = 'User';
    }
    public function index(Request $request)
    {
        $label = $this->label;
        $users = User::get();
        return view('Admin.Users.list', compact('label', 'users'));
    }

    public function add(Request $request, $id = null)
    {
        try {
            if ($request->isMethod('GET')) {
                if ($id) {
                    $formLabel = 'Edit';
                    $user = User::findorFail($id);
                } else {
                    $user = [];
                    $formLabel = 'Add';
                }
                $label = $this->label;
                return view('Admin.Users.add', compact('label', 'formLabel', 'user'));
            } else if ($request->isMethod('POST')) {
                $data = $request->all();
                $validator =  Validator::make($data, [
                    'full_name' =>  'required',
                    'email' => 'required|email|unique:users,email,' . @$data['id'] . ',id,deleted_at,NULL',
                    'mobile_number' => 'nullable|numeric|unique:users,mobile_number',
                    'status' => 'required',
                    'password' => @$data['id'] ? 'nullable' : 'required|confirmed',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                }
                $user = User::where('id', $request['id'])->first();
                if ($request['id']) {
                    $data['password'] = $user['password'];
                    $msz =  'Updated';
                } else {
                    $email = $request['email'];
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        Mail::send('emails.user_register_success', ['name' => ucfirst($data['full_name']), 'email' => $email, 'password' => $data['password']], function ($message) use ($email) {
                            $message->to($email, env('APP_NAME'))->subject('User registered successfully');
                        });
                    }
                    $data['password'] = Hash::make($request['password']);
                    $msz =  'Added';
                }
                $users =  User::addEdit($data);
                $msz = $request['id'] ? 'Updated' : 'Added';
                return redirect('admin/manage-users')->with(['success', 'User ' . $msz . ' Successfully']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $delete = User::where('id', $id)->delete();
        if ($delete) {
            return redirect()->back()->with('success', 'User deleted successfully');
        } else {
            return redirect('admin/category')->with('error', 'Something went wrong, Please try again later.');
        }
    }
}
