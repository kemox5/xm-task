<?php

namespace XM\HistoricalDataBundle\Service\FetchCompanies;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FetchComapnies
{

    public function __construct(protected HttpClientInterface $client, protected CacheInterface $cachePool)
    {
    }


    public function get(): array
    {
        $companies = $this->cachePool->get('companies', function (CacheItemInterface $cacheItemInterface) {

            $cacheItemInterface->expiresAfter(60 * 60 * 24 * 30);

            $response = $this->client->request(
                'GET',
                'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json'
            );

            if ($response->getStatusCode() == 200) {
                $content = $response->toArray();

                $data = [];

                foreach ($content as $item) {
                    if (isset($item['Symbol']) && isset($item['Company Name']))
                        $data[$item['Symbol']] = $item['Company Name'];
                }

                return $data;
            } else {
                throw new HttpException(500, 'Cannot fetch compnies');
            }
        });

        return $companies;
    }
}
