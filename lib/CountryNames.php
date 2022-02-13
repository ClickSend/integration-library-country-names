<?php 
namespace CountryNames;

class CountryNames
{
    /**
     * @const UNICODE_CATEGORIES 
     * associative array, keys are the values returned from \IntlChar::charType method, so we map them and 
     * get the actual code, so to take action on it.
     */
    const UNICODE_CATEGORIES = [
        \IntlChar::CHAR_CATEGORY_UNASSIGNED => 'Cn',
        \IntlChar::CHAR_CATEGORY_UPPERCASE_LETTER => 'Lu',
        \IntlChar::CHAR_CATEGORY_LOWERCASE_LETTER => 'Ll',
        \IntlChar::CHAR_CATEGORY_TITLECASE_LETTER => 'Lt',
        \IntlChar::CHAR_CATEGORY_MODIFIER_LETTER => 'Lm',
        \IntlChar::CHAR_CATEGORY_OTHER_LETTER => 'Lo',
        \IntlChar::CHAR_CATEGORY_NON_SPACING_MARK => 'Mn',
        \IntlChar::CHAR_CATEGORY_ENCLOSING_MARK => 'Me',
        \IntlChar::CHAR_CATEGORY_COMBINING_SPACING_MARK => 'Mc',
        \IntlChar::CHAR_CATEGORY_DECIMAL_DIGIT_NUMBER => 'Nd',
        \IntlChar::CHAR_CATEGORY_LETTER_NUMBER => 'Nl',
        \IntlChar::CHAR_CATEGORY_OTHER_NUMBER => 'No',
        \IntlChar::CHAR_CATEGORY_SPACE_SEPARATOR => 'Zs',
        \IntlChar::CHAR_CATEGORY_LINE_SEPARATOR => 'Zl',
        \IntlChar::CHAR_CATEGORY_PARAGRAPH_SEPARATOR => 'Zp',
        \IntlChar::CHAR_CATEGORY_CONTROL_CHAR => 'Cc',
        \IntlChar::CHAR_CATEGORY_FORMAT_CHAR => 'Cf',
        \IntlChar::CHAR_CATEGORY_PRIVATE_USE_CHAR => 'Co',
        \IntlChar::CHAR_CATEGORY_SURROGATE => 'Cs',
        \IntlChar::CHAR_CATEGORY_DASH_PUNCTUATION => 'Pd',
        \IntlChar::CHAR_CATEGORY_START_PUNCTUATION => 'Ps',
        \IntlChar::CHAR_CATEGORY_END_PUNCTUATION => 'Pe',
        \IntlChar::CHAR_CATEGORY_CONNECTOR_PUNCTUATION => 'Pc',
        \IntlChar::CHAR_CATEGORY_OTHER_PUNCTUATION => 'Po',
        \IntlChar::CHAR_CATEGORY_MATH_SYMBOL => 'Sm',
        \IntlChar::CHAR_CATEGORY_CURRENCY_SYMBOL => 'Sc',
        \IntlChar::CHAR_CATEGORY_MODIFIER_SYMBOL => 'Sk',
        \IntlChar::CHAR_CATEGORY_OTHER_SYMBOL => 'So',
        \IntlChar::CHAR_CATEGORY_INITIAL_PUNCTUATION => 'Pi',
        \IntlChar::CHAR_CATEGORY_FINAL_PUNCTUATION => 'Pf'
    ];
    /**
     * @const WS white space
     */
    const WS = " ";
    /**
     * @const list of characters to be replaced in the _normalize_name by their unicodedata category/property
     */
    const UNICODE_REPLACEMENTS = [
        "Cc" => self::WS,
        "Cf" => '',
        "Cs" => '',
        "Co" => '',
        "Cn" => '',
        "Lm" => '',
        "Mn" => '',
        "Mc" => self::WS,
        "Me" => '',
        "No" => '',
        "Zs" => self::WS,
        "Zl" => self::WS,
        "Zp" => self::WS,
        "Pc" => self::WS,
        "Pd" => self::WS,
        "Ps" => self::WS,
        "Pe" => self::WS,
        "Pi" => self::WS,
        "Pf" => self::WS,
        "Po" => self::WS,
        "Sm" => self::WS,
        "Sc" => '',
        "Sk" => '',
        "So" => self::WS,
        "Zs" => self::WS,
        "Zl" => self::WS,
        "Zp" => self::WS,
    ];
    /**
     * @property $data the static property that holds associative array countryname => code
     */
    public static $data = [];
    /**
     * @property $cache_dir path to cached file
     */
    private static $cache_dir = __DIR__ . '/data/cache.php';
    /**
     * @method _read_data will read and generate country list data using php generators
     */
    private static function _read_data()
    {

        $cache_dir = self::$cache_dir;
        if (file_exists($cache_dir)) {
            // get data from cache
            $data = unserialize(file_get_contents($cache_dir));
            self::$data = $data;
            unset($data);
            return;
        }
        $data = file_get_contents(__DIR__ . '/data/countries.json');
        $data = json_decode($data, true);
        
        foreach ($data as $code => $names) {
            $code = trim( strtoupper($code) );
            $norm_code =  self::_normalize_name($code);
            
            if (! is_null($norm_code) && $norm_code != '')
                yield $code => $norm_code;
            foreach ($names as $name) {
                $norm_name = self::_normalize_name($name);
                if (! is_null($norm_name) && $norm_name != '')
                    yield $code => $norm_name;
            }
        }
    }
    /**
     * @method _load_data will load data to self::$data property and/or cache the data
     */
    public static function _load_data()
    {
        if (self::$data)
            return;
        foreach (self::_read_data() as $code => $norm)
            self::$data[$norm] = $code;
        // cache the data 
        $cache_dir = self::$cache_dir;
        if (! file_exists($cache_dir))
            file_put_contents($cache_dir, serialize(self::$data));
    }
    /**
     * @method _delete_cache this method will delete cache.php in data folder
     */
    public static function _delete_cache()
    {
        $cache_dir = self::$cache_dir;
        if (file_exists($cache_dir))
            unlink($cache_dir);
    }
    /**
     * @method _normalize_name returns transliterated char to latin and lower case
     * @param $str Any the string that will be translitarated
     */
    public static function _normalize_name($str)
    {
        $clean = trim(transliterator_transliterate('Any-Latin; Lower()', $str));

        // replace anything that's not real text
        $clean = normalizer_normalize($clean, \Normalizer::FORM_KD);
        if (! is_string($clean))
            return null;
        $len = mb_strlen($clean, 'UTF-8');
        $characters = [];
        for ($i = 0; $i < $len; $i++):
            $char = mb_substr($clean, $i, 1, 'UTF-8');
            $cat = self::UNICODE_CATEGORIES[\IntlChar::charType($char)];
            $replacement = isset(self::UNICODE_REPLACEMENTS[$cat]) ? self::UNICODE_REPLACEMENTS[$cat]: $char;
            if ($replacement)
                $characters[] = $replacement;
        endfor;
        $clean = implode('', $characters);
        return trim(preg_replace('/\s+/', ' ', $clean));
    }
    /**
     * @method _fuzzy_search is for matching the closest spelling mistake in a countryname
     * @param $name Any the name of country to find country code for
     * @return null|string
     */
    private static function _fuzzy_search($name)
    {

        $best_code = null;
        $best_distance = null;
        foreach (self::$data as $cand => $code):
            if (strlen($cand) <= 4)
                continue;
            $distance = levenshtein($cand, $name);
            
            if (is_null($best_distance) || $distance < $best_distance) {
                $best_distance = $distance;
                $best_code = $code;
            }
        endforeach;
       
        if (is_null($best_distance) || $best_distance > (strlen($name) * 0.15) )
            return null;
        
        return $best_code;
    }
    /**
     * @method _mappings return the 3 letter country code  from 2 letter country code
     * @param $code Any county code that will be mapped to it's respective 3 letter code
     * @return null|string
     */
    private static function _mappings($code)
    {
        $data = file_get_contents(__DIR__ . '/data/mappings.json');
        $mappings = json_decode($data, true);
        return isset($mappings[$code]) ? $mappings[$code]: null;
    }

