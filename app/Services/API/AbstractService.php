<?php

namespace App\Services\Api;

use App\Fields\Store\TextField;
use App\Models\Image;
use App\Models\RolePermission;
use App\Traits\Status;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class AbstractService
{
    protected $model;
    protected $menu;
    protected $modelName;
    protected $resource;
    protected $columns = [];

    public function index()
    {
        try {
            
            if (!$this->hasPermission('index')) return $this->noAllowed();

            $data = $this->resource::collection($this->model::all());

            return [
                'status' => true,
                'code' => 200,
                'message' => 'All '.$this->modelName.' information was successfully retrieved.',
                'data' => $data
            ];

        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }

    }

    public function getPaginate($count = 10)
    {

        try {

            if (!$this->hasPermission('index')) return $this->noAllowed();

            $models = $this->model::orderBy('id', 'asc')
                ->paginate($count);


            $data = [
                'items' => $this->resource::collection($models),
                'pagination' => [
                    'total' => $models->total(),
                    'per_page' => $models->perPage(),
                    'current_page' => $models->currentPage(),
                    'last_page' => $models->lastPage(),
                    'from' => $models->firstItem(),
                    'to' => $models->lastItem(),
                ],
            ];

            return [
                'status' => true,
                'code' => 200,
                'message' => $this->modelName.' information was successfully retrieved with pagination !',
                'data' => $data
            ];

        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    public function orderBy($column = 'id', $type = 'desc')
    {
        try {

            if (!$this->hasPermission('index')) return $this->noAllowed();

            $data = $this->model::orderBy($column, $type)->get();

            return [
                'status' => true,
                'code' => 200,
                'message' => $this->modelName.' information successfully retrieved',
                'data' => $data
            ];

        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function activeIndex()
    {
        try {
            if (!$this->hasPermission('index')) return $this->noAllowed();

            $data = $this->model::where('status', Status::$status_active)->get();

            return [
                'status' => true,
                'code' => 200,
                'message' => 'All active '.$this->modelName.' information has been retrieved.',
                'data' => $data
            ];
        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function show($id)
    {
        try {
            if (!$this->hasPermission('show')) return $this->noAllowed();

            $data =  $this->model::find($id);

            return [
                'status' => true,
                'code' => 200,
                'message' => $this->modelName.' information received',
                'data' => new $this->resource($data)
            ];

        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function store(array $data)
    {
        try {

            if (!$this->hasPermission('create')) return $this->noAllowed();

            $validator = $this->dataValidator($data, $this->storeFields());

            if ($validator['status']) {
                return [
                    'status' => false,
                    'code' => 422,
                    'message' => 'Validator error',
                    'errors' => $validator['validator']
                ];
            }

            $data = $validator['data'];
            $object = new $this->model;

            foreach ($this->storeFields() as $field) {
                $field->fill($object, $data);
            }

            $object->save();

            return [
                'status' => true,
                'code' => 200,
                'message' => $this->modelName.' information saved',
                'data' => new $this->resource($object)
            ];


        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }

    }

    public function update(array $data, $id)
    {
        try {

            if (!$this->hasPermission('update')) return $this->noAllowed();

            $item = $this->model::find($id);
            $validator = $this->dataValidator($data, $this->updateFields());
            if ($validator['status']) {
                return [
                    'status' => false,
                    'code' => 422,
                    'message' => 'Validator error',
                    'errors' => $validator['validator']
                ];
            }

            $data = $validator['data'];

            foreach ($this->updateFields() as $field) {
                $field->fill($item, $data);
            }
            $item->save();

            return [
                'status' => true,
                'code' => 200,
                'message' => $this->modelName.' information has been edited.',
                'data' => new $this->resource($item)
            ];

        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
    public function search($search = '')
    {
        if (!$this->hasPermission('index')) return $this->noAllowed();

        $data = $this->model::where(function ($query) use ($search) {
            foreach ($this->columns as $column) {
                $query->orWhere($column, 'like', '%' . $search . '%');
            }
        })
            ->limit(10)
            ->get();

        return [
            'status' => true,
            'message' => 'Data was successfully retrieved.',
            'statusCode' => 200,
            'data' => $this->resource::collection($data)
        ];
    }

    public function destroy($id)
    {
        try {
            if (!$this->hasPermission('delete')) return $this->noAllowed();

            $item = $this->model::find($id);
            $item->delete($id);

            return [
                'status' => true,
                'code' => 200,
                'message' => $this->modelName.' data successfully deleted.',
                'data' => $item
            ];

        }catch (Exception $e){
            return [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    // ------------------ Additional functions -------------------------------------------

    public function dataValidator($data, $fields)
    {
        $rules = [];
        foreach ($fields as $field) {
            $rules[$field->getName()] = $field->getRules();
        }
        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            $errors = [];
            foreach ($validator->errors()->getMessages() as $key => $value) {
                $errors[$key] = $value[0];
            }
            return [
                'status' => $validator->fails(),
                'validator' => $errors
            ];
        }
        return [
            'status' => $validator->fails(),
            'data' =>  $validator->validated(),
            'validator' => $validator
        ];

    }
    
    public function validator($fields, $data)
    {
        $error = null;
        $rules = [];
        foreach ($fields as $field) {

            $rules[$field->getName()] = $field->getRules();
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {

            $errors = [];

            foreach ($validator->errors()->getMessages() as $key => $value) {

                $errors[$key] = $value[0];
            }

            $error =  [
                'status' => false,
                'message' => 'Validation error',
                'statusCode' => 200,
                'data' => $errors
            ];
        }

        if ($error != null){
            return [
                'error' => true,
                'data' => null,
                'message' => $error
            ];
        }
        return [
            'error' => false,
            'data' => $validator->validated()
        ];
    }
    public function storeFields()
    {
        return [
            TextField::make('column')->setRules('required|string'),
        ];
    }
    public function updateFields()
    {
        return [
            TextField::make('column')->setRules('required|string'),
        ];
    }

    public function imageFields()
    {
        return [
            TextField::make('image')->setRules('nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048'),
        ];
    }

    public function documentFields()
    {
        return [
            TextField::make('document')->setRules('nullable|file|mimes:jpeg,png,jpg,gif,svg,doc,docx,xls,xlsx|max:10048'),
        ];
    }

    public function sendResponse(bool $status = true, string $message = 'success', int $statusCode = 200, $data = null)
    {
        return [
            'status' => $status,
            'message' => $message,
            'statusCode' => $statusCode,
            'data' => $data
        ];
    }

    public function uploadImages($model, $file)
    {

        if ($file->hasFile('images')) {
            $arxivImages = $model->images; // O'chirishni istagan faylning nomi
            if ($arxivImages != null) {
                foreach ($arxivImages as $arxivImage){
                    Storage::disk('public')->delete($arxivImage->url);
                }
                $model->images()->delete();
            }

            $images = $file->file('images');
            foreach ($images as $image){
                $path = $image->store('images', 'public'); // 'images' papkasi ichiga saqlaydi
                $newImage = new Image();
                $newImage->url = $path;
                $model->images()->save($newImage);

            }
        }
        return true;
    }

    public function uploadImagesOne($model, $file)
    {

        if ($file->hasFile('image')) {
            $arxivImages = $model->image; // O'chirishni istagan faylning nomi
            if ($arxivImages != null) {
                foreach ($arxivImages as $arxivImage){
                    Storage::disk('public')->delete($arxivImage->url);
                }
                $model->image()->delete();
            }

            $image = $file->file('image');
            $path = $image->store('images', 'public'); // 'images' papkasi ichiga saqlaydi
            $newImage = new Image();
            $newImage->url = $path;
            $model->image()->save($newImage);

        }
        return true;
    }

    public function hasPermission($name)
    {
        if ($this->menu == 'public'){
            return true;
        }

        $isPermission = RolePermission::where('role_id', auth()->user()->role_id)
                ->where('permission_name', $this->menu.'-'.$name )
                ->first();

        return $isPermission;

    }

    function formatNumber($number) {
        // Bo'sh joylarni olib tashlaymiz
        $number = str_replace(' ', '', $number);
        // Boshidagi 0larni olib tashlaymiz, lekin decimal nuqtadan oldingi barcha 0larni emas
        $number = ltrim($number, '0');
        // Agar decimal nuqta bo'lsa, va barcha 0lar olib tashlansa, boshiga 0 qo'shamiz
        if (strpos($number, '.') === 0) {
            $number = '0' . $number;
        }
        // Agar hammasi olib tashlansa va bo'sh bo'lib qolsa, 0 ga o'zgartiramiz
        if ($number === '') {
            $number = '0';
        }
        return $number;
    }

    public function noAllowed(){
        return [
            'status' => false,
            'code' => 401,
            'message' => 'You do not have permission to access the API',
            'data' => null
        ]; 
    }

}
