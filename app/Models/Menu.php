<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return Cache::remember("menu-{$menuName}", 60*60*24, function() use($menuName){
            return Menu::with('items')
                ->where('name', $menuName)
                ->where('is_active', 1)
                ->first();
        });
    }
}
