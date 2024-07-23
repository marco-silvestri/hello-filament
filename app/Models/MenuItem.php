<?php

namespace App\Models;

use App\Enums\Cms\MenuOptionsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'has_submenu' => 'boolean'
    ];

    public function childrens()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id')->where('has_submenu', 1);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getNavigationSlug():?string
    {
        if($this->type === MenuOptionsEnum::CATEGORY->getValue())
        {
            $slug = Category::find($this->value)->slug->name;
            return route('category', ['slug' => $slug]);
        }

        if($this->type === MenuOptionsEnum::TAG->getValue())
        {
            $slug = Tag::find($this->value)->slug->name;
            return route('tag', ['slug' => $slug]);
        }

        if($this->type === MenuOptionsEnum::EXTERNAL_URL->getValue())
        {
            return $this->value;
        }

        return null;
    }
}
