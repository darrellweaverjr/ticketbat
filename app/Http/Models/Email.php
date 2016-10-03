<?php

namespace App\Http\Models;

/**
 * Description of Email
 *
 * @author ivan
 */
class Email {
    
    protected $mail;
    protected $sendGrid;
    protected $copies;

    public function __construct($from, $to, $subject)
    {
      $this->mail = new SendGrid\Email();
      $this->sendGrid = new SendGrid(Config::get('mail.username'), Config::get('mail.password'));
      if($from)
        $this->mail->setFrom($from);
      else $this->mail->setFrom(Config::get('mail.from'));
      $this->mail->setFromName('TicketBat.com');
      $this->copies=0;
      if(isset($to))
      {
        if(is_array($to) && count($to)>0)
                      if(count($to)==1)$this->check_email($to[0],true);
                      else
          foreach ($to as $t)
          {
            $em = explode(",",$t);
            foreach ($em as $e)
                    $this->check_email($e,false);
          }
        else
        {
          $em = explode(",",$to);
                      if(count($em)==1)$this->check_email($em[0],true);
                      else
          foreach ($em as $e)
            $this->check_email($e,false);
        }
      }
      $this->mail->setSubject($subject);          
    }

            public function check_email($e,$replace)
            {			
                    $url = Request::url();
            if (str_contains($url,'dev') || str_contains($url,'qa'))
            {
                            if(strpos(Config::get('mail.test_email'), trim($e)) !== FALSE)
                            {
                                    $this->mail->addTo(trim($e));
                    $this->copies+=1;
                            }
                            else if($replace)
                            {
                                    $this->mail->addTo(Config::get('mail.admin_email'));
                    $this->copies+=1;
                            }
            } 
            else 
            {
                $this->mail->addTo(trim($e));
            $this->copies+=1;
            }
            }

    public function footer($text,$html)
    {
      $this->mail->addFilter('footer', 'enable', 1);
      $this->mail->addFilter('footer', "text/plain", $text);
      $this->mail->addFilter('footer', "text/html", $html);
    }

    public function reply($replyto)
    {
        $this->mail->setReplyTo(trim($replyto));
    }

    public function bcc($bcc)
    {
      $this->mail->addBcc($bcc);
      $this->copies+=1;
    }

    public function cc($cc)
    {
      $this->mail->addCc($cc);
      $this->copies+=1;
    }

    public function category($category)
    {
      $this->mail->addCategory($category);
    }

    public function attachment($attach)
    {
      if(is_array($attach) && count($attach)>0)
        $this->mail->setAttachments($attach);
      else $this->mail->addAttachment($attach);
    }

    public function view($view)
    {
      $this->mail->setHtml($view->render());
    }

    public function html($html)
    {
      $this->mail->setHtml($html);
    }

    public function text($text)
    {
      $this->mail->setText($text);
    }

    public function template($template)
    {
      $this->mail->setHtml(' ');
      $this->mail->setText(' ');
      $this->mail->addFilter('templates', 'enabled', 1);
      $this->mail->addFilter('templates', 'template_id', $template);
    }

    public function body($type,$body)
    {
      if(is_array($body))
      {
        $data = $this->substitute($type,$body);
        foreach($data as $element)
        {
          $values = array();
          for ($i=0; $i <=$this->copies ; $i++)
              $values[] = $element['values'][0];
          $this->mail->addSubstitution($element['variable'], $values);
        }
      }
    }

    public function substitute($type, $data)
    {	
      $body=array();
      switch ($type) {        
        case 'manifest':{
          if(isset($data))
          {
            $body[] = array('variable'=>':type', 'values' => array($data['type']));
            $body[] = array('variable'=>':showname', 'values' => array($data['show_time']));
            $body[] = array('variable'=>':showdate', 'values' => array($data['date_now']));
          }
          break;
        }
        case 'sales_report':{
          if(isset($data))
          {
            $body[] = array('variable'=>':date', 'values' => array($data['date']));
          }
          break;
        }
        case 'reminder':{
          if(isset($data['purchase']) && is_array($data['purchase']))
          {
              foreach ($data['purchase'] as $purchase)
              {
                $body[] = array('variable'=>':show_name', 'values' => array($purchase->show_name));
                $body[] = array('variable'=>':show_date', 'values' =>array(date("Y-m-d", strtotime($purchase->show_time))));
                $body[] = array('variable'=>':show_time', 'values' =>array(date("H:i:s", strtotime($purchase->show_time))));
                $body[] = array('variable'=>':purchase_id', 'values' =>array($purchase->id));
                $body[] = array('variable'=>':transaction_id', 'values' =>array($purchase->transaction_id));
                $body[] = array('variable'=>':user_id', 'values' =>array($purchase->user_id));
              }
          }
          if(isset($data['customer']))
          {
            $body[] = array('variable'=>':name', 'values' => array($data['customer']['first_name'].' '.$data['customer']['last_name']));
          }
          break;
        }
        case 'recover_cart':{
          if(isset($data['email']) && isset($data['name']) && $data['link'])
          {
            $body[] = array('variable'=>':email', 'values' => array($data['email']));
            $body[] = array('variable'=>':name', 'values' => array($data['name']));
            $body[] = array('variable'=>':link', 'values' => array($data['link']));
            $body[] = array('variable'=>':images', 'values' => array($data['images']));
          }
          break;
        }
        case 'promos_weekly':{
          if(isset($data['weekly_promos']))
          {
            $body[] = array('variable'=>':promos', 'values' => array($data['weekly_promos']));
          }
          break;
        }
        default:
          # code...
          break;
      }
      return $body;
    }

    public function send()
    {
        try{
            $response = $this->sendGrid->send($this->mail);
            if($response->body['message'] == 'success') return true;
            return false; 
        } 
        catch(\Exception $e)
        {     
            Session::put('continue', true); 
            //new ExceptionModel($e);
            return false;
        }          
    }
}
