<?php

namespace XM\HistoricalDataBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use XM\HistoricalDataBundle\Service\FetchData\FetchData;

class FetchDataTest extends KernelTestCase
{
    private $fetchData;

    public function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $container->get(CacheInterface::class)->clear();

        $this->fetchData = $container->get(FetchData::class);
    }

    public function testValidSympol(): void
    {
        $result = $this->fetchData->fetch('GOOGL');

        $this->assertIsArray($result);

        $this->assertGreaterThanOrEqual(10, $result);
    }

    public function testInValidSympol(): void
    {
        $this->expectExceptionMessage('Cannot fetch data from RapidAPI');

        $this->fetchData->fetch('GOOGxLxx');
    }
}
