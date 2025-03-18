## Laravel View

❤️ User view feature for Laravel Application.

[![CI](https://github.com/overtrue/laravel-view/workflows/CI/badge.svg)](https://github.com/overtrue/laravel-view/actions)
[![Latest Stable Version](https://poser.pugx.org/overtrue/laravel-view/v/stable.svg)](https://packagist.org/packages/overtrue/laravel-view)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/laravel-view/v/unstable.svg)](https://packagist.org/packages/overtrue/laravel-view)
[![Total Downloads](https://poser.pugx.org/overtrue/laravel-view/downloads)](https://packagist.org/packages/overtrue/laravel-view)
[![License](https://poser.pugx.org/overtrue/laravel-view/license)](https://packagist.org/packages/overtrue/laravel-view)

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Installing

```shell
composer require animelhd/animes-view -vvv
```

### Configuration & Migrations

```php
php artisan vendor:publish --provider="Animelhd\AnimesView\ViewServiceProvider"
```

## Usage

### Traits

#### `Animelhd\AnimesView\Traits\Viewer`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Animelhd\AnimesView\Traits\Viewer;

class User extends Authenticatable
{
    use Viewer;

    <...>
}
```

#### `Animelhd\AnimesView\Traits\Vieweable`

```php
use Illuminate\Database\Eloquent\Model;
use Animelhd\AnimesView\Traits\Vieweable;

class Post extends Model
{
    use Vieweable;

    <...>
}
```

### API

```php
$user = User::find(1);
$post = Post::find(2);

$user->view($post);
$user->unview($post);
$user->toggleView($post);
$user->getViewItems(Post::class)

$user->hasViewed($post);
$post->hasBeenViewedBy($user);
```

#### Get object viewers:

```php
foreach($post->viewers as $user) {
    // echo $user->name;
}
```

#### Get View Model from User.

Used Viewer Trait Model can easy to get Vieweable Models to do what you want.
_note: this method will return a `Illuminate\Database\Eloquent\Builder` _

```php
$user->getViewItems(Post::class);

// Do more
$viewPosts = $user->getViewItems(Post::class)->get();
$viewPosts = $user->getViewItems(Post::class)->paginate();
$viewPosts = $user->getViewItems(Post::class)->where('title', 'Laravel-View')->get();
```

### Aggregations

```php
// all
$user->views()->count();

// with type
$user->views()->withType(Post::class)->count();

// viewers count
$post->viewers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount('views')->get();

foreach($users as $user) {
    echo $user->views_count;
}


// for Vieweable models:
$posts = Post::withCount('viewers')->get();

foreach($posts as $post) {
    echo $post->views_count;
}
```

### Attach user view status to vieweable collection

You can use `Viewer::attachViewStatus($vieweables)` to attach the user view status, it will set `has_viewed` attribute to each model of `$vieweables`:

#### For model

```php
$post = Post::find(1);

$post = $user->attachViewStatus($post);

// result
[
    "id" => 1
    "title" => "Add socialite login support."
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_viewed" => true
 ],
```

#### For `Collection | Paginator | CursorPaginator | array`:

```php
$posts = Post::oldest('id')->get();

$posts = $user->attachViewStatus($posts);

$posts = $posts->toArray();

// result
[
  [
    "id" => 1
    "title" => "Post title1"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_viewed" => true
  ],
  [
    "id" => 2
    "title" => "Post title2"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_viewed" => false
  ],
  [
    "id" => 3
    "title" => "Post title3"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_viewed" => true
  ],
]
```

#### For pagination

```php
$posts = Post::paginate(20);

$user->attachViewStatus($posts);
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
// Viewer
$users = User::with('views')->get();

foreach($users as $user) {
    $user->hasViewed($post);
}

// with vieweable object
$users = User::with('views.vieweable')->get();

foreach($users as $user) {
    $user->hasViewed($post);
}

// Vieweable
$posts = Post::with('views')->get();
// or
$posts = Post::with('viewers')->get();

foreach($posts as $post) {
    $post->isViewedBy($user);
}
```

### Events

| **Event**                                     | **Description**                             |
| --------------------------------------------- | ------------------------------------------- |
| `Animelhd\AnimesView\Events\Viewed`   | Triggered when the relationship is created. |
| `Animelhd\AnimesView\Events\Unviewed` | Triggered when the relationship is deleted. |

## License

MIT
