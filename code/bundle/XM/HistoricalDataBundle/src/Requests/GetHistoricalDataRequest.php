<?php

namespace XM\HistoricalDataBundle\Requests;

use DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;

class GetHistoricalDataRequest extends BaseRequest
{
    #[Assert\Choice(callback: 'getCompanies')]
    #[NotBlank()]
    public string $company_symbol;

    #[Assert\Email]
    #[NotBlank()]
    public string $email_address;

    #[Assert\Date]
    #[NotBlank()]
    public string $start_date;

    #[Assert\Date]
    #[NotBlank()]
    public string $end_date;

    public function __construct(protected ValidatorInterface $validator, protected FetchComapnies $fetchComapnies)
    {
        parent::__construct($validator);
    }

    public function getCompanies()
    {
        return  array_keys($this->fetchComapnies->get());
    }
}
