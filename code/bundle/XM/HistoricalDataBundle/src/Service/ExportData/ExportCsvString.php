<?php

namespace XM\HistoricalDataBundle\Service\ExportData;


class ExportCsvString implements ExportData
{
    public function export(array $data): string
    {
        $res = implode(',', ['Date', 'Open', 'High', 'Low', 'Close', 'Volume']);

        foreach ($data as &$line) {

            //convert date to YYYY-mm-dd format
            $line['date'] = date('Y-m-d', $line['date']);

            $line = array_values($line);

            $res .= "\n" . implode(',',  $line);
        }

        return $res;
    }
}
