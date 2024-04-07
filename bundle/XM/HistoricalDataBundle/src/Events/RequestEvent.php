<?php

namespace XM\HistoricalDataBundle\Events;


class RequestEvent
{
    public function __construct(
        public string $company_symbol,
        public string $email_address,
        public string $start_date,
        public string $end_date,
    ) {
    }
}
