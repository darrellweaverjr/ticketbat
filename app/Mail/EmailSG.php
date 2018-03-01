<?php

namespace App\Mail;

use SendGrid;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailSG sends emails thru SendGrid
 *
 * @author ivan
 */
class EmailSG {

    protected $mail;
    protected $sendGrid;

    //builder1 regular email
    public function __construct($from, $to, $subject, $data=null) {
        try {
            //init
            $this->sendGrid = new \SendGrid(env('MAIL_SENDGRID_API_KEY'));
            //from
            if (isset($from)) 
            {
                if (is_array($from)) 
                    $_from = new SendGrid\Email($from[0], $from[1]);
                else 
                    $_from = new SendGrid\Email(env('MAIL_FROM_NAME'), $from);
            }
            else    
                $_from = new SendGrid\Email(env('MAIL_FROM_NAME'), env('MAIL_FROM'));
            //to
            $_personalizations = [];
            if (isset($to)) {
                if (is_string($to)) {
                    $to = explode(",", $to);
                }
                if (is_array($to) && count($to) > 0) 
                {
                    //check if there is individual email
                    if(empty($data))
                    {
                        $personalization = new SendGrid\Personalization();
                        foreach ($to as $p => $t) 
                        {
                            $mail = $this->filter($t);
                            if($mail)
                                $personalization->addTo($mail);
                        }
                        $_personalizations[] = $personalization;
                    }
                    //check if there is multiple email batch list
                    else
                    {
                        foreach ($to as $p => $t) 
                        {
                            $mail = $this->filter($t);
                            if($mail)
                            {
                                $personalization = new SendGrid\Personalization();
                                $personalization->addTo($mail);
                                foreach ($data as $k => $v)
                                    $personalization->addSubstitution(':'.$k, strval($v[$p]));
                                $_personalizations[] = $personalization;
                            }
                        }
                    }                    
                } else
                    return false;
            } else
                return false;
            if(!count($_personalizations))
                return false;
            else
                $_to = $_personalizations[0]->getTos()[0];
            //subject
            if (isset($subject)) {
                $_subject = $subject;
            }
            else $_subject = '';
            //configure object mail
            $this->mail = new SendGrid\Mail($_from, $_subject, $_to, new SendGrid\Content('text/plain',' '));
            foreach ($_personalizations as $index=>$p)
                $this->mail->personalization[$index] = $p;
        } catch (Exception $ex) {
            throw new Exception('Error creating EmailSG: '.$ex->getMessage());
        }
    }

    public function filter($e) {
        try {
            $email = null;
            if (!filter_var($e, FILTER_VALIDATE_EMAIL) === false) {
                if (str_contains(url()->current(), 'dev.ticketbat') || str_contains(url()->current(), 'qa.ticketbat') || str_contains(url()->current(), 'localhost')) {
                    if (strpos(env('MAIL_TEST'), trim($e)) !== FALSE) {
                        $email = new SendGrid\Email(null, $e);
                    } else {
                        $email = new SendGrid\Email(null, env('MAIL_ADMIN'));
                    }
                } else {
                    $email = new SendGrid\Email(null, $e);
                }
            } 
            return $email;
        } catch (Exception $ex) {
            throw new Exception('Error filtering emails EmailSG: '.$ex->getMessage());
        }
    }

    public function subject($subject) {
        try {
            $this->mail->setSubject($subject);
        } catch (Exception $ex) {
            throw new Exception('Error adding subject EmailSG: '.$ex->getMessage());
        }        
    }
    
    public function reply($replyto) {
        try {
            $this->mail->setReplyTo(new SendGrid\ReplyTo($replyto));
        } catch (Exception $ex) {
            throw new Exception('Error adding reply EmailSG: '.$ex->getMessage());
        }        
    }

    public function bcc($bcc) {
        try {
            if (is_array($bcc)) {
                $email = new SendGrid\Email($bcc[0], $bcc[1]);
            } else {
                $email = new SendGrid\Email(null, $bcc);
            }
            $this->mail->personalization[0]->addBcc($email);
        } catch (Exception $ex) {
            throw new Exception('Error adding bcc EmailSG: '.$ex->getMessage());
        }        
    }

    public function cc($cc) {
        try {
            if (is_array($cc)) {
                $email = new SendGrid\Email($cc[0], $cc[1]);
            } else {
                $email = new SendGrid\Email(null, $cc);
            }
            $this->mail->personalization[0]->addCc($email);
        } catch (Exception $ex) {
            throw new Exception('Error adding cc EmailSG: '.$ex->getMessage());
        }        
    }

    public function category($category) {
        try {
            $this->mail->addCategory($category);
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
                    $attachment = new SendGrid\Attachment();
                    $attachment->setContent(base64_encode(file_get_contents($a)));
                    $attachment->setFilename($a);
                    $this->mail->addAttachment($attachment);
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
            $this->html($view->render());
        } catch (Exception $ex) {
            throw new Exception('Error adding view EmailSG: '.$ex->getMessage());
        }        
    }

    public function html($html) {
        try {
            $content = new SendGrid\Content("text/html", $html);
            $this->mail->addContent($content);
        } catch (Exception $ex) {
            throw new Exception('Error adding html EmailSG: '.$ex->getMessage());
        }        
    }

    public function text($text) {
        try {
            $content = new SendGrid\Content("text/plain", $text);
            $this->mail->addContent($content);
        } catch (Exception $ex) {
            throw new Exception('Error adding text EmailSG: '.$ex->getMessage());
        }        
    }

    public function template($template) {
        try {
            $this->mail->setTemplateId($template);
        } catch (Exception $ex) {
            throw new Exception('Error adding template EmailSG: '.$ex->getMessage());
        }        
    }
    
    public function section($sections) {
        try {
            if (is_array($sections)) {               
                foreach ($sections as $k=>$e) {
                    $this->mail->addSection(':'.$k, $e);
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
                    $this->mail->personalization[0]->addSubstitution($e['variable'], $e['value']);
                }
            } else
                return false;
        } catch (Exception $ex) {
            throw new Exception('Error adding body EmailSG: '.$ex->getMessage());
        }        
    }

    public function send($debug=false) {
        try {
            $response = $this->sendGrid->client->mail()->send()->post($this->mail); 
            if($debug)return $response;
            switch (true)
            {
                case (int)$response->statusCode() == 202: 
                    //Log::info('Email sent thru SendGrid successfully');
                    return true;
                case (int)$response->statusCode() >= 500: 
                    Log::error('Error sending email made by SendGrid');
                    return false;
                case (int)$response->statusCode() >= 400: 
                    Log::error('Error sending email with the request on SendGrid');
                    return false;
                default: 
                    Log::warning('Successful request on SendGrid');
                    return false;
            }
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
                            $body[] = array('variable' => ':showdate', 'value' => date('m/d/Y g:ia',strtotime($data['date_now'])));
                        }
                        break;
                    }
                case 'sales_report': {
                        if (isset($data)) {
                            $body[] = array('variable' => ':date', 'value' => $data['date']);
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
