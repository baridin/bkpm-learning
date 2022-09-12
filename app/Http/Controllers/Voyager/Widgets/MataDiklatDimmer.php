<?php

namespace App\Http\Controllers\Voyager\Widgets;

use App\Http\Controllers\Voyager\BaseDimmer;
use App\MataDiklat;

class MataDiklatDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = MataDiklat::get()->count();
        // $string = trans_choice('voyager::dimmer.page', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-file-text',
            'title'  => "{$count} Mata Diklat",
            'text'   => "Kamu mempunyai {$count} mata diklat dalam database. Klik tombol dibawah untuk melihat semua mata diklat.",
            'button' => [
                'text' => 'Lihat semua mata diklat',
                'link' => route('voyager.mata-diklats.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/03.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return app('VoyagerAuth')->user()->can('browse', app('App\\MataDiklat'));
    }
}
