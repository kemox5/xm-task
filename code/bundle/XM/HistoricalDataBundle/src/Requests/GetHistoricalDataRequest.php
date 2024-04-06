<?php

namespace XM\HistoricalDataBundle\Requests;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XM\HistoricalDataBundle\Service\FetchCompanies\FetchComapnies;


#[Assert\GroupSequence(['GetHistoricalDataRequest', 'Strict', 'Final'])]
class GetHistoricalDataRequest extends BaseRequest
{
    #[Assert\Choice(callback: 'getCompanies')]
    #[NotBlank()]
    public string $company_symbol;

    #[Assert\Email]
    #[NotBlank()]
    public string $email_address;

    #[NotBlank()]
    #[Assert\Date]
    public string $start_date;

    #[NotBlank()]
    #[Assert\Date]
    public string $end_date;


    public function __construct(protected ValidatorInterface $validator, protected FetchComapnies $fetchComapnies)
    {
        parent::__construct($validator);
        $this->validateDates();
    }

    public function getCompanies(): array
    {
        return  array_keys($this->fetchComapnies->get());
    }

    #[Assert\LessThanOrEqual(
        value: 'today',
        groups: ['Strict'],
    )]
    public function getStart_date(): \DateTimeInterface
    {
        return date_create($this->start_date);
    }

    #[Assert\LessThanOrEqual(
        'today',
        groups: ['Strict'],
    )]
    public function getEnd_date(): \DateTimeInterface
    {
        return date_create($this->end_date);
    }


    public function validateDates()
    {
        $emailConstraint = new Assert\GreaterThan($this->start_date);
        $errors = $this->validator->validate($this->end_date, $emailConstraint);
        foreach ($errors as $message) {
            $this->errors[] = [
                'property' => 'end_date',
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }
        $this->throwError($this->errors);
    }
}