    /**
     * @method to_code returns country code from country name
     * @param $countryName Any Name of country to find code for
     * @param $fuzzy boolean: if set to true, it will try matching with the closest spelling mistakes
     * @param $default Any  String will be return when country code is not found
     * @return null|string|Any
     */
    public static function to_code($countryName, $fuzzy = false, $default = null)
    {
        // load data
        if (! self::$data || ! is_array(self::$data))
            self::_load_data();
        
        // shortcut 
        if (is_string($countryName)): 
            $countryName =  trim(strtoupper($countryName));
            //  Check if the input is actually an ISO code:
            if (in_array($countryName, array_values(self::$data)))
                return $countryName;
        endif;

        //  Transliterate and clean up
        $name = self::_normalize_name($countryName);
        if (is_null($name) || ! $name || $name == '')
            return $default;
        
        # Direct look up
        $code = isset(self::$data[$name]) ? self::$data[$name]: null;
        if ($code == 'FAIL')
            return $default;
        
        // Find closest match with spelling mistakes
        if ((is_null($code) || ! $code) && $fuzzy) 
            $code = self::_fuzzy_search($name);
        
        return $code ? $code : $default;
    }
    /**
     * @method to_code_3 returns 3 letter country code from country name 
     * @param $countryName Any Name of country to find 3 code for
     * @param $fuzzy boolean: if set to true, it will try matching with the closest spelling mistakes
     */
    public static function to_code_3($countryName, $fuzzy = false)
    {
        $code = self::to_code($countryName, $fuzzy);
        
        if ($code && strlen($code) > 2) {
            return $code;
        } elseif ($code == null) {
            return $code;
        } else {
            return self::_mappings($code);
        }
    }
}