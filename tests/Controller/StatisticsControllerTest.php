<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatisticsControllerTest extends WebTestCase
{
    public function testStatistics()
    {
        $client = static::createClient();

        $client->request('GET', '/statistics.html');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testOrdering()
    {
        $client = static::createClient();

        $client->request('GET', '/ordering.html');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
