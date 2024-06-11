<?php // routes/cms/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.

use App\Models\Tag;
use App\Models\Post;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use App\Models\Category;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Home > Blog
// Breadcrumbs::for('blog', function (BreadcrumbTrail $trail) {
//     $trail->parent('home');
//     $trail->push('Blog', route('blog'));
// });

Breadcrumbs::for(
    'post',
    fn (BreadcrumbTrail $trail, Post $post) => $trail
        ->parent('home')
        ->push($post->title, route('post', $post->slug->name))
);

Breadcrumbs::for(
    'category',
    fn (BreadcrumbTrail $trail, Category $category) => $trail
        ->parent('home')
        ->push($category->name, route('category', $category->slug->name))
);

Breadcrumbs::for(
    'tag',
    fn (BreadcrumbTrail $trail, Tag $tag) => $trail
        ->parent('home')
        ->push($tag->name, route('tag', $tag->slug->name))
);
