<?php

  /**
   * Default globalization adapter
   *
   * @package angie.library.globalization
   */
  class GlobalizationAdapter {
    
    /**
     * Return language for given user
     * 
     * @param IUser $user
     * @return Language
     */
    function getLanguage($user = null) {
      if(empty($user)) {
        $user = Authentication::getLoggedUser();
      } // if
      
      if($user instanceof User) {
        if(ConfigOptions::hasValueFor('language', $user)) {
          $language_id = (integer) ConfigOptions::getValueFor('language', $user);
          
          if($language_id && $language_id != $this->getDefaultLanguage()->getId()) {
            $language = Languages::findById($language_id);
            if($language instanceof Language) {
              return $language;
            } // if
          } // if
        } // if
      } // if
      
      return $this->getDefaultLanguage();
    } // getLanguage
    
    /**
     * Default language instance
     *
     * @var Language
     */
    private $default_language = false;
    
    /**
     * Return default language
     * 
     * @return Language
     */
    function getDefaultLanguage() {
      if($this->default_language === false) {
        $this->default_language = Languages::findDefault();
      } // if
      
      return $this->default_language;
    } // getDefaultLanguage
    
    protected $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    
    /**
     * Return all letters in english alphabet
     * 
     * @return array;
     */
    function getAlphabet() {
    	return $this->alphabet;
    } // getAlphabet
    
    /**
     * Offset in seconds - cities map
     *
     * @var array
     */
    protected $timezones = array(
      -43200 => array('International Date Line West'),
      -39600 => array('Midway Island', 'Samoa'),
      -36000 => array('Hawaii'),
      -32400 => array('Alaska'),
      -28800 => array('Pacific Time (US & Canada)'),
      -25200 => array('Mountain Time (US & Canada)'),
      -21600 => array('Central Time (US & Canada)'),
      -18000 => array('Eastern Time (US & Canada)'),
      -16200 => array('Caracas'),
      -14400 => array('Atlantic Time (Canada)'),
      -12600 => array('Newfoundland'),
      -10800 => array('Brasilia', 'Buenos Aires', 'Georgetown', 'Greenland'), 
       -7200 => array('Mid-Atlantic'),
       -3600 => array('Azores', 'Cape Verde Is.'),
           0 => array('Dublin', 'Edinburgh', 'Lisbon', 'London', 'Casablanca', 'Monrovia'),
        3600 => array('Berlin', 'Brussels', 'Copenhagen', 'Madrid', 'Paris', 'Rome', 'Warsaw'),
        7200 => array('Kaliningrad', 'South Africa'),
       10800 => array('Baghdad', 'Riyadh', 'Moscow', 'Nairobi'),
       12600 => array('Tehran'),
       14400 => array('Abu Dhabi', 'Muscat', 'Baku', 'Tbilisi', 'Yerevan'),
       16200 => array('Kabul'),
       18000 => array('Ekaterinburg', 'Islamabad', 'Karachi', 'Tashkent'),
       19800 => array('Chennai', 'Kolkata', 'Mumbai', 'New Delhi'),
       20700 => array('Kathmandu'),
       21600 => array('Astana', 'Dhaka', 'Sri Jayawardenepura', 'Almaty', 'Novosibirsk'),
       23400 => array('Rangoon'),
       25200 => array('Bangkok', 'Hanoi', 'Jakarta', 'Krasnoyarsk'),
       28800 => array('Beijing', 'Hong Kong', 'Perth', 'Singapore', 'Taipei'),
       32400 => array('Seoul', 'Osaka', 'Sapporo', 'Tokyo', 'Yakutsk'),
       34200 => array('Darwin', 'Adelaide'),
       36000 => array('Melbourne', 'Papua New Guinea', 'Sydney', 'Vladivostok'),
       39600 => array('Magadan', 'Solomon Is.', 'New Caledonia'),
       43200 => array('Fiji', 'Kamchatka', 'Marshall Is.', 'Auckland', 'Wellington'),
       46800 => array('Nuku\'alofa'),
    ); // array
    
    /**
     * Return list of all available timezones
     *
     * @return array
     */
    function getTimezones() {
      return $this->timezones;
    } // getTimezones
    
    /**
     * Return list of all available currencies
     *
     * @return array
     */
    function getCurrencies() {
      
    } // getCurrencies
    
    protected $countries = array(
      'AF' => 'Afghanistan', 
      'AX' => 'Öland Islands', 
      'AL' => 'Albania', 
      'DZ' => 'Algeria', 
      'AS' => 'American Samoa', 
      'AD' => 'Andorra', 
      'AO' => 'Angola', 
      'AI' => 'Anguilla', 
      'AQ' => 'Antarctica', 
      'AG' => 'Antigua And Barbuda', 
      'AR' => 'Argentina', 
      'AM' => 'Armenia', 
      'AW' => 'Aruba', 
      'AU' => 'Australia', 
      'AT' => 'Austria', 
      'AZ' => 'Azerbaijan', 
      'BS' => 'Bahamas', 
      'BH' => 'Bahrain', 
      'BD' => 'Bangladesh', 
      'BB' => 'Barbados', 
      'BY' => 'Belarus', 
      'BE' => 'Belgium', 
      'BZ' => 'Belize', 
      'BJ' => 'Benin', 
      'BM' => 'Bermuda', 
      'BT' => 'Bhutan', 
      'BO' => 'Bolivia', 
      'BA' => 'Bosnia And Herzegovina', 
      'BW' => 'Botswana', 
      'BV' => 'Bouvet Island', 
      'BR' => 'Brazil', 
      'IO' => 'British Indian Ocean Territory', 
      'BN' => 'Brunei Darussalam', 
      'BG' => 'Bulgaria', 
      'BF' => 'Burkina Faso', 
      'BI' => 'Burundi', 
      'KH' => 'Cambodia', 
      'CM' => 'Cameroon', 
      'CA' => 'Canada', 
      'CV' => 'Cape Verde', 
      'KY' => 'Cayman Islands', 
      'CF' => 'Central African Republic', 
      'TD' => 'Chad', 
      'CL' => 'Chile', 
      'CN' => 'China', 
      'CX' => 'Christmas Island', 
      'CC' => 'Cocos (keeling) Islands', 
      'CO' => 'Colombia', 
      'KM' => 'Comoros', 
      'CG' => 'Congo', 
      'CD' => 'Congo, The Democratic Republic Of The', 
      'CK' => 'Cook Islands', 
      'CR' => 'Costa Rica', 
      'CI' => 'Cote D\'ivoire', 
      'HR' => 'Croatia', 
      'CU' => 'Cuba', 
      'CY' => 'Cyprus', 
      'CZ' => 'Czech Republic', 
      'DK' => 'Denmark', 
      'DJ' => 'Djibouti', 
      'DM' => 'Dominica', 
      'DO' => 'Dominican Republic', 
      'EC' => 'Ecuador', 
      'EG' => 'Egypt', 
      'SV' => 'El Salvador', 
      'GQ' => 'Equatorial Guinea', 
      'ER' => 'Eritrea', 
      'EE' => 'Estonia', 
      'ET' => 'Ethiopia', 
      'FK' => 'Falkland Islands (malvinas)', 
      'FO' => 'Faroe Islands', 
      'FJ' => 'Fiji', 
      'FI' => 'Finland', 
      'FR' => 'France', 
      'GF' => 'French Guiana', 
      'PF' => 'French Polynesia', 
      'TF' => 'French Southern Territories', 
      'GA' => 'Gabon', 
      'GM' => 'Gambia', 
      'GE' => 'Georgia', 
      'DE' => 'Germany', 
      'GH' => 'Ghana', 
      'GI' => 'Gibraltar', 
      'GR' => 'Greece', 
      'GL' => 'Greenland', 
      'GD' => 'Grenada', 
      'GP' => 'Guadeloupe', 
      'GU' => 'Guam', 
      'GT' => 'Guatemala', 
      'GG' => 'Guernsey', 
      'GN' => 'Guinea', 
      'GW' => 'Guinea-bissau', 
      'GY' => 'Guyana', 
      'HT' => 'Haiti', 
      'HM' => 'Heard Island And Mcdonald Islands', 
      'VA' => 'Holy See (vatican City State)', 
      'HN' => 'Honduras', 
      'HK' => 'Hong Kong', 
      'HU' => 'Hungary', 
      'IS' => 'Iceland', 
      'IN' => 'India', 
      'ID' => 'Indonesia', 
      'IR' => 'Iran, Islamic Republic Of', 
      'IQ' => 'Iraq', 
      'IE' => 'Ireland', 
      'IM' => 'Isle Of Man', 
      'IL' => 'Israel', 
      'IT' => 'Italy', 
      'JM' => 'Jamaica', 
      'JP' => 'Japan', 
      'JE' => 'Jersey', 
      'JO' => 'Jordan', 
      'KZ' => 'Kazakhstan', 
      'KE' => 'Kenya', 
      'KI' => 'Kiribati', 
      'KP' => 'Korea, Democratic People\'s Republic Of', 
      'KR' => 'Korea, Republic Of', 
      'KW' => 'Kuwait', 
      'KG' => 'Kyrgyzstan', 
      'LA' => 'Lao People\'s Democratic Republic', 
      'LV' => 'Latvia', 
      'LB' => 'Lebanon', 
      'LS' => 'Lesotho', 
      'LR' => 'Liberia', 
      'LY' => 'Libyan Arab Jamahiriya', 
      'LI' => 'Liechtenstein', 
      'LT' => 'Lithuania', 
      'LU' => 'Luxembourg', 
      'MO' => 'Macao', 
      'MK' => 'Macedonia, The Former Yugoslav Republic Of', 
      'MG' => 'Madagascar', 
      'MW' => 'Malawi', 
      'MY' => 'Malaysia', 
      'MV' => 'Maldives', 
      'ML' => 'Mali', 
      'MT' => 'Malta', 
      'MH' => 'Marshall Islands', 
      'MQ' => 'Martinique', 
      'MR' => 'Mauritania', 
      'MU' => 'Mauritius', 
      'YT' => 'Mayotte', 
      'MX' => 'Mexico', 
      'FM' => 'Micronesia, Federated States Of', 
      'MD' => 'Moldova, Republic Of', 
      'MC' => 'Monaco', 
      'MN' => 'Mongolia', 
      'ME' => 'Montenegro', 
      'MS' => 'Montserrat', 
      'MA' => 'Morocco', 
      'MZ' => 'Mozambique', 
      'MM' => 'Myanmar', 
      'NA' => 'Namibia', 
      'NR' => 'Nauru', 
      'NP' => 'Nepal', 
      'NL' => 'Netherlands', 
      'AN' => 'Netherlands Antilles', 
      'NC' => 'New Caledonia', 
      'NZ' => 'New Zealand', 
      'NI' => 'Nicaragua', 
      'NE' => 'Niger', 
      'NG' => 'Nigeria', 
      'NU' => 'Niue', 
      'NF' => 'Norfolk Island', 
      'MP' => 'Northern Mariana Islands', 
      'NO' => 'Norway', 
      'OM' => 'Oman', 
      'PK' => 'Pakistan', 
      'PW' => 'Palau', 
      'PS' => 'Palestinian Territory, Occupied', 
      'PA' => 'Panama', 
      'PG' => 'Papua New Guinea', 
      'PY' => 'Paraguay', 
      'PE' => 'Peru', 
      'PH' => 'Philippines', 
      'PN' => 'Pitcairn', 
      'PL' => 'Poland', 
      'PT' => 'Portugal', 
      'PR' => 'Puerto Rico', 
      'QA' => 'Qatar', 
      'RE' => 'Reunion', 
      'RO' => 'Romania', 
      'RU' => 'Russian Federation', 
      'RW' => 'Rwanda', 
      'SH' => 'Saint Helena', 
      'KN' => 'Saint Kitts And Nevis', 
      'LC' => 'Saint Lucia', 
      'PM' => 'Saint Pierre And Miquelon', 
      'VC' => 'Saint Vincent And The Grenadines', 
      'WS' => 'Samoa', 
      'SM' => 'San Marino', 
      'ST' => 'Sao Tome And Principe', 
      'SA' => 'Saudi Arabia', 
      'SN' => 'Senegal', 
      'RS' => 'Serbia', 
      'SC' => 'Seychelles', 
      'SL' => 'Sierra Leone', 
      'SG' => 'Singapore', 
      'SK' => 'Slovakia', 
      'SI' => 'Slovenia', 
      'SB' => 'Solomon Islands', 
      'SO' => 'Somalia', 
      'ZA' => 'South Africa', 
      'GS' => 'South Georgia And The South Sandwich Islands', 
      'ES' => 'Spain', 
      'LK' => 'Sri Lanka', 
      'SD' => 'Sudan', 
      'SR' => 'Suriname', 
      'SJ' => 'Svalbard And Jan Mayen', 
      'SZ' => 'Swaziland', 
      'SE' => 'Sweden', 
      'CH' => 'Switzerland', 
      'SY' => 'Syrian Arab Republic', 
      'TW' => 'Taiwan, Province Of China', 
      'TJ' => 'Tajikistan', 
      'TZ' => 'Tanzania, United Republic Of', 
      'TH' => 'Thailand', 
      'TL' => 'Timor-leste', 
      'TG' => 'Togo', 
      'TK' => 'Tokelau', 
      'TO' => 'Tonga', 
      'TT' => 'Trinidad And Tobago', 
      'TN' => 'Tunisia', 
      'TR' => 'Turkey', 
      'TM' => 'Turkmenistan', 
      'TC' => 'Turks And Caicos Islands', 
      'TV' => 'Tuvalu', 
      'UG' => 'Uganda', 
      'UA' => 'Ukraine', 
      'AE' => 'United Arab Emirates', 
      'GB' => 'United Kingdom', 
      'US' => 'United States', 
      'UM' => 'United States Minor Outlying Islands', 
      'UY' => 'Uruguay', 
      'UZ' => 'Uzbekistan', 
      'VU' => 'Vanuatu', 
      'VE' => 'Venezuela', 
      'VN' => 'Viet Nam', 
      'VG' => 'Virgin Islands, British', 
      'VI' => 'Virgin Islands, U.s.', 
      'WF' => 'Wallis And Futuna', 
      'EH' => 'Western Sahara', 
      'YE' => 'Yemen', 
      'ZM' => 'Zambia', 
      'ZW' => 'Zimbabwe', 
    ); // array
    
    /**
     * Return list of countries
     *
     * @return array
     */
    function getCountries() {
      return $this->countries;
    } // getCountries
    
    /**
     * List of states
     *
     * @var array
     */
    private $states = array(
    
      // Canada states
      'CA' => array(
        'AB' => 'Alberta',
        'BC' => 'British Columbia',
        'MB' => 'Manitoba',
        'NB' => 'New Brunswick',
        'NF' => 'Newfoundland',
        'NT' => 'Northwest Territories',
        'NS' => 'Nova Scotia',
        'ON' => 'Ontario',
        'PE' => 'Prince Edward Island',
        'QC' => 'Quebec',
        'SK' => 'Saskatchewan',
        'YT' => 'Yukon',
      ),
      
      // US states
      'US' => array (
    		'AK' => 'Alaska',
    		'AL' => 'Alabama',
    		'AR' => 'Arkansas',
    		'AZ' => 'Arizona',
    		'CA' => 'California',
    		'CO' => 'Colorado',
    		'CT' => 'Connecticut',
    		'DC' => 'District of Columbia',
    		'DE' => 'Delaware',
    		'FL' => 'Florida',
    		'GA' => 'Georgia',
    		'HI' => 'Hawaii',
    		'IA' => 'Iowa',
    		'ID' => 'Idaho',
    		'IL' => 'Illinois',
    		'IN' => 'Indiana',
    		'KS' => 'Kansas',
    		'KY' => 'Kentucky',
    		'LA' => 'Louisiana',
    		'MA' => 'Massachusetts',
    		'MD' => 'Maryland',
    		'ME' => 'Maine',
    		'MI' => 'Michigan',
    		'MN' => 'Minnesota',
    		'MO' => 'Missouri',
    		'MS' => 'Mississippi',
    		'MT' => 'Montana',
    		'NC' => 'North Carolina',
    		'ND' => 'North Dakota',
    		'NE' => 'Nebraska',
    		'NH' => 'New Hampshire',
    		'NJ' => 'New Jersey',
    		'NM' => 'New Mexico',
    		'NV' => 'Nevada',
    		'NY' => 'New York',
    		'OH' => 'Ohio',
    		'OK' => 'Oklahoma',
    		'OR' => 'Oregon',
    		'PA' => 'Pennsylvania',
    		'PR' => 'Puerto Rico',
    		'RI' => 'Rhode Island',
    		'SC' => 'South Carolina',
    		'SD' => 'South Dakota',
    		'TN' => 'Tennessee',
    		'TX' => 'Texas',
    		'UT' => 'Utah',
    		'VA' => 'Virginia',
    		'VT' => 'Vermont',
    		'WA' => 'Washington',
    		'WI' => 'Wisconsin',
    		'WV' => 'West Virginia',
    		'WY' => 'Wyoming',
  		)
    );
    
    /**
     * Return a list of states or provides for a given $country_code
     *
     * @param string $country_code
     * @return array
     */
    function getStates($country_code) {
      return isset($this->states[$country_code]) ? $this->states[$country_code] : null;
    } // getStates
    
    /**
     * Return array of month names
     *
     * @param Language $language
     * @param boolean $short
     * @return array
     */
    function getMonthNames($language = null, $short = false) {
      return array(
        1 => $short ? lang('Jan', null, null, $language) : lang('January', null, null, $language),
        2 => $short ? lang('Feb', null, null, $language) : lang('February', null, null, $language),
        3 => $short ? lang('Mar', null, null, $language) : lang('March', null, null, $language),
        4 => $short ? lang('Apr', null, null, $language) : lang('April', null, null, $language),
        5 => $short ? lang('May', null, null, $language) : lang('May', null, null, $language),
        6 => $short ? lang('Jun', null, null, $language) : lang('June', null, null, $language),
        7 => $short ? lang('Jul', null, null, $language) : lang('July', null, null, $language),
        8 => $short ? lang('Aug', null, null, $language) : lang('August', null, null, $language),
        9 => $short ? lang('Sep', null, null, $language) : lang('September', null, null, $language),
        10 => $short ? lang('Oct', null, null, $language) : lang('October', null, null, $language),
        11 => $short ? lang('Nov', null, null, $language) : lang('November', null, null, $language),
        12 => $short ? lang('Dec', null, null, $language) : lang('December', null, null, $language)
      );
    } // getMonthNames
    
    /**
     * Return day names
     *
     * @param Language $language
     * @param boolean $short
     * @return array
     */
    function getDayNames($language = null, $short = false) {
      return array(
        0 => $short ? lang('Sun', null, null, $language) : lang('Sunday', null, null, $language),
        1 => $short ? lang('Mon', null, null, $language) : lang('Monday', null, null, $language),
        2 => $short ? lang('Tue', null, null, $language) : lang('Tuesday', null, null, $language),
        3 => $short ? lang('Wed', null, null, $language) : lang('Wednesday', null, null, $language),
        4 => $short ? lang('Thu', null, null, $language) : lang('Thursday', null, null, $language),
        5 => $short ? lang('Fri', null, null, $language) : lang('Friday', null, null, $language),
        6 => $short ? lang('Sat', null, null, $language) : lang('Saturday', null, null, $language),
      );
    } // getDayNames
    
    /**
     * Returns true if $date is workday
     *
     * @param DateValue $date
     * @return boolean
     */
    function isWorkday(DateValue $date) {
      return in_array($date->getWeekday(), $this->getWorkDays());
    } // isWorkday
    
        /**
     * get array of work days in a week
     * 
     * @return array
     */
    function getWorkDays() {
      return ConfigOptions::getValue('time_workdays');
    } // getWorkDays
    
    /**
     * Get array of days off
     * 
     * @return array
     */
    function getDaysOff() {
    	return DayOffs::find();
    } // getDaysOff
    
    /**
     * Get Days off mapped for JS
     * 
     * @return array
     */
    function getDaysOffMappedForJs() {
    	return array();
    } // getDaysOffMappedForJs

    /**
     * Returns true if $date is day off
     *
     * @param DateValue $date
     * @return boolean
     */
    function isDayOff(DateValue $date) {
      $day_offs = DayOffs::find();
      foreach ($day_offs as $day_off) {
        /** @var DayOff $day_off */
        $day_off_date = new DateValue(strtotime($day_off->getEventDate()));
        if (($date->getDay() == $day_off_date->getDay()) && ($date->getMonth() == $day_off_date->getMonth())) {
          if (($date->getYear() == $day_off_date->getYear()) || $day_off->getRepeatYearly() === 1) {
            return true;
          } //if
        } //if
      } //foreach
      return false;
    } // isDayOff

    /**
     * Cached decimal separator for logged user
     *
     * @var string
     */
    private $logged_user_decimal_separator = false;

    /**
     * Cached thousands separator for logged user
     *
     * @var string
     */
    private $logged_user_thousands_separator = false;

    /**
     * Get number sepaarators for logged users
     *
     * @return string
     */
    function getNumberSeparators() {
      if ($this->logged_user_decimal_separator == false) {
        $logged_user = Authentication::getLoggedUser();
        if ($logged_user instanceof User) {
          $language = $logged_user->getLanguage();
        } else {
          $language = Languages::findDefault();
        } // if

        $this->logged_user_decimal_separator = $language instanceof Language ? $language->getDecimalSeparator() : '.';
        $this->logged_user_thousands_separator = $language instanceof Language ? $language->getThousandsSeparator() : '';
      } // if

      return array($this->logged_user_decimal_separator, $this->logged_user_thousands_separator);
    } // getNumberSeparators

    /**
     * Format number
     *
     * @param float $number
     * @param Language $language
     * @param int $decimal_spaces
     * @param boolean $trim_zeros
     * @return string
     */
    function formatNumber($number, Language $language = null, $decimal_spaces = 2, $trim_zeros = false) {
      if ($language instanceof Language) {
        $decimal_separator = $language->getDecimalSeparator();
        $thousands_separator = $language->getThousandsSeparator();
      } else {
        list($decimal_separator, $thousands_separator) = $this->getNumberSeparators();
      } // if

      $formatted_number = number_format($number, $decimal_spaces, $decimal_separator, $thousands_separator);

      if($trim_zeros) {
        $formatted_number = rtrim(trim($formatted_number, 0), $decimal_separator);

        if($formatted_number && substr($formatted_number, 0, 1) == $decimal_separator) {
          $formatted_number = '0' . $formatted_number;
        } // if
      } // if

      return $formatted_number;
    } // formatNumber

    /**
     * Format money
     *
     * @param float $number
     * @param Currency $currency
     * @param Language $language
     * @param Boolean $include_code
     * @param Boolean $round
     * @return string
     */
    function formatMoney($number, Currency $currency = null, Language $language = null, $include_code = false, $round = false) {
      // get default currency
      if (!($currency instanceof Currency)) {
        $currency = Currencies::getDefault();
      } // if

      // if we need to round money
      if ($round && $currency->getDecimalRounding()) {
        $number = Currencies::roundDecimal($number, $currency);
      } // if

      $formatted = $this->formatNumber($number, $language, $currency->getDecimalSpaces());

      if ($include_code) {
        if (strtoupper($currency->getCode()) == 'USD') {
          $formatted = $currency->getCode() . ' ' . $formatted;
        } else if ($currency->getCode() == '$') {
          $formatted = $currency->getCode() . $formatted;
        } else {
          $formatted = $formatted . ' ' . $currency->getCode();
        } // if
      } // if

      return $formatted;
    } // formatMoney
    
  }