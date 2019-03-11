<?php

namespace App\Mail;

use App\Exceptions\Handler;

/**
 * Class EmailSG sends emails thru SendGrid
 *
 * @author ivan
 */
class EmailSG extends \SendGrid {
    
    private $emails = [];
    
    //main constructor, pass target and subject
    public function __construct($from, $to, $subject) {
        //create object
        parent::__construct(env('MAIL_SENDGRID_API_KEY'));
        //from
        if (isset($from)) 
        {
            if (is_array($from)) 
                $_from = [$from[1], $from[0]];
            else 
                $_from = [$from, env('MAIL_FROM_NAME')];
        }
        else    
            $_from = [env('MAIL_FROM'), env('MAIL_FROM_NAME')];
        //to
        if(!is_array($to))
            $to = explode(',', $to);
        foreach ($to as $t)
        {
            $t = $this->filter($t);
            if ($t)
            {
                $email = new \SendGrid\Mail\Mail(); 
                $email->setFrom($_from[0], $_from[1]);
                $email->setSubject($subject);
                $email->addTo($t);
                $this->emails[] = $email;
            }
        }
    }
    
    public function filter($e) {
        try {
            $email = null;
            if (!filter_var($e, FILTER_VALIDATE_EMAIL) === false) {
                if ( env('APP_ENV','development') != 'production'  ) {
                    if (strpos(env('MAIL_TEST'), trim($e)) !== FALSE) {
                        $email = $e;
                    } else {
                        $email = env('MAIL_ADMIN');
                    }
                } else {
                    $email = $e;
                }
            } 
            return $email;
        } catch (Exception $ex) {
            throw new Exception('Error filtering emails EmailSG: '.$ex->getMessage());
        }
    }

    public function subject($subject) {
        try {
            foreach ($this->emails as $email)
                $email->setSubject($subject);
        } catch (Exception $ex) {
            throw new Exception('Error adding subject EmailSG: '.$ex->getMessage());
        }        
    }
    
    public function reply($replyto) {
        try {
            foreach ($this->emails as $email)
                $email->setReplyTo ($replyto);
        } catch (Exception $ex) {
            throw new Exception('Error adding reply EmailSG: '.$ex->getMessage());
        }        
    }

    public function bcc($bcc) {
        try {
            if (is_array($bcc)) {
                foreach ($this->emails as $email)
                    $email->addBcc($bcc[1], $bcc[0]);
            } else {
                foreach ($this->emails as $email)
                    $email->addBcc($bcc);
            }
        } catch (Exception $ex) {
            throw new Exception('Error adding bcc EmailSG: '.$ex->getMessage());
        }        
    }

    public function cc($cc) {
        try {
            if (is_array($cc)) {
                foreach ($this->emails as $email)
                    $email->addCc ($cc[1], $cc[0]);
            } else {
                foreach ($this->emails as $email)
                    $email->addCc ($cc);
            }
        } catch (Exception $ex) {
            throw new Exception('Error adding cc EmailSG: '.$ex->getMessage());
        }        
    }

    public function category($category) {
        try {
            foreach ($this->emails as $email)
                $email->addCategory ($category);
        } catch (Exception $ex) {
            throw new Exception('Error adding category EmailSG: '.$ex->getMessage());
        }        
    }

    public function attachment($attach) {
        try {
            if (is_string($attach)) {
                $attach = explode(",", $attach);
            }
            if (is_array($attach) && count($attach) > 0) {
                foreach ($attach as $a) {
                    $attachment = new \SendGrid\Mail\Attachment(base64_encode(file_get_contents($a)), null, $a);
                    foreach ($this->emails as $email)
                        $email->addAttachment ($attachment);
                }
                return true;
            } else
                return false;
        } catch (Exception $ex) {
            throw new Exception('Error adding attachment EmailSG: '.$ex->getMessage());
        }         
    }

    public function view($view) {
        try {
            foreach ($this->emails as $email)
                $email->addContent ("text/html", $view->render());
        } catch (Exception $ex) {
            throw new Exception('Error adding view EmailSG: '.$ex->getMessage());
        }        
    }

    public function html($html) {
        try {
            foreach ($this->emails as $email)
                $email->addContent ("text/html", $html);
        } catch (Exception $ex) {
            throw new Exception('Error adding html EmailSG: '.$ex->getMessage());
        }        
    }

    public function text($text) {
        try {
            foreach ($this->emails as $email)
                $email->addContent ("text/plain", $text);
        } catch (Exception $ex) {
            throw new Exception('Error adding text EmailSG: '.$ex->getMessage());
        }        
    }

    public function template($template) {
        try {
            foreach ($this->emails as $email)
                $email->setTemplateId ($template);
        } catch (Exception $ex) {
            throw new Exception('Error adding template EmailSG: '.$ex->getMessage());
        }        
    }
    
