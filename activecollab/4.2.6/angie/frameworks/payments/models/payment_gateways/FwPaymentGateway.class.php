<?php

  /**
   * Framework level payment gateway instance implementation
   *
   * @package angie.frameworks.payments
   * @subpackage models
   */
  abstract class FwPaymentGateway extends BasePaymentGateway implements IRoutingContext  {
    
    /**
     * Payment gateway type
     * 
     * @var string
     */
    var $payment_gateway_type;

    /**
     * Accepted countries
     *
     * @var array
     */
    var $countries = array(
      array('name' => 'AFGHANISTAN', 'value' => 'AF'),
      array('name' => 'ALAND ISLANDS', 'value' => 'AX'),
      array('name' => 'ALBANIA', 'value' => 'AL'),
      array('name' => 'ALGERIA', 'value' => 'DZ'),
      array('name' => 'AMERICAN SAMOA', 'value' => 'AS'),
      array('name' => 'ANDORRA', 'value' => 'AD'),
      array('name' => 'ANGOLA', 'value' => 'AO'),
      array('name' => 'ANGUILLA', 'value' => 'AI'),
      array('name' => 'ANTARCTICA', 'value' => 'AQ'),
      array('name' => 'ANTIGUA AND BARBUDA', 'value' => 'AG'),
      array('name' => 'ARGENTINA', 'value' => 'AR'),
      array('name' => 'ARMENIA', 'value' => 'AM'),
      array('name' => 'ARUBA', 'value' => 'AW'),
      array('name' => 'AUSTRALIA', 'value' => 'AU'),
      array('name' => 'AUSTRIA', 'value' => 'AT'),
      array('name' => 'AZERBAIJAN', 'value' => 'AZ'),
      array('name' => 'BAHAMAS', 'value' => 'BS'),
      array('name' => 'BAHRAIN', 'value' => 'BH'),
      array('name' => 'BANGLADESH', 'value' => 'BD'),
      array('name' => 'BARBADOS', 'value' => 'BB'),
      array('name' => 'BELARUS', 'value' => 'BY'),
      array('name' => 'BELGIUM', 'value' => 'BE'),
      array('name' => 'BELIZE', 'value' => 'BZ'),
      array('name' => 'BENIN', 'value' => 'BJ'),
      array('name' => 'BERMUDA', 'value' => 'BM'),
      array('name' => 'BHUTAN', 'value' => 'BT'),
      array('name' => 'BOLIVIA', 'value' => 'BO'),
      array('name' => 'BOSNIA AND HERZEGOVINA', 'value' => 'BA'),
      array('name' => 'BOTSWANA', 'value' => 'BW'),
      array('name' => 'BOUVET ISLAND', 'value' => 'BV'),
      array('name' => 'BRAZIL', 'value' => 'BR'),
      array('name' => 'BRITISH INDIAN OCEAN TERRITORY', 'value' => 'IO'),
      array('name' => 'BRUNEI DARUSSALAM', 'value' => 'BN'),
      array('name' => 'BULGARIA', 'value' => 'BG'),
      array('name' => 'BURKINA FASO', 'value' => 'BF'),
      array('name' => 'BURUNDI', 'value' => 'BI'),
      array('name' => 'CAMBODIA', 'value' => 'KH'),
      array('name' => 'CAMEROON', 'value' => 'CM'),
      array('name' => 'CANADA', 'value' => 'CA'),
      array('name' => 'CAPE VERDE', 'value' => 'CV'),
      array('name' => 'CAYMAN ISLANDS', 'value' => 'KY'),
      array('name' => 'CENTRAL AFRICAN REPUBLIC', 'value' => 'CF'),
      array('name' => 'CHAD', 'value' => 'TD'),
      array('name' => 'CHILE', 'value' => 'CL'),
      array('name' => 'CHINA', 'value' => 'CN'),
      array('name' => 'CHRISTMAS ISLAND', 'value' => 'CX'),
      array('name' => 'COCOS (KEELING) ISLANDS', 'value' => 'CC'),
      array('name' => 'COLOMBIA', 'value' => 'CO'),
      array('name' => 'COMOROS', 'value' => 'KM'),
      array('name' => 'CONGO', 'value' => 'CG'),
      array('name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF', 'value' => 'CD'),
      array('name' => 'COOK ISLANDS', 'value' => 'CK'),
      array('name' => 'COSTA RICA', 'value' => 'CR'),
      array('name' => 'COTE D\'IVOIRE', 'value' => 'CI'),
      array('name' => 'CROATIA', 'value' => 'HR'),
      array('name' => 'CUBA', 'value' => 'CU'),
      array('name' => 'CYPRUS', 'value' => 'CY'),
      array('name' => 'CZECH REPUBLIC', 'value' => 'CZ'),
      array('name' => 'DENMARK', 'value' => 'DK'),
      array('name' => 'DJIBOUTI', 'value' => 'DJ'),
      array('name' => 'DOMINICA', 'value' => 'DM'),
      array('name' => 'DOMINICAN REPUBLIC', 'value' => 'DO'),
      array('name' => 'ECUADOR', 'value' => 'EC'),
      array('name' => 'EGYPT', 'value' => 'EG'),
      array('name' => 'EL SALVADOR', 'value' => 'SV'),
      array('name' => 'EQUATORIAL GUINEA', 'value' => 'GQ'),
      array('name' => 'ERITREA', 'value' => 'ER'),
      array('name' => 'ESTONIA', 'value' => 'EE'),
      array('name' => 'ETHIOPIA', 'value' => 'ET'),
      array('name' => 'FALKLAND ISLANDS (MALVINAS)', 'value' => 'FK'),
      array('name' => 'FAROE ISLANDS', 'value' => 'FO'),
      array('name' => 'FIJI', 'value' => 'FJ'),
      array('name' => 'FINLAND', 'value' => 'FI'),
      array('name' => 'FRANCE', 'value' => 'FR'),
      array('name' => 'FRENCH GUIANA', 'value' => 'GF'),
      array('name' => 'FRENCH POLYNESIA', 'value' => 'PF'),
      array('name' => 'FRENCH SOUTHERN TERRITORIES', 'value' => 'TF'),
      array('name' => 'GABON', 'value' => 'GA'),
      array('name' => 'GAMBIA', 'value' => 'GM'),
      array('name' => 'GEORGIA', 'value' => 'GE'),
      array('name' => 'GERMANY', 'value' => 'DE'),
      array('name' => 'GHANA', 'value' => 'GH'),
      array('name' => 'GIBRALTAR', 'value' => 'GI'),
      array('name' => 'GREECE', 'value' => 'GR'),
      array('name' => 'GREENLAND', 'value' => 'GL'),
      array('name' => 'GRENADA', 'value' => 'GD'),
      array('name' => 'GUADELOUPE', 'value' => 'GP'),
      array('name' => 'GUAM', 'value' => 'GU'),
      array('name' => 'GUATEMALA', 'value' => 'GT'),
      array('name' => 'GUERNSEY', 'value' => 'GG'),
      array('name' => 'GUINEA', 'value' => 'GN'),
      array('name' => 'GUINEA-BISSAU', 'value' => 'GW'),
      array('name' => 'GUYANA', 'value' => 'GY'),
      array('name' => 'HAITI', 'value' => 'HT'),
      array('name' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'value' => 'HM'),
      array('name' => 'HOLY SEE (VATICAN CITY STATE)', 'value' => 'VA'),
      array('name' => 'HONDURAS', 'value' => 'HN'),
      array('name' => 'HONG KONG', 'value' => 'HK'),
      array('name' => 'HUNGARY', 'value' => 'HU'),
      array('name' => 'ICELAND', 'value' => 'IS'),
      array('name' => 'INDIA', 'value' => 'IN'),
      array('name' => 'INDONESIA', 'value' => 'ID'),
      array('name' => 'IRAN, ISLAMIC REPUBLIC OF', 'value' => 'IR'),
      array('name' => 'IRAQ', 'value' => 'IQ'),
      array('name' => 'IRELAND', 'value' => 'IE'),
      array('name' => 'ISLE OF MAN', 'value' => 'IM'),
      array('name' => 'ISRAEL', 'value' => 'IL'),
      array('name' => 'ITALY', 'value' => 'IT'),
      array('name' => 'JAMAICA', 'value' => 'JM'),
      array('name' => 'JAPAN', 'value' => 'JP'),
      array('name' => 'JERSEY', 'value' => 'JE'),
      array('name' => 'JORDAN', 'value' => 'JO'),
      array('name' => 'KAZAKHSTAN', 'value' => 'KZ'),
      array('name' => 'KENYA', 'value' => 'KE'),
      array('name' => 'KIRIBATI', 'value' => 'KI'),
      array('name' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'value' => 'KP'),
      array('name' => 'KOREA, REPUBLIC OF', 'value' => 'KR'),
      array('name' => 'KUWAIT', 'value' => 'KW'),
      array('name' => 'KYRGYZSTAN', 'value' => 'KG'),
      array('name' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'value' => 'LA'),
      array('name' => 'LATVIA', 'value' => 'LV'),
      array('name' => 'LEBANON', 'value' => 'LB'),
      array('name' => 'LESOTHO', 'value' => 'LS'),
      array('name' => 'LIBYAN ARAB JAMAHIRIYA', 'value' => 'LY'),
      array('name' => 'LIECHTENSTEIN', 'value' => 'LI'),
      array('name' => 'LITHUANIA', 'value' => 'LT'),
      array('name' => 'LUXEMBOURG', 'value' => 'LU'),
      array('name' => 'MACAO', 'value' => 'MO'),
      array('name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'value' => 'MK'),
      array('name' => 'MADAGASCAR', 'value' => 'MG'),
      array('name' => 'MALAWI', 'value' => 'MW'),
      array('name' => 'MALAYSIA', 'value' => 'MY'),
      array('name' => 'MALDIVES', 'value' => 'MV'),
      array('name' => 'MALI', 'value' => 'ML'),
      array('name' => 'MALTA', 'value' => 'MT'),
      array('name' => 'MARSHALL ISLANDS', 'value' => 'MH'),
      array('name' => 'MARTINIQUE', 'value' => 'MQ'),
      array('name' => 'MAURITANIA', 'value' => 'MR'),
      array('name' => 'MAURITIUS', 'value' => 'MU'),
      array('name' => 'MAYOTTE', 'value' => 'YT'),
      array('name' => 'MEXICO', 'value' => 'MX'),
      array('name' => 'MICRONESIA, FEDERATED STATES OF', 'value' => 'FM'),
      array('name' => 'MOLDOVA, REPUBLIC OF', 'value' => 'MD'),
      array('name' => 'MONACO', 'value' => 'MC'),
      array('name' => 'MONGOLIA', 'value' => 'MN'),
      array('name' => 'MONTSERRAT', 'value' => 'MS'),
      array('name' => 'MOROCCO', 'value' => 'MA'),
      array('name' => 'MOZAMBIQUE', 'value' => 'MZ'),
      array('name' => 'MYANMAR', 'value' => 'MM'),
      array('name' => 'NAMIBIA', 'value' => 'NA'),
      array('name' => 'NAURU', 'value' => 'NR'),
      array('name' => 'NEPAL', 'value' => 'NP'),
      array('name' => 'NETHERLANDS', 'value' => 'NL'),
      array('name' => 'NETHERLANDS ANTILLES', 'value' => 'AN'),
      array('name' => 'NEW CALEDONIA', 'value' => 'NC'),
      array('name' => 'NEW ZEALAND', 'value' => 'NZ'),
      array('name' => 'NICARAGUA', 'value' => 'NI'),
      array('name' => 'NIGER', 'value' => 'NE'),
      array('name' => 'NIGERIA', 'value' => 'NG'),
      array('name' => 'NIUE', 'value' => 'NU'),
      array('name' => 'NORFOLK ISLAND', 'value' => 'NF'),
      array('name' => 'NORTHERN MARIANA ISLANDS', 'value' => 'MP'),
      array('name' => 'NORWAY', 'value' => 'NO'),
      array('name' => 'OMAN', 'value' => 'OM'),
      array('name' => 'PAKISTAN', 'value' => 'PK'),
      array('name' => 'PALAU', 'value' => 'PW'),
      array('name' => 'PALESTINIAN TERRITORY, OCCUPIED', 'value' => 'PS'),
      array('name' => 'PANAMA', 'value' => 'PA'),
      array('name' => 'PAPUA NEW GUINEA', 'value' => 'PG'),
      array('name' => 'PARAGUAY', 'value' => 'PY'),
      array('name' => 'PERU', 'value' => 'PE'),
      array('name' => 'PHILIPPINES', 'value' => 'PH'),
      array('name' => 'PITCAIRN', 'value' => 'PN'),
      array('name' => 'POLAND', 'value' => 'PL'),
      array('name' => 'PORTUGAL', 'value' => 'PT'),
      array('name' => 'PUERTO RICO', 'value' => 'PR'),
      array('name' => 'QATAR', 'value' => 'QA'),
      array('name' => 'REUNION', 'value' => 'RE'),
      array('name' => 'ROMANIA', 'value' => 'RO'),
      array('name' => 'RUSSIAN FEDERATION', 'value' => 'RU'),
      array('name' => 'RWANDA', 'value' => 'RW'),
      array('name' => 'SAINT HELENA', 'value' => 'SH'),
      array('name' => 'SAINT KITTS AND NEVIS', 'value' => 'KN'),
      array('name' => 'SAINT LUCIA', 'value' => 'LC'),
      array('name' => 'SAINT PIERRE AND MIQUELON', 'value' => 'PM'),
      array('name' => 'SAINT VINCENT AND THE GRENADINES', 'value' => 'VC'),
      array('name' => 'SAMOA', 'value' => 'WS'),
      array('name' => 'SAN MARINO', 'value' => 'SM'),
      array('name' => 'SAO TOME AND PRINCIPE', 'value' => 'ST'),
      array('name' => 'SAUDI ARABIA', 'value' => 'SA'),
      array('name' => 'SENEGAL', 'value' => 'SN'),
      array('name' => 'SERBIA AND MONTENEGRO', 'value' => 'CS'),
      array('name' => 'SEYCHELLES', 'value' => 'SC'),
      array('name' => 'SIERRA LEONE', 'value' => 'SL'),
      array('name' => 'SINGAPORE', 'value' => 'SG'),
      array('name' => 'SLOVAKIA', 'value' => 'SK'),
      array('name' => 'SLOVENIA', 'value' => 'SI'),
      array('name' => 'SOLOMON ISLANDS', 'value' => 'SB'),
      array('name' => 'SOMALIA', 'value' => 'SO'),
      array('name' => 'SOUTH AFRICA', 'value' => 'ZA'),
      array('name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'value' => 'GS'),
      array('name' => 'SPAIN', 'value' => 'ES'),
      array('name' => 'SRI LANKA', 'value' => 'LK'),
      array('name' => 'SUDAN', 'value' => 'SD'),
      array('name' => 'SURINAME', 'value' => 'SR'),
      array('name' => 'SVALBARD AND JAN MAYEN', 'value' => 'SJ'),
      array('name' => 'SWAZILAND', 'value' => 'SZ'),
      array('name' => 'SWEDEN', 'value' => 'SE'),
      array('name' => 'SWITZERLAND', 'value' => 'CH'),
      array('name' => 'SYRIAN ARAB REPUBLIC', 'value' => 'SY'),
      array('name' => 'TAIWAN, PROVINCE OF CHINA', 'value' => 'TW'),
      array('name' => 'TAJIKISTAN', 'value' => 'TJ'),
      array('name' => 'TANZANIA, UNITED REPUBLIC OF', 'value' => 'TZ'),
      array('name' => 'THAILAND', 'value' => 'TH'),
      array('name' => 'TIMOR-LESTE', 'value' => 'TL'),
      array('name' => 'TOGO', 'value' => 'TG'),
      array('name' => 'TOKELAU', 'value' => 'TK'),
      array('name' => 'TONGA', 'value' => 'TO'),
      array('name' => 'TRINIDAD AND TOBAGO', 'value' => 'TT'),
      array('name' => 'TUNISIA', 'value' => 'TN'),
      array('name' => 'TURKEY', 'value' => 'TR'),
      array('name' => 'TURKMENISTAN', 'value' => 'TM'),
      array('name' => 'TURKS AND CAICOS ISLANDS', 'value' => 'TC'),
      array('name' => 'TUVALU', 'value' => 'TV'),
      array('name' => 'UGANDA', 'value' => 'TM'),
      array('name' => 'UKRAINE', 'value' => 'UA'),
      array('name' => 'UNITED ARAB EMIRATES', 'value' => 'AE'),
      array('name' => 'UNITED KINGDOM', 'value' => 'GB'),
      array('name' => 'UNITED STATES', 'value' => 'US'),
      array('name' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'value' => 'UM'),
      array('name' => 'URUGUAY', 'value' => 'UY'),
      array('name' => 'UZBEKISTAN', 'value' => 'UZ'),
      array('name' => 'VANUATU', 'value' => 'VU'),
      array('name' => 'VENEZUELA', 'value' => 'VE'),
      array('name' => 'VIETNAM', 'value' => 'VN'),
      array('name' => 'VIRGIN ISLANDS, BRITISH', 'value' => 'VG'),
      array('name' => 'VIRGIN ISLANDS, U.S.', 'value' => 'VI'),
      array('name' => 'WALLIS AND FUTUNA', 'value' => 'WF'),
      array('name' => 'WESTERN SAHARA', 'value' => 'EH'),
      array('name' => 'YEMEN', 'value' => 'YE'),
      array('name' => 'ZAMBIA', 'value' => 'ZM'),
      array('name' => 'ZIMBABWE', 'value' => 'ZW'),

    );
    
    /**
     * Return list of supported currencies
     * 
     * @return Array
     */
    function getSupportedCurrencies() {
      return $this->supported_currencies;
    }//getSupportedCurrencies

    /**
     * Return supported currencies table
     *
     * @return string
     */
    function getSupportedCurrenciesTable() {
      $additional = "<table cellpadding='0' cellspacing='0' class='payment_gateway_additiona_info'>";
      $additional .= "<tr><th colspan='2'>" . lang('Supported currencies') . "</th>";

      foreach($this->getSupportedCurrencies() as $code => $name) {
        $additional .= "<tr><td>". $name ."</td><td align='right'>".$code."</td></tr>";
      } //foreach
      $additional .= "</table>";

      return $additional;
    } //getSupportedCurrenciesTable

    /**
     * Get payment gateway icon path
     */
    function getIconPath() {
      return $this->icon_path;
    } //getIconPath
   
    /**
     * Return payment_gateway_type
     * 
     */
    function getPaymentGatewayVerbouseType() {
      return $this->payment_gateway_type ? $this->getPaymentGatewayVerbouseType() : lang('Unknown');
    }//getPaymentGatewayVerbouseType
    
    /**
     * Return true if this expense category is used for estimate
     * 
     * @return boolean
     */
    function isUsed() {
      return boolval(Payments::findByGateway($this));
    }//isUsed
    
   /**
  	 * Get payment gateway name 
  	 * 
  	 */
  	function getName() {
  	  return $this->getAdditionalProperty('name');
  	} //getName
  	
  	/**
  	 * Set payment gateway name
  	 * 
  	 * @param $value
  	 */
  	function setName($value) {
  	  $this->setAdditionalProperty('name',$value);
  	} //setName
  	
  	/**
  	 * Get payment gateway api_username 
  	 */
  	function getApiUsername() {
  	  return $this->getAdditionalProperty('api_username');
  	} //getApiUsername
  	
  	/**
  	 * Set payment gateway api_username
  	 * 
  	 * @param $value
  	 */
  	function setAPIUsername($value) {
  	  $this->setAdditionalProperty('api_username',$value);
  	} //setAPIUsername
  	
  	
  	/**
  	 * Get payment gateway api_login_id 
  	 * 
  	 */
  	function getApiLoginId() {
  	  return $this->getAdditionalProperty('api_login_id');
  	} //getApiLoginName
  	
  	/**
  	 * Set payment gateway api_login_id
  	 * 
  	 * @param $value
  	 */
  	function setApiLoginId($value) {
  	  $this->setAdditionalProperty('api_login_id',$value);
  	} //setApiLoginName
  	
  	/**
  	 * Get payment gateway transaction_id 
  	 * 
  	 */
  	function getTransactionId() {
  	  return $this->getAdditionalProperty('transaction_id');
  	} //getTransactionId
  	
  	/**
  	 * Set payment gateway transaction_id
  	 * 
  	 * @param $value
  	 */
  	function setTransactionId($value) {
  	  $this->setAdditionalProperty('transaction_id',$value);
  	} //setTransactionId
 	
  	/**
  	 * Get payment gateway api_password 
  	 */
  	function getApiPassword() {
  	  return $this->getAdditionalProperty('api_password');
  	} //getApiUsername
  	
  	/**
  	 * Set payment gateway api_password
  	 * 
  	 * @param $value
  	 */
  	function setAPIPassword($value) {
  	  $this->setAdditionalProperty('api_password',$value);
  	} //setAPIUsername
  	
  	/**
  	 * Get payment gateway api_signature 
  	 */
  	function getApiSignature() {
  	  return $this->getAdditionalProperty('api_signature');
  	} //getApiSignature
  	
  	/**
  	 * Set payment gateway api_signature
  	 * 
  	 * @param $value
  	 */
  	function setAPISignature($value) {
  	  $this->setAdditionalProperty('api_signature',$value);
  	} //setAPISignature
  	
  	/**
  	 * Get payment gateway go_live 
  	 */
  	function getGoLive() {
  	  return $this->getAdditionalProperty('go_live');
  	} //getGoLive
  	
  	/**
  	 * Set payment gateway go_live
  	 * 
  	 * @param $value
  	 */
  	function setGoLive($value) {
  	  $this->setAdditionalProperty('go_live',$value);
  	} //setGoLive
  	  	
  	/**
  	 * Check if payment gateway is default payment gateway
  	 * 
  	 * @return boolean
  	 */
  	function isDefault() {
  	  if($this->getIsDefault() == 1) {
  	    return true;
  	  } else {
  	    return false;
  	  } //if
  	} //isDefault
  	
  	/**
  	 * Display or hide user form
  	 * 
  	 */
  	function showUserForm() {
  	  if($this->isDefault()) {
  	    return 'block';
  	  }//if
  	    return 'none';
  	}//if
  	
  	/**
  	 * Returns main page url
  	 * 
  	 */
  	function getMainPageUrl() {
  	  return Router::assemble('payment_gateways_admin_section');
  	} //getMainPageUrl
  	
  	/**
  	 * Returns allow payments url
  	 * 
  	 */
  	function getAllowPaymentsUrl() {
  	  return Router::assemble('payment_gateways_allow_payments');
  	} //getAllowPaymentsUrl
  	
  	/**
  	 * Returns allow_payments_for_invoice_url
  	 * 
  	 */
  	function getAllowPaymentsForInvoiceUrl() {
  	  return Router::assemble('payment_gateways_allow_payments_for_invoice');
  	} //getAllowPartialPaymentsUrl
  	
  	/**
     * Get enforce settings URL
     */
    function getEnforceSettingsUrl() {
      return Router::assemble('payment_gateways_enforce_settings');
    }//getEnforceSettingsUrl
  	
  	/**
     * Return set as default gateway
     *
     * @param void
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('admin_payment_set_as_default', array('payment_gateway_id' => $this->getId()));
    } // getSetAsDefaultPaymentGatewayUrl

    /**
     * Return enable url
     *
     * @param void
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble('admin_payment_enable', array('payment_gateway_id' => $this->getId()));
    } // getEnableUrl
    
     /**
     * Return disable url
     *
     * @param void
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble('admin_payment_disable', array('payment_gateway_id' => $this->getId()));
    } // getDisableUrl
    
    /**
     * Add new payment gateway URL
     *
     * @param void
     * @return string
     */
    function getAddUrl() {
      return Router::assemble('admin_payment_gateway_add');
    } // getAddUrl
    
    /**
     * Update payment gateway URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('admin_payment_gateway_edit', array('payment_gateway_id' => $this->getId()));
    } // getEditUrl
    
    /**
     * View payment gateway URL
     *
     * @return string
     */
    function getViewUrl() {
      return Router::assemble('admin_payment_gateway_view', array('payment_gateway_id' => $this->getId()));
    } // getViewUrl
    
    /**
     * Return delete payment gateway URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('admin_payment_gateway_delete', array('payment_gateway_id' => $this->getId()));
    } // getDeleteUrl
    
  	/**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['is_default'] = $this->getIsDefault();
      $result['is_enabled'] = $this->getIsEnabled();
      $result['is_used'] = $this->isUsed();
      
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      $result['urls']['disable'] = $this->getDisableUrl();
      $result['urls']['enable'] = $this->getEnableUrl();
      $result['urls']['delete'] = $this->getDeleteUrl();
      $result['urls']['edit'] = $this->getEditUrl();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'incoming_email_admin_mailbox';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('mailbox_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->getName()) {
          $errors->addError(lang('Payment gateway name is required'), 'name');
      } // if
      
      if($this instanceof PaypalGateway) {
        
        if(!$this->getApiUsername()) {
          $errors->addError(lang('API username is required'), 'api_username');
        } // if
        
        if(!$this->getApiPassword()) {
          $errors->addError(lang('API password is required'), 'api_password');
        } // if
      }//if
      
      if($this instanceof AuthorizeGateway) {
        if(!$this->getApiLoginId()) {
          $errors->addError(lang('API login ID is required'), 'api_login_id');
        } // if
        
        if(!$this->getTransactionId()) {
          $errors->addError(lang('Transaction ID is required'), 'transaction_id');
        } // if
      }//if
      
    } // validate

  }