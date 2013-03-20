<?php

namespace devcookies;

use \InvalidArgumentException;

class SignatureGenerator 
{
	private $salt = "";
	private $ignoreInvalidValues = false;
	private $ignoreLevelDeeperThan = 2;
	
	/**
	 * @param string $salt
	 * @param string $ignoreUnsupportedValues
	 * @throws InvalidArgumentException
	 */
	public function __construct($salt, $ignoreUnsupportedValues = false)
	{
		if (!is_string($salt) || !$salt)
			throw new InvalidArgumentException("\$salt required to be non empty string!");
			
		if (!is_bool($ignoreUnsupportedValues))
			throw new InvalidArgumentException("\$ignoreUnsupportedValues required to be boolean!");
		
		$this->salt = $salt;
		$this->ignoreInvalidValues = $ignoreUnsupportedValues;
	}
	
	/**
	 * @param array $params
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public function assemble(array $params)
	{
		if (empty($params))
			throw new InvalidArgumentException("Empty params passed!");
		
		return sha1($this->parseArray($params, 1) . ";" . $this->salt);
	}
	
	private function parseArray(array $params, $level)
	{
		if ($level > $this->ignoreLevelDeeperThan)
			return "";
		
		$paramsToSign = array();
		
		ksort($params);
		
		foreach ($params as $key => $value) {
			$valueToAdd = "";
			
			switch (true) {
				case is_bool($value):
					$valueToAdd = $value ? "1" : "0";
					break;
					
				case is_scalar($value) && !is_resource($value):
					$valueToAdd = (string) $value;
					break;
					
				case empty($value):
					continue 2;
					
				case is_array($value):
					$valueToAdd = $this->parseArray($value, ++$level);
					break;
					
				default:
					if (!$this->ignoreInvalidValues)
						throw new InvalidArgumentException("Type of value for key: \"{$key}\" is not supported. Supported types are: boolean, string, array and null");
					continue 2;
			}
			
			if ($valueToAdd === "") continue;
			
			$paramsToSign[$key] = $key . ':' . $valueToAdd;
		}
		
		return implode(";", $paramsToSign);
	}
}