<?php

    /**
     * File containing: Position class
     * 
     * @copyright Copyright 2013 {@link http://www.discoos.org DISCO OS Foundation} 
     *
     * @since 13. June 2013
     * 
     * @author Kenneth Gulbrandsøy <kenneth@discoos.org>
     */

    namespace RescueMe;

    /**
     * Position class
     * 
     * @package RescueMe
     */
    class Position
    {

        const TABLE = "positions";
                
        public $pos_id = -1;
        public $lat = -1;
        public $lon = -1;
        public $acc = -1;
        public $alt = -1;
        public $timestamp = -1;
        public $human = 'Aldri posisjonert';


        function __construct($pos_id = -1)
        {
            $this->pos_id = (int) $pos_id;
            $this->load();
        }
        
        function set($data) {
            $this->lat = isset_get($data,'lat');
            $this->lon = isset_get($data,'lon');
            $this->acc = isset_get($data,'acc');
            $this->alt = isset_get($data,'alt');
            $this->timestamp = isset_get($data,'timestamp', time());
        }


        function load()
        {
            if($this->pos_id === -1)
                return false;

            $query = "SELECT * FROM `".self::TABLE."` WHERE `pos_id` = " . (int) $this->pos_id;
            
            $res = DB::query($query);

            if(DB::isEmpty($res)) return false;
            
            $this->set($res->fetch_assoc());
        }


    }

?>