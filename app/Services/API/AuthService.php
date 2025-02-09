<?php

namespace App\Services\Api;

use App\Fields\Store\TextField;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService extends AbstractService
{

    public function loginFields()
    {
        return [
            TextField::make('phone')->setRules('required|regex:/^([0-9\s\-\+\(\)]*)$/|min:12'),
            TextField::make('password')->setRules('required|min:5'),
        ];
    }

    public function login($data){
        try {

            $validator = $this->dataValidator($data, $this->loginFields());
            if ($validator['status']) {
                return [
                    'status' => false,
                    'code' => 422,
                    'message' => 'Validator error',
                    'errors' => $validator['validator']
                ];
            }

            $user = User::where('phone', $data['phone'])->first();

            if (!$user){
                return [
                    'status' => false,
                    'code' => 403,
                    'message' => 'No such user exists',
                ];
            }

            if (empty($user) || !Hash::check($data['password'], $user->password)) {
                return [
                    'status' => false,
                    'code' => 403,
                    'message' => 'Incorrect login or password',
                ];
            }else{
                $user->token = $user->createToken('laravel-vue-admin')->plainTextToken;
            }

            return [
                'status' => true,
                'code' => 200,
                'message' => 'User login successful',
                'data' => $user
            ];


        } catch (Exception $e) {

            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }

    }

    public function logout(){
        try {

            $user = User::find(auth()->user()->id);

            if (!$user){
                return [
                    'status' => false,
                    'code' => 403,
                    'message' => 'No such user exists',
                ];
            }

            auth()->user()->currentAccessToken()->delete();
            
            return [
                'status' => true,
                'code' => 200,
                'message' => 'Logout Successful',
            ];
           

        } catch (Exception $e) {

            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
      

    }

    public function authUser(){
        try {

            return [
                'status' => true,
                'code' => 200,
                'message' => 'The user information that was sent from the authorization was received.',
                'data' => auth()->user()
            ];
           

        } catch (Exception $e) {

            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
      

    }

}
