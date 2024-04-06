<?php

namespace XM\HistoricalDataBundle\Service\ExportData;

interface ExportData
{
    public function export(array $data): string;
}
