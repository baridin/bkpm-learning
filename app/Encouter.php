<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encouter extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'diklat_id',
        'diklat_detail_id',
        'mata_diklat_id',
        'title',
        'detail',
        'file',
        'start_at',
        'time',
        'duration',
        'settings',
        'options'
    ];

    public $timestamps = true;

    protected $mataDiklatId = 0;

    public function details()
    {
        return $this->hasMany(EncouterDetail::class, 'encouter_id', 'id')
            ->orderBy('type', 'asc')->inRandomOrder();
    }

    public function users($id=null)
    {
        if (!empty($id)) {
            return $this->hasOne(EncouterUser::class)->where('encouter_users.user_id', $id);
        } else {
            return $this->belongsToMany(User::class, 'encouter_users', 'encouter_id', 'user_id')->withPivot('assesment')->withTimestamps();
        }
    }

    public function detailDiklatId()
    {
        return $this->hasOne(DiklatDetail::class, 'id', 'diklat_detail_id');
    }

    public function mataDiklatId()
    {
        return $this->hasOne(MataDiklat::class, 'id', 'mata_diklat_id');
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
            'ujian'
        ];
    }

    public function ruleTypeSoal()
    {
        return [
            'pg',
            'essay'
        ];
    }
    
    public function bankSoal($mataDiklatId)
    {
        $query = BankSoal::query()
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
        return remove_banksoal_unused($query, $this->ruleBankSoal(), $mataDiklatId);

    }

    public function bankSoalUjian()
    {
        # code...
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

    public function autoChoseUjian($options, $mataDiklatId)
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
