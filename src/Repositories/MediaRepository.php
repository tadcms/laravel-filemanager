<?php

namespace FileManager\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lararepo\Repositories\EloquentRepository;

class MediaRepository extends EloquentRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder $_model;
     * */
    protected $model;
    
    public function model()
    {
        return \FileManager\Models\Media::class;
    }
    
    public function create(array $attributes)
    {
        if (Auth::check()) {
            $attributes['user_id'] = Auth::id();
            $attributes['user_model'] = Auth::user()->getTable();
        }
        
        return parent::create($attributes);
    }
    
    public function update($id, array $attributes)
    {
        if (Auth::check()) {
            $attributes['user_id'] = Auth::id();
            $attributes['user_model'] = Auth::user()->getTable();
        }
        
        return parent::update($id, $attributes);
    }
    
    public function getFiles($folder_id, $type = 'image', $paginate = null)
    {
        $query = $this->model->where('folder_id', '=', $folder_id);
        
        if ($type) {
            $query->where('type', '=', $type);
        }
        
        if ($paginate) {
            return $query->paginate($paginate);
        }
        
        return $query->get();
    }
    
    /**
     * Get url file media
     *
     * @param int|\FileManager\Models\Media $file
     * @return string
     **/
    public function getFileUrl($file)
    {
        if (!is_a($file, 'FileManager\Models\Media')) {
            $file = $this->find($file);
        }
        
        $storage = Storage::disk(config('file-manager.upload_disk'));
        return $storage->url($file->path);
    }
}