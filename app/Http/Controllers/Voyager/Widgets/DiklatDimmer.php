<?php

namespace App\Http\Controllers\Voyager\Widgets;

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\Voyager\BaseDimmer;
use App\Diklat;

class DiklatDimmer extends BaseDimmer
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
        $count = Diklat::get()->count();
        // $string = trans_choice('voyager::dimmer.post', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-news',
            'title'  => "{$count} Diklat",
            'text'   => "Kamu mempunyai {$count} diklat dalam database. Klik tombol dibawah untuk melihat semua diklat.",
            'button' => [
                'text' => 'Lihat semua diklat',
                'link' => route('voyager.diklats.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return app('VoyagerAuth')->user()->can('browse', app('App\\Diklat'));
    }
}
