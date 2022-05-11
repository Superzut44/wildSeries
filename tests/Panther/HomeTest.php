<?php

namespace App\Tests\Panther;

use Symfony\Component\Panther\PantherTestCase;

class PantherControllerTest extends PantherTestCase
{
   public function testHome()
   {
       $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
       $crawler = $client->request('GET', '/login');

       $this->assertContains('Symfony Demo', $crawler->filter('a')->html()); // You can use any PHPUnit assertion
   }
}

