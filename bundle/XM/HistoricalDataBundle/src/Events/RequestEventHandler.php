<?php

namespace XM\HistoricalDataBundle\Events;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use XM\HistoricalDataBundle\Service\HistoricalData;

#[AsMessageHandler]
class RequestEventHandler
{
    public function __construct(
        private HistoricalData $historicalData
    ) {
    }

    public function __invoke(RequestEvent $requestEvent)
    {
        $this->historicalData->get($requestEvent->company_symbol, $requestEvent->email_address, $requestEvent->start_date, $requestEvent->end_date);
    }
}
