<?php

    /**
     * File containing: SMS class
     * 
     * @copyright Copyright 2013 {@link http://www.discoos.org DISCO OS Foundation}  
     *
     * @since 13. June 2013
     * 
     * @author Kenneth Gulbrandsøy <kenneth@onevoice.no>
     */
    
    namespace RescueMe\SMS;
    

    /**
     * SMS class
     * 
     * @package 
     */
    class UMS implements Provider
    {
        const WDSL_URL = "https://secure.ums.no/soap/sms/1.6/?wsdl";
        
        /**
         * UMS account
         * @var array
         */
        private $account;
        
        /**
         * constructor for SMS
         *
         * @since 13. June 2013
         *
         */
        public function __construct($company='', $department='', $password='')
        {
            $this->account = $this->newConfig($company, $department, $password);
            
        }// __construct
        
        
        public function config()
        {
            return $this->account;
        }

        private function newConfig($company='', $department='', $password='')
        {
            return array
            (
                "fields" => array(
                    "company" => $company,
                    "department" => $department,
                    "password" => $password
                ),
                "required" => array(
                    "company", 
                    "department", 
                    "password"
                ),
                "labels" => array(
                    "company" => _("company"),
                    "department" => _("department"),
                    "password" => _("password")
                ),
            );
        }// newConfig
        
        public function send($country, $to, $from, $message)
        {
            try {
                
                $sms = array
                (
                    "from" => $from,
                    "text" => $message,
                    "schedule" => time()  // send immediately, to send in one hour use: time()+3600
                );

                $recipients = array($this->getInternationalPrefix().$country.$to);

                $settings = array
                (
                    "splitMessages" => true,
                    "splitFormat" => "(%d/%t)\\n",
                );

                $client = new \SoapClient(UMS::WDSL_URL);
                $refno = $client->doSendSMS($this->account["fields"], $sms, $recipients, $settings);
                return $refno;
                
            }
            catch(\Exception $e) 
            {
                return array(array('number' => $e->getCode() ,'message' => "$e"));
            }
            
        }// send
        
        public function getInternationalPrefix() {
            return '00';
        }


    }// UMS
