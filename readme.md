# Code Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/macmotp/codegen.svg)](https://packagist.org/packages/macmotp/codegen)
[![Total Downloads](https://img.shields.io/packagist/dt/macmotp/codegen.svg)](https://packagist.org/packages/macmotp/codegen)
[![codecov](https://codecov.io/gh/macmotp/codegen/branch/main/graph/badge.svg?token=K55RQULWLJ)](undefined)

**Generate human friendly codes**

Useful for generation of referral codes based on names, receipt numbers, unique references.

## Installation

You can install the package via composer:

```bash
composer require macmotp/codegen
```

## Usage
   
#### Create semantic and sanitized reference codes from any string
``` php
use Macmotp\Codegen;

$generator = new Codegen();

echo $generator->generate('Bob McLovin');

// (string) 'BBMCLV'

echo $generator->generate('Company Name');

// (string) 'CMPYNM'
```

#### Create collections of codes
``` php
use Macmotp\Codegen;

$generator = new Codegen();

echo $generator->collection('Bob McLovin', 4);

// (array) [
//    'BBMCLV',
//    'BBMCLN',
//    'BBMCVN',
//    'BBMLVN',
// ];
```

## Configuration
#### Set your configuration parameters
``` php
use Macmotp\Codegen;
use Macmotp\Codegen\Config;

$config = new Config();

$config->setCodeLength(8);
$config->prepend('ST');

$generator = new Codegen();

echo $generator->withConfig($config)->generate('Company Name');
// (string) 'STCMPYNM'

// Will generate different results if called sequentially
echo $generator->generate('Company Name');
echo $generator->generate('Company Name');
echo $generator->generate('Company Name');
// (string) 'STCMPNNM'
// (string) 'STCMPPYN'
// (string) 'STCMPANM'
```
_Once the possibilities are running low due to lack of letters from the source, it automatically applies random letters._
_Please note that this package does not guarantee uniqueness on its results._

#### List of methods for configuration
- `setCodeLength(int $length)`: length of the output string code;
- `prepend(string $prepend)`: prepend a string;
- `append(string $append)`: append a string;

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
