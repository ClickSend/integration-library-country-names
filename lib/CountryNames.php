<?php 
namespace CountryNames;

class CountryNames
{
    /**
     * @property $data the static property that holds associative arrary countryname => code
     */
    public static $data = [];
    /**
     * @method _read_data will read and generate country list data using php generators
     */
    private static function _read_data()
    {

        $cache_dir = __DIR__  . '/data/cache.php';
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
        unset($data);
    }
    /**
     * @method _load_data will load data to self::$data property and/or cache the data
     */
    private static function _load_data()
    {
        if (self::$data)
            return;
        foreach (self::_read_data() as $code => $norm)
            self::$data[$norm] = $code;
        // cache the data 
        $cache_dir = __DIR__  . '/data/cache.php';
        if (! file_exists($cache_dir))
            file_put_contents($cache_dir, serialize(self::$data));
    }
    /**
     * @method _delete_cache this method will delete cache.php in data folder
     */
    public static function _delete_cache()
    {
        $cache_dir = __DIR__ . '/data/cache.php';
        if (file_exists($cache_dir))
            unlink($cache_dir);
    }
    /**
     * @method _normalize_name returns transliterated char to latin and lower case
     * @param $str Any the string that will be translitarated
     */
    public static function _normalize_name($str)
    {
        return transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $str);
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