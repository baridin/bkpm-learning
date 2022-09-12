<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pretest extends Model
{
    protected $fillable = [
        'diklat_id',
        'settings',
        'options'
    ];

    public $timestamps = true;

    public function users(int $id = null)
    {
        if (!is_null($id)) {
            return $this->hasOne(PretestUser::class)->where('pretest_users.user_id', $id);
        } else {
            return $this->belongsToMany(User::class, 'pretest_users', 'pretest_id','user_id')->withTimestamps()->withPivot('answer', 'value');
        }
    }

    public function details(int $id = null)
    {
        if (!is_null($id)) {
            return $this->hasOne(PretestDetail::class)->wherePretestId($id);
        } else {
            $query = $this->hasMany(PretestDetail::class);
            return $query;
        }
    }

    public function getOptionsAttribute()
    {
        return json_decode($this->attributes['options']);
    }

    public function getOptionsByKey(string $key)
    {
        $obj = $this->getOptionsAttribute();
        return empty($obj) ? 0 : $obj->{$key};
    }

    public function ruleBankSoal()
    {
        return [
            
            'pretest_postest'
        ];
    }

    public function ruleTypeSoal()
    {
        return [
            'pg'
        ];
    }
    
    public function bankSoal()
    {
        return BankSoal::query()
            ->when($this->ruleBankSoal(), function ($query) {
                $query->whereJsonContains('type', $this->ruleBankSoal());
                foreach ($this->ruleBankSoal() as $k => $rb) {
                    $query->orWhereJsonContains('type', $rb);
                }
                return $query;
            })
            ->whereIn('type_soal', $this->ruleTypeSoal())
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('type');
    }

    public function countBankSoal(string $type)
    {
        $i = 0;
        if ($this->details->count() > 0) {
            foreach ($this->details as $kd => $vd) {
                if (!empty($vd->bank_soal_id) && is_array($vd->bankSoal->type)) {
                    foreach ($vd->bankSoal->type as $key => $value) {
                        if ($value === $type) { ++$i; }
                    }
                }
            }
        }
        return $i;
    }

    public function chosenBankSoalId($id)
    {
        $bankSoal = $this->details()->whereBankSoalId($id)->first();
        if (!empty($bankSoal)) {
            return true;
        }
        return false;
    }

    
    // public function getBankSoalByTypeSoal(string $type, int $count, $mataDiklatId = null)
    // {
    //     $query = BankSoal::where('type_soal',$type)->where('mata_diklat_id',$mataDiklatId);
    //         // if (!empty($mataDiklatId)) { $query = $query); }
    //         foreach ($this->ruleBankSoal() as $value) {
    //             $query = $query->orWhereRaw(
    //                 'JSON_CONTAINS(type, \'['.$value.']\')'
    //             );
    //         }
    //     return $query->inRandomOrder()->limit($count)->get();
    // }
    // public function autoChose($options, $mataDiklatId = null)
    // {
    //     $collectData = collect([]);
    //     foreach ($options as $ko => $vo) {
    //         if ((int)$vo > 0) {
    //             $collectData->push($this->getBankSoalByTypeSoal($ko, $vo, $mataDiklatId)->toArray());
    //         }
    //     }

    //     return $collectData;
    // }
    public function getBankSoalByTypeSoal(string $type, int $count, $mataDiklatId)
    {
        $query = BankSoal::where('type_soal',$type)->where('mata_diklat_id',$mataDiklatId);
            // foreach ($this->ruleBankSoal() as $value) {
            //     $query = $query->orWhereRaw(
            //         'JSON_CONTAINS(type, \'['.$value.']\')'
            //     );
            // }
        return $query->inRandomOrder()->limit($count)->get();
    }

    public function autoChose($options, $mataDiklatId)
    {
        $collectData = collect([]);
        foreach ($options as $ko => $vo) {
            if ((int)$vo > 0) {
                $collectData->push($this->getBankSoalByTypeSoal($ko, $vo, $mataDiklatId)->toArray());
            }
        }
        return $collectData;
    }
}
