<?php

namespace XM\HistoricalDataBundle\Service;

use XM\HistoricalDataBundle\Service\ExportData\ExportData;
use XM\HistoricalDataBundle\Service\FetchData\FetchData;

class HistoricalData
{
    protected $data;

    public function __construct(protected FetchData $fetchData, protected ExportData $exportData)
    {
        $this->data = [];
    }

    public function fetch(string $company_symbol): object
    {
        $this->data = $this->fetchData->fetch($company_symbol);
        return $this;
    }

    public function export(): string
    {
        return $this->exportData->export($this->data);
    }

    public function send_email(){

    }
}
