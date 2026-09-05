<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriMenu extends Model
{
    use HasFactory;

    protected $table = 'kategori_menus';

    protected $fillable = [
        'nama',
        'slug',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'kategori_id');
    }
}
