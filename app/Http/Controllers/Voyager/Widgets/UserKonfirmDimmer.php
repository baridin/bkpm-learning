<?php

namespace App\Http\Controllers\Voyager\Widgets;

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\Voyager\BaseDimmer;
use App\User;
use Illuminate\Database\Schema\Builder;
use App\DiklatDetailUser;

class UserKonfirmDimmer extends BaseDimmer
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
        // $count = User::whereHas('getDiklatDetail', function($builder){
        //     $builder->where('diklat_detail_users.status',10)->where('diklat_detail_users.file', '=', null);
        // })->get()->count();

        $count = DiklatDetailUser::where('file','=',null)->where('status',10)->get()->count();

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-group',
            'title'  => "{$count} Peserta Konfirmasi",
            'text'   => "Kamu mempunyai {$count} Peserta konfirmasi dalam database. Klik tombol dibawah untuk melihat semua Peserta konfirmasi.",
            'button' => [
                'text' => __('voyager::dimmer.page_link_text'),
                'link' => route('voyager.users.user-konfirm'),
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
