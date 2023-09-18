<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Responses\ResponseAuth;
use App\Mail\SendCodeResetPassword;
use App\Mail\SendEmailConfirmation;
use App\Mail\SendEmailVerify;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Services\AuthServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    public function register(StoreRegisterRequest $request)
    {

        $validatedData = $request->all();
        $validatedData['password'] = bcrypt($request->password);


        $user = User::create($validatedData);

        try {
            // Delete all old code that user send before.
            ResetCodePassword::where('email', $request->email)->delete();

            // Generate random code
            $code = mt_rand(100000, 999999);
            $data['code'] = sha1($code);
            $data['updated_at'] = new Carbon();
            $data['email'] = $request->email;

            // Create a new code
            $codeData = ResetCodePassword::create($data);

            if ($codeData) {
                // Send email to user
                $codeSended = $this->sendEmailConfirmation($data, $data['code']);
                if ($codeSended['success']) {
                    return ResponseAuth::ok([
                        "user" => $user,
                        "message" => $codeSended['message'],
                        "tirar isso depois" => $codeSended['code']
                    ]);
                }
                return ResponseAuth::error();
            }
            return ResponseAuth::error();
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 422);
        }
    }

    public function sendEmailConfirmation($data, $code)
    {
        $codeData = ResetCodePassword::create($data);

        if ($codeData) {
            // Send email to user
            Mail::to($data['email'])->send(new SendEmailVerify($data['code']));



            return [
                "success" => true,
                "code" => $code,
                "message" => trans('passwords.sent_confirmation')
            ];
        }
        return [
            "success" => false
        ];
    }
    public function login(Request $request)
    {
        $email = $request['email'];
        $password = $request['password'];

        $value = [];
        if(!$email || $email == ""){
            $value["email"] = "email";
        }
        if(!$password || $password == ""){
            $value["password"] = "senha";
        }
        if((!$email || $email == "") || (!$password || $password == "")){
            return response(["success" => false, "message" => "Por Favor preencha esses dados", "values" => $value]);
        }
        
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

     

        if (!auth()->attempt($loginData)) {
            return response(['success' => false, 'message' => 'Email e/ou senha inválidos']);
        }

        if (auth()->user()->email_verified_at === null) {
            $code = mt_rand(100000, 999999);
            $data['code'] = sha1($code);
            $data['updated_at'] = new Carbon();
            $data['email'] = $request->email;
            $codeSended = $this->sendEmailConfirmation($data, $data['code']);
            if ($codeSended['success']) {
                return ResponseAuth::ok([
                    "message" => $codeSended['message'],
                    "emailVerified" => false
                ]);
            }
            return ResponseAuth::error();
        }
        $accessToken = auth()->user()->createToken('authToken');

        return response(['success' => true, 'user' => auth()->user(), 'access_token' => $accessToken->plainTextToken, "emailVerified" => true]);
    }

    public function resetPass(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        try {
            // Delete all old code that user send before.
            ResetCodePassword::where('email', $request->email)->delete();

            // Generate random code
            $code = mt_rand(100000, 999999);
            $data['code'] = sha1($code);
            $data['updated_at'] = new Carbon();

            // Create a new code
            $codeData = ResetCodePassword::create($data);

            if ($codeData) {
                // Send email to user
                Mail::to($request->email)->send(new SendCodeResetPassword(["code" => $code, "type" => 'reset']));

                return ResponseAuth::ok(["code-teste" => $code], trans('passwords.sent'));
            }
            return ResponseAuth::error();
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 422);
        }
    }


    public function checkCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|email:reset_code_passwords',
            'code' => 'required'
        ]);


        $codeChecked = AuthServices::checkCode($request->code, $request->email);
        return response()->json($codeChecked);
    }

    public function emailVerify(Request $request)
    {

        $code = $request->code;
        $codeChecked = AuthServices::checkCodeByCode($code);
        if ($codeChecked['success']) {
            $user = User::where('email', $codeChecked['email'])->get()->first();
            if ($user) {
                $user->email_verified_at = new Carbon();
                $user->save();

                return ResponseAuth::ok(["email" => $user->email], "Email verificado com sucesso");
            }
            return ResponseAuth::error(["user" => $user], "Este usuário não existe");
        }
        return ResponseAuth::error([], $codeChecked['message']);
    }
    public function updatePass(Request $request)
    {
        $request->validate([
            'email' => 'required|email|email:users',
            'code' => 'required',
            'password' => 'required|confirmed'
        ]);

        $codeChecked = AuthServices::checkCode($request->code, $request->email);

        try {
            if (!$codeChecked['success']) {
                return response()->json($codeChecked);
            }

            $user = User::where('email', $request->email)->get()->first();
            if ($user) {
                $user->password = bcrypt($request->password);
                $user->save();
                return ResponseAuth::create([], "Senha alterada, volte e faça login!");
            }
            return ResponseAuth::error([], "Usuário não encontrado");
        } catch (\Exception $e) {
            return ResponseAuth::error([], $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return ResponseAuth::ok();
        } catch (\Exception $e) {
            return ResponseAuth::error([], $e->getMessage());
        }
    }
    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        $url =  Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return ResponseAuth::ok(["url" => $url]);
    }
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'github', 'google'])) {
            return response()->json(["error" => "Esse provedor não está disponível"], 422);
        }
    }
    public function handleProviderCallback(Request $request)
    {
        $validator = Validator::make($request->only('provider', 'access_provider_token'), [
            'provider' => ['required', 'string'],
            'access_provider_token' => ['required', 'string']
        ]);
        if ($validator->fails())
            return ResponseAuth::error($validator->errors());
        $provider = $request->provider;
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
    
    
    
        try {
            $user = Socialite::driver($provider)->userFromToken($request->access_provider_token);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return ResponseAuth::error([], "Credenciais inválidas");
        }

        $userCreated = User::where('email', $user->getEmail())->get()->first();
        if($userCreated) {
            if($userCreated->email_verified_at == null){
                $userCreated->email_verified_at = new Carbon();
            }
            $userCreated->save();
        }else{
            $dataToCreateUser = [
                "email" => $user->getEmail(),
                "email_verified_at" => new Carbon(),
                "name" => $user->getName(),

            ];

            $userCreated = User::create($dataToCreateUser);
        }


        $providerUser = $userCreated->providers()->where('provider_id', $user->getId())->get()->first();
        if($providerUser){
            $providerUser->avatar = $user->getAvatar();
            $providerUser->save();
        }else {
            $userCreated->providers()->create([
                "provider" =>$provider,
                "provider_id" => $user->getId(),
                "avatar" => $user->getAvatar()
            ]);
        }
       
        $token = $userCreated->createToken('authToken')->plainTextToken;
        return ResponseAuth::ok([
            "user" => $userCreated,
            "token" => $token
        ]);
    }
}
