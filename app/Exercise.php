<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'mata_diklat_id',
        'section_id',
        'title',
        'type',
        'line',
        'settings',
        'options'
    ];

    public $timestamps = true;

    public function details()
    {
        $query = $this->hasMany(ExerciseDetail::class, 'exercise_id', 'id');
        if ($this->attributes['settings'] == 'otomatis') {
            $query->inRandomOrder();
        }
        return $query;
    }

    public function users($user=null, $exer=null)
    {
        if (!is_null($user) && !is_null($exer)) {
            return $this->hasMany(ExerciseUser::class, 'exercise_id', 'id')->whereExerciseId($exer)->whereUserId($user);
        } else {
            return $this->belongsToMany(User::class, 'exercise_users', 'exercise_id', 'user_id')->withPivot('assesment')->withTimestamps();
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
            'latihan',
        ];
    }

    public function ruleTypeSoal()
    {
        return [
            'pg',
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
