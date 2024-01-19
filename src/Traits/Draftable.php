<?php

namespace Davidcb\LaravelDraftable\Traits;

use Mockery\Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Davidcb\LaravelDraftable\DraftableModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Draftable
{
    public function drafts(): MorphMany
    {
        return $this->morphMany(DraftableModel::class, 'draftable', 'draftable_model', 'draftable_id');
    }

    public static function getAllDrafts(bool $unfillable = false): Collection
    {
        $drafts = static::draftQuery()->get();

        return static::buildCollection($drafts, $unfillable);
    }

    public static function getPublishedDrafts(bool $unfillable = false): Collection
    {
        $drafts = static::draftQuery()->published()->get();

        return static::buildCollection($drafts, $unfillable);
    }

    public static function getNotPublishedDrafts(bool $unfillable = false): Collection
    {
        $drafts = static::draftQuery()->notPublished()->get();

        return static::buildCollection($drafts, $unfillable);
    }

    public function getPublishedDraft(): DraftableModel
    {
        return static::getPublishedDrafts()->first();
    }

    public function getDraft(int $id): DraftableModel
    {
        return static::draftQuery()->where('id', $id)->first();
    }

    public function publish(): self
    {
        if (is_null($this->published_at)) {
            $this->draft->publish();
        }

        return $this;
    }

    public function saveAsDraft(): self
    {
        $draftArray = $this->toArray();

        $draftableEntryArray = [
            'draftable_id' => $this->id,
            'draftable_data' => $draftArray,
            'draftable_model' => static::class,
            'published_at' => null,
            'data' => []
        ];

        try {
            $draft = DraftableModel::create($draftableEntryArray);
            $this->draft = $draft;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $this;
    }

    public function saveWithDraft(): self
    {
        $this->save();
        $draftArray = $this->toArray();
        unset($draftArray['id']);

        $draftableEntryArray = [
            'draftable_id' => $this->id,
            'draftable_data' => $draftArray,
            'draftable_model' => static::class,
            'published_at' => now(),
            'data' => []
        ];

        try {
            $draft = DraftableModel::create($draftableEntryArray);
            $this->draft = $draft;
        } catch (\Exception $e) {
            throw new  Exception($e->getMessage());
        }

        return $this;
    }

    private static function buildCollection(Collection $drafts, bool $unfillable = false): Collection
    {
        if ($unfillable) {
            return $drafts;
        }

        $collection = new Collection();

        foreach ($drafts as $draft) {
            $newClass = new static();
            $newClass->forceFill($draft->draftable_data);
            $newClass->published_at = $draft->published_at;
            $newClass->draft = $draft;
            $collection->push($newClass);
        }

        return $collection;
    }

    private static function draftQuery(): Builder
    {
        return DraftableModel::where('draftable_model', static::class)->latest();
    }
}
