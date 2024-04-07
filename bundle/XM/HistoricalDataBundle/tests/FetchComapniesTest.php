<?php

namespace XM\HistoricalDataBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;
use XM\HistoricalDataBundle\Service\FetchData\FetchData;

class FetchComapniesTest extends KernelTestCase
{
    private $fetchComapnies;

    public function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $container->get(CacheInterface::class)->clear();

        $this->fetchComapnies = $container->get(FetchComapnies::class);
    }

    public function testValid(): void
    {
        $result = $this->fetchComapnies->get();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(10, $result);
    }
}
