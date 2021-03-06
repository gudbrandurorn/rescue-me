<?php
use RescueMe\User;
use RescueMe\Locale; 
use RescueMe\Missing; 

$missing_modules = array();
if(!RescueMe\Module::exists("RescueMe\SMS\Provider"))
	$missing_modules[] = 'RescueMe\SMS\Provider';

#if(class_exists('\RescueMe\Missing'))
#	$missing[] = '\RescueMe\Missing';

if(sizeof($missing_modules) > 0) {
	$TWIG['error'] = array('header' => sizeof($missing_modules) > 1 
										? T_('Missing modules!') 
										: T_('Missing module!'),
						   'body' => sizeof($missing_modules) > 1 
						   				? T_('The system is missing following modules!') 
						   				: T_('The system is missing following module!'),
						   'data' => implode(', ', $missing_modules));
} else {
	if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $TWIG['data']	= $_POST;
        require_once(APP_PATH_INC.'common.inc.php');
        
		$operation = new RescueMe\Operation;
		$operation = $operation->add(
			'trace', 
			$_POST['m_name'], 
			$edit->id,
			$_POST['mb_mobile_country'], //"NO", 
			$_POST['mb_mobile']);

		if(!$operation) {
	        $TWIG['message']['header'] = T_('Could not initiate trace');
	        $TWIG['message']['body']   = T_('System error: could not initiate operation');
		} else {
			$missing = Missing::add(
				$_POST['m_name'], 
				$_POST['m_mobile_country'], 
				$_POST['m_mobile'], $operation->id);
			
			if($missing) {
				header("Location: ".ADMIN_URI.'missing/'.$operation->id);
				exit();
			}
	        $TWIG['message']['header'] = T_('Could not initiate trace');
	        $TWIG['message']['body']   =  RescueMe\DB::errno() ? 'DB Error: '. RescueMe\DB::error() : T_('Please try again');
	    }
    }
	$TWIG['countries'] = Locale::getCountryNames();
	
	// POSSIBLE BUG IF USER IS SET
	
	$TWIG['selected_country'] = isset($edit) ? Locale::getCountryCode($edit->mobile_country) : Locale::getCurrentCountryCode();
}

