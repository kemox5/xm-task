<?php

namespace XM\HistoricalDataBundle\Service\ExportData;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportCsvFile implements ExportData
{
    public function __construct(protected ParameterBagInterface $parameters)
    {
    }

    public function export(array $data): string
    {
        //unique filename
        $filename = $this->parameters->get('csv_directory') . sha1(time()) . '.csv';

        //write result to csv
        $fp = fopen($filename, 'wb');

        // set csv header row
        fputcsv($fp,  ['Date', 'Open', 'High', 'Low', 'Close', 'Volume']);

        // set rows
        foreach ($data as &$line) {

            //convert date to YYYY-mm-dd format
            $line['date'] = date('Y-m-d', $line['date']);

            $line = array_values($line);

            fputcsv($fp, $line);
        }

        fclose($fp);

        return $filename;
    }

    
}
