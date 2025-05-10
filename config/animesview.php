<?php

return [
    /**
     * Use uuid as primary key.
     */
    'uuids' => false,

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',

    /*
     * Anime tables foreign key name.
     */
    'anime_foreign_key' => 'anime_id',

    /*
     * Table name for views records.
     */
    'views_table' => 'views',

    /*
     * Model name for view record.
     */
    'view_model' => Animelhd\AnimesView\View::class,
	
	/*
     * Model name for vieweable record.
     */
    'vieweable_model' => App\Models\Anime::class,

     /*
     * Model name for viewer model.
     */
    'viewer_model' => App\Models\User::class,
];
