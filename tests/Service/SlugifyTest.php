<?php

namespace App\Tests\Service;

use App\Service\Slugify;
use PHPUnit\Framework\TestCase;

class SlugifyTest extends TestCase
{
   /**
    * @dataProvider getSlugs
    */
   public function testSlug(string $string, string $slug)
   {
       $slugify = new Slugify();
       $this->assertSame($slug, $slugify->generate($string));
   }

   public function getSlugs()
   {
       return [
         ['Lorem Ipsum', 'lorem-ipsum'],
       	 ['  Lorem Ipsum  ', 'lorem-ipsum'],
       	 [' lOrEm  iPsUm  ', 'lorem-ipsum'],
       	 ['!Lorem Ipsum!', 'lorem-ipsum'],
       	 ['lorem-ipsum', 'lorem-ipsum'],
       	 ['lorem 日本語 ipsum', 'lorem-ipsum'],
       	 ['lorem русский язык ipsum', 'lorem-ipsum'],
        ];
   }
}
