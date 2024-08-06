<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_customer extends Model
{
    use HasFactory;

    protected $fillable = ['kode', 'nama', 'telp'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->kode = 'C' . str_pad(M_customer::max('id') + 1, 3, '0', STR_PAD_LEFT);
        });
    }
}
