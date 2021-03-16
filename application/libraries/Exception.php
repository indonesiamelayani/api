<?php

class AccessException extends Exception {
	/* Properties */
	protected $code = "403";
	protected $message = "Access Forbidden for this service";

	// Redefine the exception so message isn't optional
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message !== null ? $message : $this->message, $code !== 0 ? $this->code : $code, $previous);
    }

    public function getErrorCode() {
    	return strval($this->code);
    }

    public function getErrorMessage() {
    	return $this->message;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class ParameterException extends Exception {
	/* Properties */
	protected $code = "01";
	protected $message = "Parameter tidak sesuai";

	// Redefine the exception so message isn't optional
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message !== null ? $message : $this->message, $code !== 0 ? $this->code : $code, $previous);
    }

    public function getErrorCode() {
    	return $this->code;
    }

    public function getErrorMessage() {
    	return $this->message;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class DataNotFoundException extends Exception {
	/* Properties */
	protected $code = "02";
	protected $message = "Data tidak lengkap";

	// Redefine the exception so message isn't optional
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message !== null ? $message : $this->message, $code !== 0 ? $this->code : $code, $previous);
    }

    public function getErrorCode() {
    	return $this->code;
    }

    public function getErrorMessage() {
    	return $this->message;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class InvalidRuleException extends Exception {
    /* Properties */
    protected $code = "03";
    protected $message = "Tidak memenuhi persyaratan";

    // Redefine the exception so message isn't optional
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message !== null ? $message : $this->message, $code !== 0 ? $this->code : $code, $previous);
    }

    public function getErrorCode() {
        return $this->code;
    }

    public function getErrorMessage() {
        return $this->message;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class ThirdPartyServiceException extends Exception {
	/* Properties */
	protected $code = "04";
	protected $message = "Service Pihak Ketiga error";

	// Redefine the exception so message isn't optional
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message !== null ? $message : $this->message, $code !== 0 ? $this->code : $code, $previous);
    }

    public function getErrorCode() {
    	return $this->code;
    }

    public function getErrorMessage() {
    	return $this->message;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

?>