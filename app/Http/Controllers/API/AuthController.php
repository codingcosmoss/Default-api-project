<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthUserResource;
use App\Http\Resources\OneUserResource;
use App\Http\Resources\UserResources;
use App\Models\BoxSize;
use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\Currency;
use App\Models\DrugCompany;
use App\Models\MedicineCategory;
use App\Models\Menu;
use App\Models\PaymentType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Setting;
use App\Models\Settings\Configuration;
use App\Models\SizeType;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Api\AuthService;
use App\Traits\Status;
use Database\Seeders\DefaultBaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;


/**

     * Author: Muhammadali

     * Auth Controller performs the function of user login and registration

 **/

class AuthController extends AbstractController
{
    public function __construct(AuthService $service){
        $this->service = $service;
    }
   
    public function login()
    {
        $data = $this->service->login(request()->all());
        return $this->sendResponse($data);
    }
   
    public function logout()
    {
        $data = $this->service->logout(request()->all());
        return $this->sendResponse($data);
    }
   
   
    public function authUser()
    {
        $data = $this->service->authUser(request()->all());
        return $this->sendResponse($data);
    }

}

