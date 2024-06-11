<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Error404Test extends TestCase
{
   use RefreshDatabase;

   /** @test
    * @dataProvider getTestableRoutes
    */

   public function fallback_to_404_if_entity_not_exists($route)
   {
      Menu::create([
         'name' => 'home-page',
         'is_active' => true
      ]);
      $res = $this->get($route);
      $res->assertStatus(404);
      $res->assertSeeText(__('errors.404'));
   }

   public static function getTestableRoutes(){
      return [
         [
            '/category/test-category'
         ],
         [
            '/tag/test-tag'
         ],
      ];
   }
}
