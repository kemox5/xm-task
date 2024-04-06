<?php

namespace XM\HistoricalDataBundle\Service\FetchData;

interface FetchData
{
    public function fetch(string $company): array;
}
