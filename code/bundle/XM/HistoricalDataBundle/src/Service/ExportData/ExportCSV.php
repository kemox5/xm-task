<?php

namespace XM\HistoricalDataBundle\Service\ExportData;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportCSV implements ExportData
{


    public function __construct(protected ParameterBagInterface $parameters)
    {
    }

    public function export(array $data): string
    {
        //unique filename
        $filename = $this->parameters->get('csv_directory') . sha1(time()) . '.csv';


        //convert date to YYYY-mm-dd format
        $data = array_map(function ($line) {
            $line['date'] = date('Y-m-d', $line['date']);
            return $line;
        }, $data);


        //prepare keys and capitalize first letter
        if (isset($data[0])) {
            $keys = array_keys($data[0]);
            array_unshift($data, array_map(function ($item) {
                return ucfirst($item);
            }, $keys));
        }

        //write result to csv
        $fp = fopen($filename, 'wb');
        foreach ($data as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);

        return $filename;
    }
}
