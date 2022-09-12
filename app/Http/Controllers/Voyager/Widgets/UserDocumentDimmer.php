<?php

namespace App\Http\Controllers\Voyager\Widgets;

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\Voyager\BaseDimmer;
use App\User;

class UserDocumentDimmer extends BaseDimmer
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
        $count = User::whereHas('getDiklatDetail', function($builder){
            $builder->where('diklat_detail_users.status', 1)->where('diklat_detail_users.file', '!=', null);
        })->whereStatus('active')->get()->count();
        // $string = trans_choice('voyager::dimmer.page', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-group',
            'title'  => "{$count} Dokumen Konfirmasi Peserta",
            'text'   => "Kamu mempunyai {$count} Dokumen Konfirmasi Peserta dalam database. Klik tombol dibawah untuk melihat semua Dokumen Konfirmasi Peserta.",
            'button' => [
                'text' => 'Lihat semua dokumen',
                'link' => route('voyager.users.user-document'),
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
