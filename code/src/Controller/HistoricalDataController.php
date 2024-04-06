<?php

namespace App\Controller;

// use App\Service\HistoricalData\HistoricalData;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use XM\HistoricalDataBundle\Requests\GetHistoricalDataRequest;
use XM\HistoricalDataBundle\Service\HistoricalData;

#[Route('/api/historical-data', format: 'json')]
class HistoricalDataController extends AbstractController
{
    #[Route('/', name: 'get_historical_data'),]
    public function getHistoricalData(GetHistoricalDataRequest $request, HistoricalData $historicaldata, LoggerInterface $logger): JsonResponse
    {
        try {
            $historicaldata->get($request);
            return $this->json(['success' => true]);
        } catch (Exception $e) {
            $logger->error($e);
            return $this->json(['success' => false], 400);
        }
    }
}
