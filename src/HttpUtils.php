<?php

class HttpUtils
{
	/**
	 * Http status code
	 * We'll update these as we need them
	 * @var array
	 */
	private static $httpStatusCodes = array(
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		500 => 'Internal Server Error',
		);

	/**
	 * Send a http head according to status code
	 * @param  integer $code
	 * @return void
	 */
	public static function sendHeader($code)
	{
		if (!array_key_exists($code, self::$httpStatusCodes)) {
			$code = 500;
		}
		header(sprintf('%s %d %s', 
			$_SERVER['SERVER_PROTOCOL'],
			$code,
			self::$httpStatusCodes[$code]
			), true, 500);
	}

	/**
	 * Check if an ip is in an array of ip ranges
	 * @param  string $requestIp
	 * @param  array $range
	 * @return bool
	 */
	public static function ipIsInRange($requestIp, array $range)
	{
		foreach ($range as $ip) {
			if (HttpUtils::checkIp4($requestIp, $ip) === true) {
				return true;
			}
		}
		return false;
	}

	/**
   * Compares two IPv4 addresses.
   * Copied from Symony HttpFoundation
   * In case a subnet is given, it checks if it contains the request IP.
   *
   * @param string $requestIp IPv4 address to check
   * @param string $ip        IPv4 address or subnet in CIDR notation
   *
   * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet.
   */
  public static function checkIp4($requestIp, $ip)
  {
    if (false !== strpos($ip, '/')) {
      list($address, $netmask) = explode('/', $ip, 2);
      if ($netmask === '0') {
  	    // Ensure IP is valid - using ip2long below implicitly validates, but we need to do it manually here
        return filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
      }
      if ($netmask < 0 || $netmask > 32) {
    	  return false;
      }
      } else {
      	$address = $ip;
        $netmask = 32;
    }
    return 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
  }
}