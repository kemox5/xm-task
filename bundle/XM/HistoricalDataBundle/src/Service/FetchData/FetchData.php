<?php

namespace XM\HistoricalDataBundle\Service\FetchData;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface FetchData
{
    public function fetch(string $company): array;
}
