<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use CountryNamesLibrary\CountryNames;

final class CountryToCodeTest extends TestCase
{
    public function testToCode(): void
    {
        echo phpversion();
        $this->assertEquals('DE', CountryNames::to_code('Germany'));
        $this->assertEquals('GB', CountryNames::to_code('UK'));
        $this->assertEquals('MK', CountryNames::to_code('North Macedonia'));
        $this->assertEquals(null, CountryNames::to_code('Nothing'));
    }

    public function testToCode3(): void
    {
        $this->assertEquals('DEU', CountryNames::to_code_3('Germany'));
        $this->assertEquals('GBR', CountryNames::to_code_3('UK'));
        $this->assertEquals(null, CountryNames::to_code_3('Nothing'));
    }
    public function testUnicode(): void
    {
        $this->assertEquals('RU', CountryNames::to_code('Российская Федерация'));
    }

    public function testFuzzyMatching()
    {
        $this->assertEquals('FK', CountryNames::to_code('Falklands Islands', $fuzzy=true));
        $this->assertEquals('DE', CountryNames::to_code('TGermany', $fuzzy=true));
        $this->assertEquals('PSE', CountryNames::to_code_3('State of Palestine', $fuzzy=true));
    }
    public function testNonStandardsCodes()
    {
        $this->assertEquals('EU', CountryNames::to_code('European Union'));
        $this->assertEquals('EUU', CountryNames::to_code_3('European Union'));
        $this->assertEquals('XK', CountryNames::to_code('Kosovo'));
        $this->assertEquals('XKX', CountryNames::to_code_3('Kosovo'));
    }
    public function testGB()
    {
        $this->assertEquals('GB-SCT', CountryNames::to_code('Scotland'));
        $this->assertEquals('GB-WLS', CountryNames::to_code_3('Wales'));
        $this->assertEquals('GB-NIR', CountryNames::to_code('Northern Ireland'));
        $this->assertEquals('GB-NIR', CountryNames::to_code('Northern Ireland', $fuzzy=true));

        $text = "United Kingdom of Great Britain and Northern Ireland";
        $this->assertEquals('GB', CountryNames::to_code($text));
        $text = "United Kingdom of Great Britain and Northern Ireland";
        $this->assertEquals('GB', CountryNames::to_code($text, $fuzzy=true));
    }
}
