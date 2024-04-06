<?php

namespace XM\HistoricalDataBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use XM\HistoricalDataBundle\Requests\GetHistoricalDataRequest;
use XM\HistoricalDataBundle\Service\ExportData\ExportData;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;
use XM\HistoricalDataBundle\Service\FetchData\FetchData;

class HistoricalData
{
    protected $data;
    protected $csv;
    protected $req;

    public function __construct(
        protected FetchData $fetchData,
        protected ExportData $exportData,
        protected FetchComapnies $fetchComapnies,
        protected MailerInterface $mailer,
        protected ParameterBagInterface $parameters,
        protected LoggerInterface $logger
    ) {
        $this->data = [];
    }

    public function fetch(): object
    {
        $this->data = $this->fetchData->fetch($this->req->company_symbol);
        return $this;
    }

    public function get_company_name(): string
    {
        $companies = $this->fetchComapnies->get();
        return $companies[$this->req->company_symbol];
    }

    public function export()
    {
        $this->csv = $this->exportData->export($this->data);
        return $this;
    }

    public function filter_date()
    {
        $new_arr = [];

        foreach ($this->data as $item) {
            if ($item['date'] >= strtotime($this->req->start_date . ' 00:00:00') && $item['date'] <= strtotime($this->req->end_date . ' 23:59:59')) {
                $new_arr[] = $item;
            }
        }

        $this->data = $new_arr;
        return $this;
    }

    public function send_email()
    {
        try {
            $email = (new Email())
                ->from($this->parameters->get('FromAddress'))
                ->to($this->req->email_address)
                ->subject($this->get_company_name())
                ->text('From ' . $this->req->start_date . ' To ' . $this->req->end_date)
                ->attachFromPath($this->csv);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->log('error', $e);
        }
    }

    public function get(GetHistoricalDataRequest $req)
    {
        $this->req = $req;
        $this->fetch()->filter_date()->export()->send_email();
    }
}
