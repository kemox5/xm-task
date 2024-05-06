![Image Description](https://cloud.xm-cdn.com/static/xm/common/logos/XMLogo-2021_homepage.svg)

# XM PHP Exercise - v23.0.0

Get Stock Historical Prices for companies by their symbols.

## Requirments
https://github.com/kemox5/xm-task/blob/main/XM%20PHP%20Exercise-1.pdf


## Installation

```bash
- clone project
- cd project_path
- copy .env.example and rename it to .env
- fill empty configration in .env file
- run docker-composer up -d
```

## Usage

- Send POST request to http://localhost/api/historical-data/

```json
{
    "company_symbol": "AAIT",
    "email_address": "karimx@gmail.com",
    "start_date":"2023-08-01",
    "end_date": "2024-04-06"
}
```

## Testing
```bash
    - run php bin/phpunit 
    - run php bin/phpunit bundle/XM/HistoricalDataBundle/tests
```

## License

[MIT](https://choosealicense.com/licenses/mit/)
