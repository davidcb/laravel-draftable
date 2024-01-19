<?php

namespace Davidcb\LaravelDraftable\Test;

use Illuminate\Database\Eloquent\Model;
use Davidcb\LaravelDraftable\Traits\Draftable;

class Dummy extends Model
{
    use Draftable;

    protected $table = 'dummies';
    protected $guarded = [];
    public $timestamps = false;

    protected $fillable = ['title', 'description'];
}
