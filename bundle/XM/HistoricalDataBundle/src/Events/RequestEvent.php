<?php

namespace XM\HistoricalDataBundle\Events;

use XM\HistoricalDataBundle\Dto\HistoricalDataDto;

class RequestEvent
{
    public function __construct(
        public HistoricalDataDto $historicalDataDto
    ) {
    }
}
