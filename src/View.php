<?php

namespace Animelhd\AnimesView;

use Illuminate\Database\Eloquent\Model;
use App\Models\Anime;
use Animelhd\AnimesView\Events\Viewed;
use Animelhd\AnimesView\Events\Unviewed;

class View extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => Viewed::class,
        'deleted' => Unviewed::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('animesview.views_table');
        parent::__construct($attributes);
    }

    public function anime()
    {
        return $this->belongsTo(config('animesview.vieweable_model'), config('animesview.anime_foreign_key'));
    }

    public function user()
    {
        return $this->belongsTo(config('animesview.user_model'), config('animesview.user_foreign_key'));
    }
}
