<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // INI KUNCI PENTINGNYA!
    // Kita harus daftarkan 'budget' disini biar bisa disave
    protected $fillable = [
        'nama', 
        'budget'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}