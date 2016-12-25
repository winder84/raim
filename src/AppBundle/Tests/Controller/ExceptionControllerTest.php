<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExceptionControllerTest extends WebTestCase
{
    public function testShow404()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/404');
    }

}
