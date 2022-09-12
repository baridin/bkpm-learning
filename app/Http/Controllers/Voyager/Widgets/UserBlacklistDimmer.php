<?php

namespace App\Http\Controllers\Voyager\Widgets;

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\Voyager\BaseDimmer;
use App\User;
use Illuminate\Database\Schema\Builder;

class UserBlacklistDimmer extends BaseDimmer
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
        $count = User::whereStatus('blacklist')->get()->count();
        // $string = trans_choice('voyager::dimmer.page', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-group',
            'title'  => "{$count} Peserta ditolak",
            'text'   => "Kamu mempunyai {$count} Peserta ditolak dalam database. Klik tombol dibawah untuk melihat semua Peserta ditolak.",
            'button' => [
                'text' => 'Lihat semua peserta ditolak',
                'link' => url('admin/users?key=status&filter=contains&s=blacklist'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/01.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return app('VoyagerAuth')->user()->can('browse', app('App\\User'));
    }
}
