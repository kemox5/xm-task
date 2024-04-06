<?php

namespace XM\HistoricalDataBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use XM\HistoricalDataBundle\Service\ExportData\ExportCsvFile;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;
use XM\HistoricalDataBundle\Service\FetchData\FetchData;

class ExportCsvFileTest extends KernelTestCase
{
    private $exportCsvFile, $container;

    public function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->container = $container;
        $this->exportCsvFile = $container->get('xm.export_csv');
    }

    public function testValid(): void
    {
        $this->exportCsvFile->export([]);

        $filename = $this->container->getParameter('csv_directory') . sha1(time()) . '.csv';

        $this->assertFileExists($filename);
    }
}
