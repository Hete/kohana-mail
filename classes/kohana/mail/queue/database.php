<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * ORM-based queue.
 * 
 * @pacakge Mail
 * @category Queues
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Queue_ORM extends Mail_Queue {

    public function peek() {
        DB::select()->from('mails')
                ->order_by('created', 'ASC')
                ->limit(1)
                ->execute();

        return $this->db_result_to_mail($latest_mail);
    }

    public function pull() {

        $latest_mail = DB::select()->from('mails')
                ->order_by('created', 'ASC')
                ->limit(1)
                ->execute();

        DB::delete('mails')
                ->where('id', '=', $latest_mail->id)
                ->execute();

        return $this->db_result_to_mail($latest_mail);
    }

    public function push(Mail $mail) {
        
        $values = array(
            
        );
        
        DB::insert()->values($values)->execute();
        
    }

    private function db_result_to_mail(Database_Result $db) {
        
    }
    

}

?>
