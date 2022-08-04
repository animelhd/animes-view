<?php

namespace Animelhd\AnimesView\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property \Illuminate\Database\Eloquent\Collection $views
 */
trait Viewer
{
    public function view(Model $object)
    {
        /* @var \Animelhd\AnimesView\Traits\Vieweable $object */
        if (!$this->hasViewed($object)) {
            $view = app(config('animesview.view_model'));
            $view->{config('animesview.user_foreign_key')} = $this->getKey();

            $object->views()->save($view);
        }
    }

    public function unview(Model $object)
    {
        /* @var \Animelhd\AnimesView\Traits\Vieweable $object */
        $relation = $object->views()
            ->where('vieweable_id', $object->getKey())
            ->where('vieweable_type', $object->getMorphClass())
            ->where(config('animesview.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

    public function toggleView(Model $object)
    {
        $this->hasViewed($object) ? $this->unview($object) : $this->view($object);
    }

    public function hasViewed(Model $object): bool
    {
        return ($this->relationLoaded('views') ? $this->views : $this->views())
            ->where('vieweable_id', $object->getKey())
            ->where('vieweable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function views(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(config('animesview.view_model'), config('animesview.user_foreign_key'), $this->getKeyName());
    }

    public function attachViewStatus($vieweables, callable $resolver = null)
    {
        $returnFirst = false;
        $toArray = false;

        switch (true) {
            case $vieweables instanceof Model:
                $returnFirst = true;
                $vieweables = \collect([$vieweables]);
                break;
            case $vieweables instanceof LengthAwarePaginator:
                $vieweables = $vieweables->getCollection();
                break;
            case $vieweables instanceof Paginator:
                $vieweables = \collect($vieweables->items());
                break;
            case \is_array($vieweables):
                $vieweables = \collect($vieweables);
                $toArray = true;
                break;
        }

        \abort_if(!($vieweables instanceof Collection), 422, 'Invalid $vieweables type.');

        $viewed = $this->views()->get()->keyBy(function ($item) {
            return \sprintf('%s-%s', $item->vieweable_type, $item->vieweable_id);
        });

        $vieweables->map(function ($vieweable) use ($viewed, $resolver) {
            $resolver = $resolver ?? fn ($m) => $m;
            $vieweable = $resolver($vieweable);

            if ($vieweable && \in_array(Vieweable::class, \class_uses($vieweable))) {
                $key = \sprintf('%s-%s', $vieweable->getMorphClass(), $vieweable->getKey());
                $vieweable->setAttribute('has_viewed', $viewed->has($key));
            }
        });

        return $returnFirst ? $vieweables->first() : ($toArray ? $vieweables->all() : $vieweables);
    }

    /**
     * Get Query Builder for views
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function getViewItems(string $model)
    {
        return app($model)->whereHas(
            'viewers',
            function ($q) {
                return $q->where(config('animesview.user_foreign_key'), $this->getKey());
            }
        );
    }
}
