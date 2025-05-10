<?php

namespace Animelhd\AnimesView\Traits;

use Animelhd\AnimesView\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Anime;

trait Viewer
{
	public function view(Anime $anime): void
    {
        if (! $this->hasViewed($anime)) {
            $this->views()->create([
                'anime_id' => $anime->getKey(),
            ]);
        }
    }

    public function unview(Anime $anime): void
    {
        $this->views()
            ->where('anime_id', $anime->getKey())
            ->delete();
    }

    public function toggleView(Anime $anime): void
    {
        $this->hasViewed($anime)
            ? $this->unview($anime)
            : $this->view($anime);
    }

    public function hasViewed(Anime $anime): bool
    {
        return $this->views()
            ->where('anime_id', $anime->getKey())
            ->exists();
    }

    public function views()
    {
        return $this->hasMany(View::class, config('animesview.user_foreign_key'));
    }
}
