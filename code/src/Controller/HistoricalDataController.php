<?php

namespace App\Controller;

// use App\Service\HistoricalData\HistoricalData;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use XM\HistoricalDataBundle\Requests\GetHistoricalDataRequest;
use XM\HistoricalDataBundle\Service\HistoricalData;

#[Route('/historical-data')]
class HistoricalDataController extends AbstractController
{
    #[Route('/', name: 'get_historical_data'),]
    public function getHistoricalData(GetHistoricalDataRequest $request, HistoricalData $historicaldata): JsonResponse
    {
        try {
            $historicaldata->get($request);

            return $this->json(['success' => true]);
        } catch (TransportExceptionInterface $e) {
            die($e);
            return $this->json(['success' => false], 400);
        } catch (Exception $e) {
            die($e);

            return $this->json(['success' => false], 400);
        }
    }
}
