<?php

namespace App\Services\Api;

use App\Fields\Store\TextField;
use App\Http\Resources\UserResource;
use App\Models\User;

class AService extends AbstractService
{
    protected $model = User::class;
    protected $modelName = 'User';
    protected $resource = UserResource::class;
    protected $columns = ['name'];
    protected $menu = 'User';


    public function storeFields()
    {
        return [
            TextField::make('clinic_id')->setRules('nullable'),
        ];
    }

    public function updateFields()
    {
        return [
            TextField::make('clinic_id')->setRules('nullable'),
        ];
    }

}
