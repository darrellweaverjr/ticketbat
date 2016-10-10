<?php

namespace App\Mail;

use SendGrid;

/**
 * Description of Email
 *
 * @author ivan
 */
class EmailSG {

    protected $mail;
    protected $sendGrid;

    public function __construct($from, $to, $subject) {
        try {
            //init
            $this->sendGrid = new \SendGrid(env('MAIL_SENDGRID_API_KEY'));
            $this->mail = new SendGrid\Mail();
            $this->mail->addPersonalization(new SendGrid\Personalization());
            //from
            if (isset($from)) {
                if (is_array($from)) {
                    $e = new SendGrid\Email($from[0], $from[1]);
                    $this->mail->setFrom($e);
                } else {
                    $e = new SendGrid\Email(null, $from);
                    $this->mail->setFrom($e);
                }
            } else {
                $e = new SendGrid\Email(env('MAIL_FROM_NAME'), env('MAIL_FROM'));
                $this->mail->setFrom($e);
            }
            //to
            if (isset($to)) {
                if (is_string($to)) {
                    $to = explode(",", $to);
                }
                if (is_array($to) && count($to) > 0) {
                    foreach ($to as $t) {
                        $this->filter($t);
                    }
                } else
                    return false;
            } else
                return false;
            //subject
            if (isset($subject)) {
                $this->mail->setSubject($subject);
            }
        } catch (Exception $ex) {
            
        }
    }

    public function filter($e, $placed = 'to') {
        try {
            $email = null;
            if (!filter_var($e, FILTER_VALIDATE_EMAIL) === false) {
                if (str_contains(url()->current(), 'dev.ticketbat') || str_contains(url()->current(), 'qa.ticketbat')) {
                    if (strpos(env('MAIL_TEST'), trim($e)) !== FALSE) {
                        $email = new SendGrid\Email(null, $e);
                    } else {
                        $email = new SendGrid\Email(null, env('MAIL_ADMIN'));
                    }
                } else {
                    $email = new SendGrid\Email(null, $e);
                }
                if ($email) {
                    switch ($placed) {
                        case 'bcc':
                            $this->mail->personalization[0]->addBcc($email);
                            break;
                        case 'cc':
                            $this->mail->personalization[0]->addCc($email);
                            break;
                        default:
                            $this->mail->personalization[0]->addTo($email);
                            break;
                    }
                    return true;
                } else
                    return false;
            } else
                return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function reply($replyto) {
        $this->mail->setReplyTo(new ReplyTo($replyto));
    }

    public function bcc($bcc) {
        if (is_array($bcc)) {
            $email = new SendGrid\Email($bcc[0], $bcc[1]);
        } else {
            $email = new SendGrid\Email(null, $bcc);
        }
        $this->mail->personalization[0]->addBcc($email);
    }

    public function cc($cc) {
        if (is_array($cc)) {
            $email = new SendGrid\Email($cc[0], $cc[1]);
        } else {
            $email = new SendGrid\Email(null, $cc);
        }
        $this->mail->personalization[0]->addCc($email);
    }

    public function category($category) {
        $this->mail->addCategory($category);
    }

    public function attachment($attach) {
        if (is_string($attach)) {
            $attach = explode(",", $attach);
        }
        if (is_array($attach) && count($attach) > 0) {
            foreach ($attach as $a) {
                $attachment = new SendGrid\Attachment();
                $attachment->setFilename($a);
                $attachment->setDisposition("attachment");
                $this->mail->addAttachment($attachment);
            }
        } else
            return false;
    }

    public function view($view) {
        $this->html($view->render());
    }

    public function html($html) {
        $content = new SendGrid\Content("text/html", $html);
        $this->mail->addContent($content);
    }

    public function text($text) {
        $content = new SendGrid\Content("text/plain", $text);
        $this->mail->addContent($content);
    }

    public function template($template) {
        $this->mail->setTemplateId($template);
    }

    public function body($type, $body) {
        if (is_array($body)) {
            $data = $this->replace($type, $body);
            foreach ($data as $e) {
                $this->mail->personalization[0]->addSubstitution($e['variable'], $e['value']);
            }
        } else
            return false;
    }

    public function send() {
        try {
            $request_body = $this->mail;
            $response = $this->sendGrid->client->mail()->send()->post($request_body);
            switch (true)
            {
                case (int)$response->statusCode() == 202: 
                    console.log('Email SendGrid sent successfully');
                    return true;
                    break;
                case (int)$response->statusCode() > 500: 
                    console.log('Error made by SendGrid');
                    return false;
                    break;
                case (int)$response->statusCode() > 400: 
                    console.log('Error with the request on SendGrid');
                    return false;
                    break;
                default: 
                    console.log('Successful request on SendGrid');
                    return false;
                    break;
            }
            return true;
        } catch (\Exception $e) {            
            return false;
        }
    }

    public function replace($type, $data) {
        $body = array();
        switch ($type) {
            case 'manifest': {
                    if (isset($data)) {
                        $body[] = array('variable' => ':type', 'value' => $data['type']);
                        $body[] = array('variable' => ':showname', 'value' => $data['show_time']);
                        $body[] = array('variable' => ':showdate', 'value' => $data['date_now']);
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
                        $body[] = array('variable' => ':name', 'value' => $data['customer']['first_name'] . ' ' . $data['customer']['last_name']);
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
            case 'promos_weekly': {
                    if (isset($data['weekly_promos'])) {
                        $body[] = array('variable' => ':promos', 'value' => $data['weekly_promos']);
                    }
                    break;
                }
            case 'welcome': {
                    if (isset($data)) {
                        $body[] = array('variable' => ':username', 'value' => $data['username']);
                        $body[] = array('variable' => ':password', 'value' => $data['password']);
                    }
                    break;
                }    
            default:
                break;
        }
        return $body;
    }

}
