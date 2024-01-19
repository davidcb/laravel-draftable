<?php

namespace Davidcb\LaravelDraftable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DraftableModel extends Model
{
    protected $dates = ['created_at', 'updated_at', 'published_at'];

    protected $casts = ['draftable_data' => 'array', 'data' => 'array'];

    protected $fillable = ['draftable_id', 'draftable_data', 'draftable_model', 'data', 'published_at'];

    public function __construct()
    {
        parent::__construct();
        $this->table = config('laravel-draftable.table_name', 'drafts');
    }

    public function scopeNotPublished($query): Builder
    {
        return $query->where('published_at', null);
    }

    public function scopePublished($query): Builder
    {
        return $query->where('published_at', '!=', null);
    }

    public function publish(): DraftableModel
    {
        try {
            $newClass = $this->draftable_model::create($this->draftable_data);
            $this->published_at = now();
            $this->draftable_id = $newClass->id;
            $this->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $this;
    }

    public function restore(): DraftableModel
    {
        try {
            $newClass = $this->draftable_model::where('id', $this->draftable_id)->first();

            if (empty($newClass)) {
                throw new \Exception('Can\'t Find Resource for ' . $this->draftable_model . ' with id ' . $this->draftable_id);
            }

            $newClass->update($this->draftable_data);
            $this->published_at = now();
            $this->draftable_id = $newClass->id;
            $this->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $this;
    }

    public function model(): DraftableModel
    {
        try {
            $newClass = new $this->draftable_model();
            $newClass->forceFill($this->draftable_data);
            $newClass->published_at = $this->published_at;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $newClass;
    }

    public function setData($key, $value): DraftableModel
    {
        $data = $this->data;
        $data[$key] = $value;
        $this->data = $data;
        $this->save();
        return $this;
    }

    public function getData($key): mixed
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
