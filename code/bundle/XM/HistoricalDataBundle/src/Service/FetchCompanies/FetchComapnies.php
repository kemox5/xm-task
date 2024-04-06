<?php

namespace XM\HistoricalDataBundle\Service\FetchCompanies;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
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

            $cacheItemInterface->expiresAfter(60*60*24*30);

            $response = $this->client->request(
                'GET',
                'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json'
            );

            $statusCode = $response->getStatusCode();
            // $statusCode = 200
            $contentType = $response->getHeaders()['content-type'][0];
            // $contentType = 'application/json'
            $content = $response->getContent();
            // $content = '{"id":521583, "name":"symfony-docs", ...}'
            $content = $response->toArray();
            // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

            $data = [];

            foreach($content as $item){
                $data[$item['Symbol']] = $item['Company Name'];
            }

            return $data;
        });

        return $companies;
    }
}
