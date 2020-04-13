<?php

class ValidatePassword extends Validator 
{
    private $pass;
    
    public function __construct( $pass = null) 
    {
        $this->pass = $pass;        

        parent::__construct();
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function validate() 
    {
        if (strlen($this->pass) < 6 ) 
        {
            $this->setError('Password is too short');
            return;
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/',$this->pass )) 
        {
            $this->setError('Password contains invalid characters');
            return;
        }
        if (strlen($this->pass) > 20 ) 
        {
            $this->setError('Password is too long');
            return;
        }
    }
}
    