# Code Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/macmotp/codegen.svg?style=flat-square)](https://packagist.org/packages/macmotp/codegen)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/macmotp/codegen/run-tests?label=tests)](https://github.com/macmotp/codegen/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/macmotp/codegen.svg?style=flat-square)](https://packagist.org/packages/macmotp/codegen)

**Generate human friendly codes**

## Installation

You can install the package via composer:

```bash
composer require macmotp/codegen
```

##  Usage
   
#### Create semantic and sanitized reference codes from any string
``` php
use Macmotp\Codegen;

$generator = new Codegen();

echo $generator->make('John Doe')->forHumans();

// (string) 'JHND'

$set = $generator->make('John Doe')->toArray();

dump($set);

// (array) [
//    'JHND',
//    'JHDE',
//    'JHNE',
//    'JNDE',
//    'JHNN',
//    'JNDD',
//    'JHNA',
//    'JHNK',
//    'JHN3',
//    'JHNT',
// ]
```
_Once the possibilities are running low due to lack of letters from the source, it will apply random letters for unique solutions._

#### Set up a configuration if you need to prepend/append patterns or incrementing numbers
``` php
use Macmotp\Codegen;
use Macmotp\Codegen\Config;

$config = new Config();
$config->codeLength = 8;
$config->prepend = 'NR';
$config->numberLength = 6;
$config->startingNumber = 14;
$config->incremental = true;
$config->sanitize = false;
$config->count = 5;

$generator = new Codegen($config);

$receiptNumbers = $generator->make()->toArray();

dump($receiptNumbers);

// (array) [
//    'NR000014',
//    'NR000015',
//    'NR000016',
//    'NR000017',
//    'NR000018',
// ]
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](changelog.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/contributing.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/security.md) on how to report security vulnerabilities.

## Credits

- [Marco Gava](https://github.com/macmotp)

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
