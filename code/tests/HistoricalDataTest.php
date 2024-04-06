<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HistoricalDataTest extends KernelTestCase
{
    private $url, $client;

    public function setUp(): void
    {
        $this->url =  '/api/historical-data/';
        $this->client = new \GuzzleHttp\Client(['base_uri' => 'http://nginx',  'http_errors' => false]);
    }

    public function testEmptyRequest()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => []
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());


        $responseContnt = $response->getBody()->getContents();
        $this->assertJson($responseContnt);


        $responseArray = json_decode($responseContnt, true);
        $this->assertEquals('validation_failed', $responseArray['message']);

        // Check each error in the response
        $expectedErrors = [
            [
                "property" => "company_symbol",
                "value" => null,
                "message" => "This value should not be blank."
            ],
            [
                "property" => "email_address",
                "value" => null,
                "message" => "This value should not be blank."
            ],
            [
                "property" => "start_date",
                "value" => null,
                "message" => "This value should not be blank."
            ],
            [
                "property" => "end_date",
                "value" => null,
                "message" => "This value should not be blank."
            ]
        ];

        foreach ($expectedErrors as $expectedError) {
            $this->assertContains($expectedError, $responseArray['errors']);
        }
    }

    public function testValidRequest()
    {
        self::bootKernel();
        $container = static::getContainer();
        $csv_directory = $container->getParameter('csv_directory');
        $filename = $csv_directory . sha1(time()) . '.csv';

        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'company_symbol' => 'GOOGL',
                    'email_address' => 'test@example.com',
                    'start_date' => '2023-08-01',
                    'end_date' =>"2024-04-06"
                ]
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);

        $responseArray = json_decode($responseContent, true);
        $this->assertEquals('true', $responseArray['success']);
        $this->assertFileExists($filename);

       /*  $fileContent = file($filename);

        if (count($fileContent) > 1) {
            $firstRow = explode(',', $fileContent[1]);
            $lastRow =  explode(',', $fileContent[count($fileContent) - 1]);
            $this->assertGreaterThanOrEqual(date('Y-m-d', strtotime('-1 month')), $firstRow[0]);
            $this->assertLessThanOrEqual(date('Y-m-d'), $lastRow[0]);
        } */
    }

    public function testInvalidRequestFormat()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'body' => 'Invalid JSON data'
            ]
        );

        $this->assertEquals(400, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);
    }

    public function testInvalidCompanySymbol()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'company_symbol' => 'INVALID_SYMBOL',
                    'email_address' => 'test@example.com',
                    'start_date' => '2023-01-01',
                    'end_date' => '2023-12-31'
                ]
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);

        $responseArray = json_decode($responseContent, true);
        $this->assertEquals('validation_failed', $responseArray['message']);

        // Check the error for invalid company symbol
        $expectedError = [
            "property" => "company_symbol",
            "value" => "INVALID_SYMBOL",
            "message" => "The value you selected is not a valid choice."
        ];

        $this->assertContains($expectedError, $responseArray['errors']);
    }

    public function testInvalidDateFormat()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'company_symbol' => 'AAPL',
                    'email_address' => 'test@example.com',
                    'start_date' => '2023/01/01', // Invalid date format
                    'end_date' => '2023-12-1131'
                ]
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);

        $responseArray = json_decode($responseContent, true);
        $this->assertEquals('validation_failed', $responseArray['message']);

        // Check the error for invalid date format
        $expectedErrors = [[
            "property" => "start_date",
            "value" => "2023/01/01",
            "message" => "This value is not a valid date."
        ], [
            "property" => "end_date",
            "value" => "2023-12-1131",
            "message" => "This value is not a valid date."
        ]];

        foreach ($expectedErrors as  $expectedError) {
            $this->assertContains($expectedError, $responseArray['errors']);
        }
    }

    public function testFutureEndDate()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'company_symbol' => 'AAPL',
                    'email_address' => 'test@example.com',
                    'start_date' => '2023-01-01',
                    'end_date' => date('Y-m-d', strtotime('+1 day')) // Future end date
                ]
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);

        $responseArray = json_decode($responseContent, true);
        $this->assertEquals('validation_failed', $responseArray['message']);

        $this->assertEquals('end_date', $responseArray['errors'][0]['property']);
    }

    public function testInvalidEmailAddress()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'company_symbol' => 'AAPL',
                    'email_address' => 'invalid-email', // Invalid email format
                    'start_date' => '2023-01-01',
                    'end_date' => '2023-12-31'
                ]
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);

        $responseArray = json_decode($responseContent, true);
        $this->assertEquals('validation_failed', $responseArray['message']);

        // Check the error for invalid email address format
        $expectedError = [
            "property" => "email_address",
            "value" => "invalid-email",
            "message" => "This value is not a valid email address."
        ];

        $this->assertContains($expectedError, $responseArray['errors']);
    }

    public function testDateRangeValidation()
    {
        $response =  $this->client->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'company_symbol' => 'AAPL',
                    'email_address' => 'test@example.com',
                    'start_date' => '2023-12-31', // End date before start date
                    'end_date' => '2023-01-01'
                ]
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();
        $this->assertJson($responseContent);

        $responseArray = json_decode($responseContent, true);
        $this->assertEquals('validation_failed', $responseArray['message']);

        // Check the error for invalid date range
        $expectedError = [
            "property" => "end_date",
            "value" => "2023-01-01",
            "message" => "This value should be greater than \"2023-12-31\"."
        ];

        $this->assertContains($expectedError, $responseArray['errors']);
    }
}
