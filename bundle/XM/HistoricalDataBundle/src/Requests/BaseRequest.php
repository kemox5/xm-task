<?php

namespace XM\HistoricalDataBundle\Requests;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    protected array $errors;

    public function __construct(protected ValidatorInterface $validator)
    {
        $this->errors = [];

        $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    public function validate()
    {
        $errors = $this->validator->validate($this);

        /** @var \Symfony\Component\Validator\ConstraintViolation  */
        foreach ($errors as $message) {
            $this->errors[] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        $this->throwError();
    }

    public function throwError()
    {
        $messages = ['success' => false, 'message' => 'validation_failed', 'errors' => $this->errors];
        if (count($messages['errors']) > 0) {
            $response = new JsonResponse($messages, 200);
            $response->send();
            exit;
        }
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    public function isValidDate($value,  $property)
    {
        if (!date_create($value)) {
            $this->errors[] = [
                'property' => $property,
                'value' => $value,
                'message' => 'This value should be a valid date',
            ];
            return false;
        }
        return true;
    }

    protected function populate(): void
    {
        if ($this->getRequest()->getContent() != '') {
            foreach ($this->getRequest()->toArray() as $property => $value) {
                if (property_exists($this, $property)) {
                    $this->{$property} = $value;
                }
            }
        }
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
