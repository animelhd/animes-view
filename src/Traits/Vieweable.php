<?php

namespace Animelhd\AnimesView\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Animelhd\AnimesView\View;

trait Vieweable
{
    public function views(): HasMany
    {
        return $this->hasMany(config('animesview.view_model'), config('animesview.anime_foreign_key'));
    }
}