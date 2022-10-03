<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Otp;
use App\Models\User;
use App\Models\UserTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Microservices\Crm;

class AuthController extends Controller
{
    public function viewLogin()
    {
        return view('auth.login');
    }

    function loginCheck(Request $request)
    {
        $crm = new Crm();
        $contact = $crm->findContact($request->email_phone);
        $user = User::where('_id', $contact['contact_id'])->first();

        if (empty($contact)) {
            return back()->with('notification_error', 'Không tìm thấy tài khoản của bạn !!!');
        }

        if (empty($user)) {
            $user = User::create([
                '_id' => (int)$contact['contact_id'],
                'password' => '',
                'is_cache' => 0,
                'password_cache' => Hash::make(rand(100000, 900000))
            ]);
        }
        $arr = json_encode(array('_id' => $user['_id'], 'email' => $request->email_phone));
        $token = base64_encode($arr);
        return redirect()->route('login.password', ['token' => $token]);
    }

    public function viewPassword($token)
    {
        $param = base64_decode($token);
        $item = json_decode($param);
        if (empty($item->_id) || empty($item->email)) {
            return redirect()->route('login');
        }
        $user = User::where('_id', $item->_id)->first();
        if (empty($user)) return redirect()->route('login');
        $contact = (new Crm())->getContactDetail($user['_id']);
        $data = [
            '_id' => $user['_id'],
            'email_phone' => $item->email,
            'avatar' => $contact['avatar'],
            'fullname' => $contact['fullname']
        ];
        return view('auth.password', $data);
    }

    public function login(Request $request)
    {
        $request->validate([
            '_id' => 'required',
            'password' => 'required|min:6'
        ]);

        $user = User::where('_id', (int)$request->_id)->first();
        if (empty($user->is_cache)) {
            $check = Hash::check($request->password, $user['password_cache']);
//            $check = Auth::attempt(array('_id'=> $user['_id'] , 'password_cache' => $request->password));
        } else {
            $check = Auth::attempt(array('_id' => $user['_id'], 'password' => $request->password));
        }
        if ($check) {
            Auth::login($user);
            return redirect()->route('home');
        }
        return back()->with('notification_error', 'Mật khẩu không chính xác !!!, vui lòng nhập lại');
    }


    public function viewRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $userTempModel = new UserTemp();
        $crm = new Crm();
        $request->validate([
            'email' => [
                'required',
                function ($attribute, $value, $fail) use ($crm) {
                    $contact = $crm->findContact($value);
                    if (!empty($contact)) {
                        $fail('The ' . $attribute . ' exist in the system.');
                    }
                },
            ],
        ]);
        $mailFormat = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        $type = preg_match($mailFormat, $request->email) ? 'email' : 'phone';
        try {
            $user_temp_id = $userTempModel->create([
                "$type" => $request->email,
                'type' => $type,
                'fullname' => $request->fullname ?? ''
            ]);
            $data = ['user_id' => $user_temp_id, 'action_type' => 'register', 'type' => $type];
//            SendOtp::dispatch([
//                'data' => array('user_id' => $user->id, 'action_type' => 'register', 'type' => $type),
//                'action' => 'create'
//            ]);
            return redirect()->route('verify.otp', array_merge($data, ['callback' => route('verify.register', ['id' => $user_temp_id])]));
        } catch (\Exception $ex) {
            return back();
        }
    }

    public function verifyRegister($id, Request $request)
    {
        $otpModel = new Otp();
        $userTempModel = new UserTemp();
        $action_type = 'register';

//        $check_otp = $otpModel->validate_otp($id, $action_type, $request->code);

//        if (!$check_otp['status']) {
//            return back()->with('notification_error', $check_otp['message']);
//        }
        $userTemp = $userTempModel->detail($id);

        if(empty($userTempModel)){
            return back()->with('notification_error', 'Otp does not exists !!!');
        }
        $data = collect($userTemp)->only(['email', 'phone', 'fullname' , 'address', 'avatar']);

//        $contactModel = new Contact();
//        $contact = $contactModel->create($data);

        $password_randdom = substr(md5(time()), 0, 10);

        $user = User::firstOrCreate(
            ['_id' => 123456],
            [
                'password' => '',
                'is_cache' => 0,
                'password_cache' => Hash::make($password_randdom)
            ]
        );
        Auth::login($user);
        return redirect()->route('home')->with('temporary_password', $password_randdom);
    }


    public function forgotPassword()
    {

    }

    public function verifyForgotPassword()
    {

    }

    function logout(Request $request){
        Auth::logout();
        return redirect()->route('login');
    }
}
