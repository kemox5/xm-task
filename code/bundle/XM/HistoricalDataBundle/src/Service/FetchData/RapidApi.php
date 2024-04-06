<?php

namespace XM\HistoricalDataBundle\Service\FetchData;

use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RapidApi implements FetchData
{

    public function __construct(protected HttpClientInterface $client, protected CacheInterface $cachePool, protected ParameterBagInterface $parameters)
    {
    }

    /**
     * Fetch data from RapidAPI and cache the result untill midnight
     */
    public function fetch($symbol): array
    {
        $RapidAPIKey = $this->parameters->get('RapidAPIKey');

        $prices = $this->cachePool->get($symbol, function (CacheItemInterface $cacheItemInterface) use ($RapidAPIKey) {

            $cacheItemInterface->expiresAt(new DateTimeImmutable(date('Y-m-d') . ' 23:59:59'));

            $response = $this->client->request(
                'GET',
                'https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data?symbol=AMRN&region=US',
                [
                    'headers' => [
                        'X-RapidAPI-Key: ' . $RapidAPIKey,
                        'X-RapidAPI-Host: yh-finance.p.rapidapi.com'
                    ]
                ]
            );

            if ($response->getStatusCode() == 200) {

                $content = $response->toArray();

                $prices = $content['prices'] ?? [];

                //remove adjclose col 
                return array_map(function ($item) {
                    unset($item['adjclose']);
                    return ($item);
                }, $prices);
            }
        });

        return $prices;
    }
}
