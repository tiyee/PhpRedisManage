<?php

namespace Library;
	class redis extends \Redis implements \Config\redis {
		private static $_instance;
   		private $expire = 2592000;


    public static function getInstance() {
        if(! (self::$_instance instanceof self) ) {
            self::$_instance = new self(self::RE_HOST,self::RE_PORT);
        }
        return self::$_instance;
    }
    public function __construct($hostname,$port) {


        if(false === $this->connect($hostname,$port,1) ){
            throw new \Exception\error('can\'t connect');

        }
	   if(!empty(self::RE_AUTH)) {
            if ($this->auth(self::RE_AUTH) == false) {
                throw new \Exception\error('can\'t connect');
            }
	   }




    }
     public $db_num = self::RE_DBNO;

}