    public function section($sections) {
        try {
            if (is_array($sections)) {               
                foreach ($sections as $k=>$e) {
                    foreach ($this->emails as $email)
                        $email->addSection (':'.$k, $e);
                }
            } else
                return false;
        } catch (Exception $ex) {
            throw new Exception('Error adding section EmailSG: '.$ex->getMessage());
        }        
    }

    public function body($type, $body) {
        try {
            if (is_array($body)) {
                $this->html('<html><body></body></html>');
                $data = $this->replace($type, $body);
                foreach ($data as $e) {
                    foreach ($this->emails as $email)
                        $email->addSubstitution ($e['variable'], $e['value']);
                }
            } else
                return false;
        } catch (Exception $ex) {
            throw new Exception('Error adding body EmailSG: '.$ex->getMessage());
        }        
    }

    public function send($debug=false) {
        try {
            $result = true;
            $debug = '';
            foreach ($this->emails as $email)
            {
                $response = parent::send($email);
                //result
                $result = ($response->statusCode()==202 && $result);
                //debug
                if($debug)
                    $debug.= json_encode($response)."\n";
            }
            //debug
            return ($debug)? $debug : $result;
        } catch (Exception $ex) { 
            Handler::reportException($ex);
            return false;
        }
    }

    public function replace($type, $data) {
        try {
            $body = array();
            switch ($type) {
                case 'receipt': {
                        if (isset($data) && isset($data['rows']) && isset($data['totals']) && isset($data['banners'])) {
                            $body[] = array('variable'=>':rows', 'value' => $data['rows']);
                            $body[] = array('variable'=>':totals', 'value' => $data['totals']);
                            $body[] = array('variable'=>':banners', 'value' => $data['banners']);
                            $body[] = array('variable'=>':top', 'value' => (isset($data['top'])? $data['top'] : ''));
                        }
                        break;
                    }
                case 'manifest': {
                        if (isset($data)) {
                            $body[] = array('variable' => ':type', 'value' => $data['type']);
                            $body[] = array('variable' => ':showname', 'value' => $data['name']);
                            $body[] = array('variable' => ':showdate', 'value' => date('m/d/Y g:ia'));
                        }
                        break;
                    }
                case 'reminder': {
                        if (isset($data['purchase']) && is_array($data['purchase'])) {
                            foreach ($data['purchase'] as $purchase) {
                                $body[] = array('variable' => ':show_name', 'value' => $purchase->show_name);
                                $body[] = array('variable' => ':show_date', 'value' => date("Y-m-d", strtotime($purchase->show_time)));
                                $body[] = array('variable' => ':show_time', 'value' => date("H:i:s", strtotime($purchase->show_time)));
                                $body[] = array('variable' => ':purchase_id', 'value' => $purchase->id);
                                $body[] = array('variable' => ':transaction_id', 'value' => $purchase->transaction_id);
                                $body[] = array('variable' => ':user_id', 'value' => $purchase->user_id);
                            }
                        }
                        if (isset($data['customer'])) {
                            $body[] = array('variable' => ':name', 'value' => $data['customer']->first_name.' '.$data['customer']->last_name);
                        }
                        break;
                    }
                case 'recover_cart': {
                        if (isset($data['email']) && isset($data['name']) && $data['link']) {
                            $body[] = array('variable' => ':email', 'value' => $data['email']);
                            $body[] = array('variable' => ':name', 'value' => $data['name']);
                            $body[] = array('variable' => ':link', 'value' => $data['link']);
                            $body[] = array('variable' => ':images', 'value' => $data['images']);
                        }
                        break;
                    }
                case 'promos_announced': {
                        if (isset($data) && isset($data['announced']) && isset($data['week'])) {
                            $body[] = array('variable' => ':announced', 'value' => $data['announced']);
                            $body[] = array('variable' => ':week', 'value' => $data['week']);
                            $body[] = array('variable' => ':year', 'value' => date('Y'));
                        }
                        break;
                    }
                case 'welcome':{
                        if(isset($data) && isset($data['username']) && isset($data['password']))
                        {       
                          $body[] = array('variable'=>':username', 'value' => $data['username']);
                          $body[] = array('variable'=>':password', 'value' => $data['password']);
                          if(empty($data['first_purchase']))
                              $body[] = array('variable'=>':purchase', 'value' => '');
                          else 
                              $body[] = array('variable'=>':purchase', 'value' => 'Thank you for making your first purchase with <a href="https://www.ticketbat.com" style="color:#00AA0E; text-transform:capitalize;">TicketBat.com</a>!');
                        }
                        break;
                    }
                case 'custom': {
                        if (isset($data) && isset($data['body'])) {
                            $body[] = array('variable' => ':body', 'value' => $data['body']);
                        }
                        break;
                    }
                default:
                    break;
            }
            return $body;
        } catch (Exception $ex) {
            throw new Exception('Error replacing body EmailSG: '.$ex->getMessage());
        }        
    }

}
