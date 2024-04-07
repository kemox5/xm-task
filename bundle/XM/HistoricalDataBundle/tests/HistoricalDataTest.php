<?php

namespace XM\HistoricalDataBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\EventListener\MessengerTransportListener;
use Symfony\Component\Mailer\MailerInterface;
use XM\HistoricalDataBundle\Dto\HistoricalDataDto;
use XM\HistoricalDataBundle\Service\HistoricalData;

class HistoricalDataTest extends KernelTestCase
{
    private $historicalData, $container;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->container = $container;
        $this->historicalData = $container->get(HistoricalData::class);
    }

    public function testValid(): void
    {
        $start_date = '2022-01-01';
        $end_date = '2022-01-01';
        $company_symbol = 'GOOGL';
        $email_address = 'karim@app.com';

        $historicalDataDto = new HistoricalDataDto($company_symbol, $email_address, $start_date, $end_date);
        $this->historicalData->get($historicalDataDto);

        $mail = $this->getMailerMessage();
        $this->assertQueuedEmailCount(1);
        $this->assertEmailSubjectContains($mail, 'Google Inc.');
        $this->assertEmailAttachmentCount($mail, 1);
        $this->assertEmailAddressContains($mail, 'to', 'karim@app.com');
        $this->assertEmailTextBodyContains($mail, 'From ' . $start_date . ' To ' . $end_date);
    }
}
