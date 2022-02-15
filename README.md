Utility library to turn country names into ISO two-letter codes.
this library is the PHP clone of the python library: alephdata/countrynames/

# Installation 

## Clone the git repo:
```bash 
$ git clone https://github.com/ClickSend/integration-library-country-names.git
```
# Example:

In your php file, paste this code. <br/>
Make sure to edit the `integration-library-country-names` directory to  match your file structure.<br/>
Make sure the directory `integration-library-country-names/lib/data` is writable, for caching purpose.
```php
<?php 

require_once 'integration-library-country-names/lib/CountryNames.php';

use CountryNames\CountryNames;

var_dump('DE' == CountryNames::to_code('Germany'));
var_dump('DE' == CountryNames::to_code('Bundesrepublik Deutschland'));
var_dump('DE' == CountryNames::to_code('Bundesrepublik Deutschlan', $fuzzy=true));
var_dump('DE' == CountryNames::to_code('DE'));
var_dump('DEU' == CountryNames::to_code_3('Germany'));

```


you're all setup!
To use the library as a composer package, see below.
<hr/>


## To use it as a Composer Package:
First clone the repo to vendor directory in your project, then run composer autoload-dump command
```bash 
$ git clone https://github.com/ClickSend/integration-library-country-names.git   your-project-path/vendor/integration-library-country-names
```
```bash
$ composer autoload-dump
```

```php
<?php 

require_once 'vendor/autoload.php';

use CountryNames\CountryNames;

var_dump('DE' == CountryNames::to_code('Germany'));
var_dump('DE' == CountryNames::to_code('Bundesrepublik Deutschland'));
var_dump('DE' == CountryNames::to_code('Bundesrepublik Deutschlan', $fuzzy=true));
var_dump('DE' == CountryNames::to_code('DE'));
var_dump('DEU' == CountryNames::to_code_3('Germany'));

```
