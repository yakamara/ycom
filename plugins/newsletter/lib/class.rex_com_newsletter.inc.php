<?php

class rex_com_newsletter
{

  static 
    $needed_columns = array("id","email","newsletter_last_id","newsletter","status","activation_key");
  
  public 
    $newsletter_from_email = "",
    $newsletter_from_name = "", 
    $newsletter_subject = "", 
    $newsletter_body_text = "", 
    $newsletter_body_html = "", 
    $newsletter_attachments = null,
    $newsletter_replace = array();


  static function getNeededColumns() 
  {
    return self::$needed_columns;
  }

  static function getAvailableTables() 
  {
    $available_tables = array();
    $s = rex_sql::factory();
    $s->setQuery("show tables");
    $tables = $s->getArray();
    foreach ($tables as $table) {
      $c = rex_sql::factory();
      $c->setQuery("SHOW COLUMNS FROM ".current($table));
      $fields = array();
      foreach ($c->getArray() as $column) {
        if (in_array($column["Field"], rex_com_newsletter::getNeededColumns())) {
          $fields[] = $column["Field"];
        }
      }
      if (count(rex_com_newsletter::getNeededColumns()) == count($fields)) {
        $available_tables[] = current($table);
      }
    }
    return $available_tables;
  }


  public function setSubject($subject) 
  {
    $this->newsletter_subject = $subject;
  }
  
  public function setFrom($email,$name) 
  {
    $this->newsletter_from_email = $email;
    $this->newsletter_from_name = $name;
  }
  
  public function setHTMLBody($html) 
  {
    $this->newsletter_body_html = $html;
  }

  public function setTextBody($text) 
  {
    $this->newsletter_body_text = $text;
  }
  
  public function setBody($attachments) 
  {
    $this->newsletter_attachments = $attachments;
  }

  public function setReplace($search, $replace)
  {
    $this->newsletter_replace[$search] = $replace;
  }

  public function getReplaceArray()
  {
    return $this->newsletter_replace;
  }

  public function sendToUser($user)
  {
  
    global $REX;
  
    if(trim($user["email"]) == "") {
      return FALSE;
    }
  
    $mail = new rex_mailer();
    $mail->AddAddress($user["email"]);
    $mail->From = $this->newsletter_from_email;
    $mail->FromName = $this->newsletter_from_name;
    $mail->Subject = $this->newsletter_subject;
    
    if(is_array($this->newsletter_attachments) && count($this->newsletter_attachments)>0) {
      foreach($this->newsletter_attachments as $name => $attachment) {
      	$mail->AddAttachment($attachment, $name);
      }
    }
  
    if (trim($this->newsletter_body_html) != "") {
      $mail->Body = $this->newsletter_body_html;
      $mail->AltBody = $this->newsletter_body_text;
      foreach ($user as $k => $v) {
        $mail->Body = str_replace( "###".$k."###",$v,$mail->Body);
        $mail->Body = str_replace( "###".strtoupper($k)."###",$v,$mail->Body);
        $mail->Body = str_replace( "+++".$k."+++",urlencode($v),$mail->Body);
        $mail->Body = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Body);
        $mail->Subject = str_replace( "###".$k."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "###".strtoupper($k)."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "+++".$k."+++",urlencode($v),$mail->Subject);
        $mail->Subject = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Subject);
        $mail->AltBody = str_replace( "###".$k."###",$v,$mail->AltBody);
        $mail->AltBody = str_replace( "###".strtoupper($k)."###",$v,$mail->AltBody);
        $mail->AltBody = str_replace( "+++".$k."+++",urlencode($v),$mail->AltBody);
        $mail->AltBody = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->AltBody);
      }
      
      foreach ($this->getReplaceArray() as $k => $v) {
        $mail->Body = str_replace( "###".$k."###",$v,$mail->Body);
        $mail->Body = str_replace( "###".strtoupper($k)."###",$v,$mail->Body);
        $mail->Body = str_replace( "+++".$k."+++",urlencode($v),$mail->Body);
        $mail->Body = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Body);
        $mail->Subject = str_replace( "###".$k."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "###".strtoupper($k)."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "+++".$k."+++",urlencode($v),$mail->Subject);
        $mail->Subject = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Subject);
        $mail->AltBody = str_replace( "###".$k."###",$v,$mail->AltBody);
        $mail->AltBody = str_replace( "###".strtoupper($k)."###",$v,$mail->AltBody);
        $mail->AltBody = str_replace( "+++".$k."+++",urlencode($v),$mail->AltBody);
        $mail->AltBody = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->AltBody);
      }
      
    } else {
      $mail->Body = $this->newsletter_body_text;
      foreach ($user as $k => $v) {
        $mail->Body = str_replace( "###".$k."###",$v,$mail->Body);
        $mail->Body = str_replace( "###".strtoupper($k)."###",$v,$mail->Body);
        $mail->Body = str_replace( "+++".$k."+++",urlencode($v),$mail->Body);
        $mail->Body = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Body);
        $mail->Subject = str_replace( "###".$k."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "###".strtoupper($k)."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "+++".$k."+++",urlencode($v),$mail->Subject);
        $mail->Subject = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Subject);
      }
      foreach ($this->getReplaceArray() as $k => $v) {
        $mail->Body = str_replace( "###".$k."###",$v,$mail->Body);
        $mail->Body = str_replace( "###".strtoupper($k)."###",$v,$mail->Body);
        $mail->Body = str_replace( "+++".$k."+++",urlencode($v),$mail->Body);
        $mail->Body = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Body);
        $mail->Subject = str_replace( "###".$k."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "###".strtoupper($k)."###",$v,$mail->Subject);
        $mail->Subject = str_replace( "+++".$k."+++",urlencode($v),$mail->Subject);
        $mail->Subject = str_replace( "+++".strtoupper($k)."+++",urlencode($v),$mail->Subject);
      }
    } 
  
    return $mail->Send();
  }

}