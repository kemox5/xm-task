<?php

namespace App\Controller;

// use App\Service\HistoricalData\HistoricalData;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use XM\HistoricalDataBundle\Events\RequestEvent;
use XM\HistoricalDataBundle\Requests\GetHistoricalDataRequest;

#[Route('/api/historical-data', format: 'json')]
class HistoricalDataController extends AbstractController
{
    #[Route('/', name: 'get_historical_data'),]
    public function getHistoricalData(GetHistoricalDataRequest $request, LoggerInterface $logger, MessageBusInterface $bus): JsonResponse
    {
        try {

            $cutomEvent = new RequestEvent($request->company_symbol, $request->email_address, $request->start_date, $request->end_date);
            $bus->dispatch( $cutomEvent);

            // $historicaldata->get($request);
            return $this->json(['success' => true]);
        } catch (Exception $e) {
            $logger->error($e);
            return $this->json(['success' => false], 400);
        }
    }
}
