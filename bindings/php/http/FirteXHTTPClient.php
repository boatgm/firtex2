<?php
require_once(dirname(__FILE__) . '/../FirteXException.php');
require_once(dirname(__FILE__) . '/FirteXHttpResponse.php');
require_once(dirname(__FILE__) . '/FirteXHttpTransport.php');
require_once(dirname(__FILE__) . '/../FirteXQueryResult.php');


class FirteXHTTPClient {
    
    /**
     * string
     * Server host name or IP address
     */
	protected $_host;
    
    /**
     * int
     * Server port
     */
    protected $_port;
    
    /**
     * string
     * Request URI, constructed from $_host and $_port
     */
    protected $_requestURI;

    /**
     * int
     * Timeout for http request
     */
    protected $_timeout;

	/**
	 * HTTP Transport implementation
	 */
	protected $_httpTransport = false;

    /**
     * Is square brackets are escaped
     */
    protected $_isSquareBracketsEscaped = true;

    // Default timeout for http request (15 seconds)
    const DEFAULT_TIMEOUT = 15;

	/**
	 * Constructor.
	 *
	 * @param string $host
	 * @param string $port
	 * @param FirteXHttpTransport $httpTransport
	 */
	public function __construct($host = 'localhost', $port = 8180, $httpTransport = false)
	{
        $this->_timeout = self::DEFAULT_TIMEOUT;
		$this->setHost($host);
		$this->setPort($port);
        $this->_requestURI = $host . ':' . strval($port) . '/';

		if ($httpTransport)
		{
			$this->setHttpTransport($httpTransport);
		}
        // Square brackets are escaped in 5.1.3
		$this->_isSquareBracketsEscaped = version_compare(phpversion(), '5.1.3', '>=');
	}

	/**
	 * Returns the set host
	 *
	 * @return string
	 */
	public function getHost()
	{
		return $this->_host;
	}

	/**
	 * Set the server host 
	 *
	 * @param string $host
	 *
	 * @throws FirteXInvalidArgumentException if the host parameter is empty
	 */
	public function setHost($host)
	{
		if (empty($host))
		{
			throw new FirteXInvalidArgumentException('Host parameter is empty');
		}
		else
		{
			$this->_host = $host;
		}
	}

	/**
	 * Get the port
	 *
	 * @return integer
	 */
	public function getPort()
	{
		return $this->_port;
	}

	/**
	 * Set the port. 
	 *
	 * @param integer $port
	 *
	 * @throws FirteXInvalidArgumentException if the port parameter is empty
	 */
	public function setPort($port)
	{
		$port = (int) $port;

		if ($port <= 0)
		{
			throw new FirteXInvalidArgumentException('Port is not a valid port number');
		}
		else
		{
			$this->_port = $port;
		}
	}

	/**
	 * Get the current configured HTTP Transport
	 *
	 * @return FirteXHttpTransport
	 */
	public function getHttpTransport()
	{
		if ($this->_httpTransport === false)
		{
			require_once(dirname(__FILE__) . '/FirteXFileGetContentsHttpTransport.php');

			$this->_httpTransport = new FirteXFileGetContentsHttpTransport($this->_timeout, 
                                                                           FirteXHttpTransport::METHOD_GET);
		}

		return $this->_httpTransport;
	}

	/**
	 * Set the HTTP Transport implemenation that will be used for all HTTP requests
	 *
	 * @param FirteXHttpTransport_Interface
	 */
	public function setHttpTransport(FirteXHttpTransport $httpTransport)
	{
		$this->_httpTransport = $httpTransport;
	}

	/**
	 * Get the current default timeout
	 *
	 * @return float in seconds
	 *
	 * @deprecated Use the getTimeout method on the HTTP transport implementation
	 */
	public function getTimeout()
	{
		return $this->_timeout;
	}

	/**
	 * Set the default timeout for all calls that aren't passed a specific timeout
	 *
	 * @param float $timeout Timeout value in seconds
	 */
	public function setTimeout($timeout)
	{
        $this->_timeout = $timeout;
		$this->getHttpTransport()->setTimeout($timeout);
	}

    /**
     * Set http method
     */
    public function setHttpMethod($method = FirteXHttpTransport::METHOD_GET) 
    {
        $this->getHttpTransport()->setMethod($method);
    }

    /**
     * Get http method
     */
    public function getHttpMethod() 
    {
        return $this->getHttpTransport()->getMethod();
    }
    
