With this package you can change textContent of HTML tag. 

# install using composer
``` 
composer require vendor/countrynames
```
# Examples:
```php
<?php 

require_once 'vendor/autoload.php';

use CountryNames\CountryNames;

assert_options(ASSERT_CALLBACK, 'assert_handler');

assert ('DE' == CountryNames::to_code('Germany'));
assert ('DE' == CountryNames::to_code('Bundesrepublik Deutschland'));
assert ('DE' == CountryNames::to_code('Bundesrepublik Deutschlan', $fuzzy=true));
assert ('DE' == CountryNames::to_code('DE'));
assert ('DEU' == CountryNames::to_code_3('Germany'));



function my_assert_handler($file, $line, $code)
{
    echo "Assertion Failed:
        File '$file'\n
        Line '$line'\n
        Code '$code'\n";
}