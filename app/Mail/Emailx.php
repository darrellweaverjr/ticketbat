<?php

namespace App\Mail;

use SendGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Description of Email
 *
 * @author ivan
 */
class Emailx {
    
    protected $mail;
    protected $sendGrid;
    protected $copies;
    
    public static function test()
    {
         $from = new Email(null, "nick@ticketbat.com");
        $subject = "Hello World from the SendGrid PHP Library";
        $to = new Email(null, "ivan@ticketbat.com");
        $content = new Content("text/plain", "some text here");
        $mail = new Mail($from, $subject, $to, $content);
        $to = new Email(null, "ivankbc333@gmail.com");
        $mail->personalization[0]->addTo($to);
        //echo json_encode($mail, JSON_PRETTY_PRINT), "\n";
        
        //$apiKey = getenv('SENDGRID_API_KEY');
        $sg = new \SendGrid(env('MAIL_USERNAME'), env('MAIL_PASSWORD'));
        $request_body = $mail;
        $response = $sg->client->mail()->send()->post($request_body);
        echo $response->statusCode();
        echo $response->body();
        echo $response->headers();
    }

    public function __construct($from, $to, $subject)
    {
      
        $this->mail = new SendGrid\Email();
      $this->sendGrid = new SendGrid(env('MAIL_USERNAME'), env('MAIL_PASSWORD'));
      if($from)
        $this->mail->setFrom($from);
      else $this->mail->setFrom(env('MAIL_FROM'));
      $this->mail->setFromName(env('MAIL_FROM_NAME'));
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
                            if(strpos(env('MAIL_TEST'), trim($e)) !== FALSE)
                            {
                                    $this->mail->addTo(trim($e));
                    $this->copies+=1;
                            }
                            else if($replace)
                            {
                                    $this->mail->addTo(env('MAIL_ADMIN'));
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
