<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\Api\AService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AbstractController extends Controller
{
    protected $service;


    public function index()
    {
        $data = $this->service->index(request()->all());
        return $this->sendResponse($data);
    }
    public function getPaginate($count)
    {
        $data = $this->service->getPaginate($count);
        return $this->sendResponse($data);
    }

    public function orderBy($column, $type)
    {
        $data = $this->service->orderBy($column, $type);
        return $this->sendResponse($data);
    }
    public function activeIndex()
    {
        $data = $this->service->activeIndex(request()->all());
        return $this->sendResponse($data);
    }
    public function show($id)
    {
        $data = $this->service->show($id);
        return $this->sendResponse($data);
    }
    public function create()
    {
        $data = $this->service->store(request()->all());
        return $this->sendResponse($data);
    }

    public function update($id = null)
    {
        $data = $this->service->update(request()->all(), $id);
        return $this->sendResponse($data);
    }

    public function search($search)
    {
        $data = $this->service->search($search);
        return $this->sendResponse($data);
    }
    public function destroy($id)
    {
        $data = $this->service->destroy($id);
        return $this->sendResponse($data);
    }

    /**
     * @param $data
     * @return array|JsonResponse`
     */
    public function sendResponse($data)
    {
        return response()->json($data, 200);
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
}
