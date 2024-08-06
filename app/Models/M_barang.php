<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode',
        'nama',
        'harga'
    ];
}
