<?php

namespace XM\HistoricalDataBundle\Service\ExportData;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportCSV implements ExportData
{

    protected $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }
    
    public function export(array $data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sample.csv"');


        $filename = sha1(time()) . '.csv';

        $fp = fopen($this->parameters->get('csv_directory') . $filename, 'wb');
        foreach ($data as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);

        return 'public/csv/' . $filename;
    }
}
