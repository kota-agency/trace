<?php
/**
* Voting module
* Vote class
*
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class HT_Vote {

    public $vote_id, $key, $magnitude, $ip, $time, $user_id, $comments; 
    
    //constructor
    public function __construct($magnitude) {
        $this->magnitude=$magnitude;

        //get the user ip
        $this->ip = hkb_get_user_ip();

        //vote time/date 
        $this->time = time();

        //get the current user
        $this->user_id = hkb_get_current_user_id();
        

        //generate key
        $this->key = md5( strval($this->magnitude) . $this->ip . $this->time . $this->user_id );        
    }

    public function set_comments($comments=''){
        $this->comments = $comments;
    }

    public function get_comments(){
        return $this->comments;
    }

} //end class


class HT_Vote_Up extends HT_Vote {
    //constructor
    public function __construct() {
        parent::__construct(10);
    }

} 


class HT_Vote_Down extends HT_Vote {
    //constructor
    public function __construct() {
        parent::__construct(0);
    }

} 

class HT_Vote_Neutral extends HT_Vote {
    //constructor
    public function __construct() {
        parent::__construct(5);
    }

}

class HT_Vote_Value extends HT_Vote {
    //constructor
    public function __construct($value = 5) {
        parent::__construct($value);
    }

} 