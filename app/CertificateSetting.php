<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    protected $fillable = [
        'logo',
        'logo_transkip',
        'kepala_pusdiklat',
        'nip_kepala_pusdiklat',
        'berdasar_akreditasi',
    ];

    public $timestamps = true;
}
