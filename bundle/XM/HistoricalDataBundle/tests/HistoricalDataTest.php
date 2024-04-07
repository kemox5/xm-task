<?php

namespace XM\HistoricalDataBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XM\HistoricalDataBundle\Requests\GetHistoricalDataRequest;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;
use XM\HistoricalDataBundle\Service\HistoricalData;

class HistoricalDataTest extends KernelTestCase
{
    private $historicalData;

    public function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->historicalData = $container->get(HistoricalData::class);
    }

    public function testValid(): void
    {
        $start_date = '2022-01-01';
        $end_date = '2022-01-01';
        $company_symbol = 'GOOGL';
        $email_address = 'karim@app.com';

        $this->historicalData->get($company_symbol, $email_address, $start_date, $end_date);

        $mail = $this->getMailerMessage();
        $this->assertQueuedEmailCount(1);
        $this->assertEmailSubjectContains($mail, 'Google Inc.');
        $this->assertEmailAttachmentCount($mail, 1);
        $this->assertEmailAddressContains($mail, 'to', 'karim@app.com');
        $this->assertEmailTextBodyContains($mail, 'From ' . $start_date . ' To ' . $end_date);
    }
}
