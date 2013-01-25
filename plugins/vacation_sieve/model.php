<?php

/*
 +-----------------------------------------------------------------------+
 | lib/rcube_vacation.php                                                |
 |                                                                       |
 | Copyright (C) 2009 Boris HUISGEN <bhuisgen@hbis.fr>                   |
 | Licensed under the GNU GPL                                            |
 +-----------------------------------------------------------------------+
 */

class model
{
	public $username = '';
	public $email = '';
	public $email_local = '';
	public $email_domain =  '';
	public $addressed_to =  null;
	public $send_from =  null;
	public $vacation_enable = false;
	public $vacation_start = 0;
	public $vacation_starttime = 12;
	public $vacation_end = 0;
	public $vacation_endtime = 12;
	public $append_subject = true;
	public $vacation_subject = 'Out of office';
	public $vacation_message = 'I am in Holidays...';
	public $every = 1;

	/**
	 * Constructor of the class.
	 */
	public function __construct()
	{
		$this->init();
	}
	
	/*
	 * Initialize the object.
	 */
	private function init()
	{
		$this->username = rcmail::get_instance()->user->get_username();
		
	    $parts = explode('@', $this->username);
	    if (count($parts) >= 2)
	    {
	       $this->email = $this->username;
	       $this->email_local = $parts[0];
	       $this->email_domain = $parts[1] ;
	    }
	}
	
	/*
	 * Gets the username.
	 *
	 * @return string the username.
	 */		
	public function get_username()
	{
		return $this->username;
	}
	
	/*
	 * Gets the full email of the user.
	 *
	 * @return string the email of the user.
	 */			
	public function get_email()
	{	    
	    return $this->email;
    }
	
	/*
	 * Gets the email local part of the user.
	 *
	 * @return string the email local part.
	 */		
	public function get_email_local()
	{    
	    return $this->email_local;
    }

	/*
	 * Gets the email domain of the user.
	 *
	 * @return string the email domain.
	 */			
	public function get_email_domain()
	{	    
	    return $this->email_domain;
    }

	/*
	 * Gets the destination email address(es)
	 *
	 * @return string the 'from' email.
	 */			
	public function get_addressed_to()
	{	    
	    return $this->addressed_to;
    }

	/*
	 * Gets the sending email address..
	 *
	 * @return string the 'from' email.
	 */			
	public function get_send_from()
	{	    
	    return $this->send_from;
    }

	/*
	 * Checks if the vacation is enabled.
	 *
	 * @return boolean TRUE if vacation is enabled; FALSE otherwise.
	 */
	public function is_vacation_enable ()
	{
		return $this->vacation_enable;
	}

	/*
	 * Gets the vacation start date.
	 *
	 * @returng int the timestamp of the start date.
	 */
	public function get_vacation_start()
	{
        if ( !$this->vacation_start )
            $this->vacation_start = time();

		return $this->vacation_start;
	}

	/*
	 * Gets the vacation start time.
	 *
	 * @returng int Time in 24h format.
	 */
	public function get_vacation_starttime()
	{
		return $this->vacation_starttime;
	}

	/*
	 * Gets the vacation end date.
	 *
	 * @returng int the timestamp of the end date.
	 */
	public function get_vacation_end()
	{
        if ( !$this->vacation_end )
            $this->vacation_end = 86400 + time();

		return $this->vacation_end;
	}

	/*
	 * Gets the vacation end time.
	 *
	 * @returng int Time in 24h format.
	 */
	public function get_vacation_endtime()
	{
		return $this->vacation_endtime;
	}
	
	/*
	 * Gets the vacation subject.
	 *
	 * @return string the vacation subject.
	 */
	public function get_vacation_subject()
	{
		return $this->vacation_subject;
	}

	/*
	 * Gets the append subject.
	 *
	 * @return bool the apppend subject.
	 */
	public function get_append_subject()
	{
		return $this->append_subject;
	}

	/*
	 * Gets the vacation message.
	 *
	 * @return string the vacation message.
	 */
	public function get_vacation_message()
	{
		return $this->vacation_message;
	}
	
	/*
	 * Checks if a copy in inbox must be keep when the vacation is enabled.
	 *
	 * @return boolean TRUE if a copy must be keeped; FALSE otherwise.
	 */
	public function is_vacation_keep_copy_in_inbox()
	{
		return $this->vacation_keepcopyininbox;
	}
	
	/*
	 * Gets the periodicity of the email sent
	 * 
	 * @return int the periodicity
	 */
	public function get_every()
	{
		return $this->every;
	}

	/*
	 * Sets the email of the user
	 *
	 * @param string $email the email.
	 */
	public function set_email($email)
	{
		$this->email = $email;
	}
	
	/*
	 * Sets the email local part of the user
	 *
	 * @param string $local the local part of the email.
	 */
	public function set_email_local($local)
	{
		$this->email_local = $local;
	}
	
	/*
	 * Sets the email domain part of the user
	 *
	 * @param string $local the domain part of the email.
	 */
	public function set_email_domain($domain)
	{
		$this->email_domain = $domain;
	}
	
	/*
	 * Sets the destination email address(es)
	 *
	 * @param string the 'from' email.
	 */			
	public function set_addressed_to($email)
	{	    
	    $this->addressed_to = $email;
    }

	/*
	 * Sets the sending email address..
	 *
	 * @param string the 'from' email.
	 */			
	public function set_send_from($email)
	{	    
	    $this->send_from = $email;
    }

	/*
	 * Enables or disables the vacation.
	 *
	 * @param boolean the flag.
	 */
	public function set_vacation_enable($flag)
	{
		$this->vacation_enable = $flag;
	}

	/*
	 * Sets the vacation start date.
	 *
	 * @param int the timestamp of the vacation start date.
	 */
	public function set_vacation_start ($date)
	{
		$this->vacation_start = $date;
	}

	/*
	 * Sets the vacation start time.
	 *
	 * @param int The time in 24h format.
	 */
	public function set_vacation_starttime ($time)
	{
		$this->vacation_starttime = $time;
	}

	/*
	 * Sets the vacation end date.
	 *
	 * @param int the timestamp of the vacation end date.
	 */
	public function set_vacation_end ($date)
	{
		$this->vacation_end = $date;
	}
	
	/*
	 * Sets the vacation end time.
	 *
	 * @param int The time in 24h format.
	 */
	public function set_vacation_endtime ($time)
	{
		$this->vacation_endtime = $time;
	}

	/*
	 * Sets the vacation subject.
	 *
	 * @param string $subject the vacation subject.
	 */
	public function set_vacation_subject($subject)
	{
		$this->vacation_subject = $subject;
	}

	/*
	 * Sets the append subject.
	 *
	 * @param bool $append Append the original subject to the subject
	 */
	public function set_append_subject($append)
	{
		$this->append_subject = $append;
	}

	/*
	 * Sets the vacation message.
	 *
	 * @param string $message the vacation message.
	 */
	public function set_vacation_message($message)
	{
		$this->vacation_message = $message;
	}
	
	/*
	 * Sets the vacation keep copy in inbox flag.
	 *
	 * @param boolean the flag.
	 */
	public function set_vacation_keep_copy_in_inbox($flag)
	{
		$this->vacation_keepcopyininbox = $flag;
	}
	
	/*
	 * Sets the periodicity of the auto answer
	 * 
	 * @param int $period the periodicity
	 */
	public function set_every($period)
	{
		$this->every = $period;
	}
}
