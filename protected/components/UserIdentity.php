<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	const ERROR_EMAIL_INVALIDO=1;
	const ERROR_STATUS_SUSPENDIDO=2;
	const ERROR_STATUS_RETIRADO=3;
	
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		/*
		if (strpos($this->username,"@")) {
			$user=Usuarios::model()->findByAttributes(array('p.PERS_EMAIL'=>$this->username));
		} else {
			$user=Usuarios::model()->findByAttributes(array('USUA_USUARIO'=>$this->username));
		}
		
		if($user===null)
			if (strpos($this->username,"@")) {
				$this->errorCode=self::ERROR_EMAIL_INVALIDO;
			} else {
				$this->errorCode=self::ERROR_USERNAME_INVALID;
			}*/
		

		$user=Usuarios::model()->findByAttributes(array('USUA_USUARIO'=>$this->username));
		
		if($user===null)
			/*if (strpos($this->username,"@")) {
				$this->errorCode=self::ERROR_EMAIL_INVALIDO;
			} else {
				$this->errorCode=self::ERROR_USERNAME_INVALID;
			}
			*/
		$this->errorCode=self::ERROR_USERNAME_INVALID;		
        elseif(!$user->validatePassword($this->password)){
			$this->errorCode=self::ERROR_PASSWORD_INVALID;	
		}elseif($user->USES_ID==2)
			$this->errorCode=self::ERROR_STATUS_SUSPENDIDO;
		elseif($user->USES_ID===3)
			$this->errorCode=self::ERROR_STATUS_RETIRADO;
		else{
				
			$this->_id=$user->USUA_ID;
			$this->username=$user->USUA_USUARIO;
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}
}