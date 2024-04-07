<?php

namespace XM\HistoricalDataBundle\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use XM\HistoricalDataBundle\Dto\HistoricalDataDto;
use XM\HistoricalDataBundle\Requests\GetHistoricalDataRequest;
use XM\HistoricalDataBundle\Service\ExportData\ExportData;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;
use XM\HistoricalDataBundle\Service\FetchData\FetchData;

class HistoricalData
{
    protected array $data;
    protected string $csv;
    private $historicalDataDto;

    public function __construct(
        protected FetchData $fetchData,
        protected ExportData $exportData,
        protected FetchComapnies $fetchComapnies,
        protected MailerInterface $mailer,
        protected ParameterBagInterface $parameters,
        protected LoggerInterface $logger,
    ) {
        $this->data = [];
    }

    /**
     * Start the proccess
     */
    public function get(
         HistoricalDataDto $historicalDataDto
    ) {
        $this->historicalDataDto = $historicalDataDto;

        try {
            $this->fetch_data()->filter_dates()->export_data()->send_email();
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }


    /**
     * Fetch data from FetchData service 
     */
    public function fetch_data(): object
    {
        $this->data = $this->fetchData->fetch($this->historicalDataDto->company_symbol);
        return $this;
    }


    /**
     * Filter data between start_date and end_date
     */
    public function filter_dates()
    {
        $new_arr = [];

        foreach ($this->data as $item) {
            if ($item['date'] >= strtotime($this->historicalDataDto->start_date . ' 00:00:00') && $item['date'] <= strtotime($this->historicalDataDto->end_date . ' 23:59:59')) {
                $new_arr[] = $item;
            }
        }

        $this->data = $new_arr;
        return $this;
    }


    /**
     * Export data by ExportData service
     */
    public function export_data()
    {
        $this->csv = $this->exportData->export($this->data);
        return $this;
    }


    /**
     * Send result to email_address
     */
    public function send_email()
    {
        $company_name = $this->get_company_name();

        try {
            $email = (new Email())
                ->from($this->parameters->get('FromAddress'))
                ->to($this->historicalDataDto->email_address)
                ->subject($company_name)
                ->text('From ' . $this->historicalDataDto->start_date . ' To ' . $this->historicalDataDto->end_date)
                ->attach($this->csv, sha1(time()) . '.csv', 'text/csv');

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e);
        }
    }

    /**
     * Get company_name to use it as subject for the email
     */
    public function get_company_name(): string
    {
        $companies = $this->fetchComapnies->get();
        return $companies[$this->historicalDataDto->company_symbol];
    }
}
