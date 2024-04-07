<?php

namespace XM\HistoricalDataBundle\Dto;

class HistoricalDataDto
{

    public function __construct(public readonly string $company_symbol, public readonly string $email_address, public readonly string $start_date, public readonly string $end_date)
    {
    }
}