	/**
	 * Simple search API
	 *
	 * @param string $query The raw query string (see FirteX documentation)
	 * @param int $offset The starting offset for result documents
	 * @param int $limit The maximum number of result documents to return
	 * @param array $params key / value pairs for other query parameters (see FirteX documentation)
     *
	 * @return FirteXQueryResult
	 *
	 * @throws FirteXHttpTransportException if an error occurs during the service call
	 * @throws FirteXInvalidArgumentException if an invalid HTTP method is used
	 */
	public function search($query, $sortBy, $filter, $offset = 0, $limit = 10, $fieldSelect, ) {
		if (!is_null($params)) {
			if (!is_array($params)) {
				throw new FirteXInvalidArgumentException("\$params must be a valid array or null");
			}
		} else {
			$params = array();
		}
		
		$parameter['query'] = $query;
		$parameter['parameter'] = 'start:'. $offset .',top:'. $limit;
        foreach( $params AS $clause_name => $clause_value ) {
            foreach( $clause_value AS $key => $value) {
                if ( isset( $parameter[$clause_name] ) ) {
                    $parameter[$clause_name] .= ',';
                } else {
                    $parameter[$clause_name] = '';
                }
                if ($value != '') {
                    $parameter[$clause_name] .= $key .':'. $value;
                } else {
                    $parameter[$clause_name] .= $key;
                }
            }
        }

        return $this->sendAndReceive($this->buildQueryString('search?', $parameter));
	}

	public function search($query, $offset = 0, $limit = 10, $params = array()) {
		if (!is_null($params)) {
			if (!is_array($params)) {
				throw new FirteXInvalidArgumentException("\$params must be a valid array or null");
			}
		} else {
			$params = array();
		}
		
		$parameter['query'] = $query;
		$parameter['parameter'] = 'start:'. $offset .',top:'. $limit;
        foreach( $params AS $clause_name => $clause_value ) {
            foreach( $clause_value AS $key => $value) {
                if ( isset( $parameter[$clause_name] ) ) {
                    $parameter[$clause_name] .= ',';
                } else {
                    $parameter[$clause_name] = '';
                }
                if ($value != '') {
                    $parameter[$clause_name] .= $key .':'. $value;
                } else {
                    $parameter[$clause_name] .= $key;
                }
            }
        }

        return $this->sendAndReceive($this->buildQueryString('search?', $parameter));
	}

    /**
     * Send echo command to firtex server 
     *
     * @return bool true if the server is accessible
     */
    function ping() {
        $httpTransport = $this->getHttpTransport();
		$httpResponse = $httpTransport->sendAndReceive($this->_requestURI, 'echo?ping');
        return ($httpResponse->getStatusCode() == 200) ? true : false;
    }

	protected function buildQueryString($prefix, $params) {
        if ($this->getHttpTransport()->getMethod() == FirteXHttpTransport::METHOD_GET) {
            if ($this->_isSquareBracketsEscaped) {
                $queryString = http_build_query($params, null, ';');
                return $prefix . preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $queryString);
            } else {
                $queryString = http_build_query($params, null, ';');
                return $prefix . preg_replace('/\\[(?:[0-9]|[1-9][0-9]+)\\]=/', '=', $queryString);
            }
        } else {
            return $prefix . $this->array_implode('=', ';', $params);
		}
	}

	/**
	 * Send request to server and receive response
	 *
	 * @param string $queryStr
	 * @return FirteXQueryResult
	 *
	 * @throws FirteXHttpTransportException if a non 200 response status is returned
	 */
	protected function sendAndReceive($queryStr)
	{
		$httpTransport = $this->getHttpTransport();
		$httpResponse = $httpTransport->sendAndReceive($this->_requestURI, $queryStr);
        if ($httpResponse->getStatusCode() == 200) {
            return new FirteXQueryResult($httpResponse->getBody());
        } else {
            $result = new FirteXQueryResult(null);
            $result->setError($httpResponse->getStatusMessage());
            return $result;
        }
	}

    /**
     * Implode an array with the key and value pair giving
     * a glue, a separator between pairs and the array
     * to implode.
     * @param string $glue The glue between key and value
     * @param string $separator Separator between pairs
     * @param array $array The array to implode
     * @return string The imploded array
     */
    private function array_implode( $glue, $separator, $array ) {
        if ( ! is_array( $array ) ) return $array;
        $string = array();
        foreach ( $array as $key => $val ) {
            if ( is_array( $val ) )
                $val = implode( ',', $val );
            $string[] = "{$key}{$glue}{$val}";
            
        }
        return implode( $separator, $string );
    }
}