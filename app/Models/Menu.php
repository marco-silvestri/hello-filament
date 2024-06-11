<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function items():HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public static function getNamedMenu(string $menuName)
    {
            return   
            Menu::where('name', $menuName)
            ->where('is_active', 1)
            ->first();
        
    }
}
