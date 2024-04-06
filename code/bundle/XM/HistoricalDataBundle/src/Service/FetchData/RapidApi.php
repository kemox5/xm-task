<?php

namespace XM\HistoricalDataBundle\Service\FetchData;

class RapidApi implements FetchData
{

    public function fetch(string $company): array
    {
        $data = [
            [
                "val1",
                "val2",
                "val3",
            ]
        ];


        return $data;
    }
}
