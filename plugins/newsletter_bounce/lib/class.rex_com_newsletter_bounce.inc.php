<?php
/* class.rex_com_newsletter_bounce.inc.php aka class.phpmailer-bmh.php
   v5.1 from: https://github.com/hippich/PHPMailer-BMH

.---------------------------------------------------------------------------.
|  Software: PHPMailer-BMH (Bounce Mail Handler)                            |
|   Version: 5.1
|   Contact: codeworxtech@users.sourceforge.net                             |
|      Info: http://phpmailer.codeworxtech.com                              |
| ------------------------------------------------------------------------- |
|    Author: Andy Prevost andy.prevost@worxteam.com (admin)                 |
| Copyright (c) 2002-2009, Andy Prevost. All Rights Reserved.               |
| ------------------------------------------------------------------------- |
|   License: Distributed under the General Public License (GPL)             |
|            (http://www.gnu.org/licenses/gpl.html)                         |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
| ------------------------------------------------------------------------- |
| This is a update of the original Bounce Mail Handler script               |
| http://sourceforge.net/projects/bmh/                                      |
| The script has been renamed from Bounce Mail Handler to PHPMailer-BMH     |
| ------------------------------------------------------------------------- |
| We offer a number of paid services:                                       |
| - Web Hosting on highly optimized fast and secure servers                 |
| - Technology Consulting                                                   |
| - Oursourcing (highly qualified programmers and graphic designers)        |
'---------------------------------------------------------------------------'

/**
 * PHPMailer-BMH (Bounce Mail Handler)
 *
 * PHPMailer-BMH is a PHP program to check your IMAP/POP3 inbox and
 * delete all 'hard' bounced emails. It features a callback function where
 * you can create a custom action. This provides you the ability to write
 * a script to match your database records and either set inactive or
 * delete records with email addresses that match the 'hard' bounce results.
 *
 * @package PHPMailer-BMH
 * @author Andy Prevost
 * @copyright 2008-2009, Andy Prevost
 * @license GPL licensed
 * @version 5.1
 * @link http://sourceforge.net/projects/bmh
 *
 */

define('VERBOSE_QUIET',  0); // means no output at all
define('VERBOSE_SIMPLE', 1); // means only output simple report
define('VERBOSE_REPORT', 2); // means output a detail report
define('VERBOSE_DEBUG',  3); // means output detail report as well as debug info.

class rex_com_newsletter_bounce {

  /////////////////////////////////////////////////
  // PROPERTIES, PUBLIC
  /////////////////////////////////////////////////

  /**
   * Holds Bounce Mail Handler version.
   * @var string
   */
  public $Version = "5.1";

  /**
   * Mail server
   * @var string
   */
  public $mailhost = 'localhost';

  /**
   * The username of mailbox
   * @var string
   */

  public $mailbox_username;
  /**
   * The password needed to access mailbox
   * @var string
   */
  public $mailbox_password;

  /**
   * The last error msg
   * @var string
   */
  public $error_msg;

  /**
   * Maximum limit messages processed in one batch
   * @var int
   */
  public $max_messages = 3000;

  /**
   * Callback Action function name
   * the function that handles the bounce mail. Parameters:
   *   int     $msgnum        the message number returned by Bounce Mail Handler
   *   string  $bounce_type   the bounce type: 'antispam','autoreply','concurrent','content_reject','command_reject','internal_error','defer','delayed'        => array('remove'=>0,'bounce_type'=>'temporary'),'dns_loop','dns_unknown','full','inactive','latin_only','other','oversize','outofoffice','unknown','unrecognized','user_reject','warning'
   *   string  $email         the target email address
   *   string  $subject       the subject, ignore now
   *   string  $xheader       the XBounceHeader from the mail
   *   1 or 0  $remove        delete status, 0 is not deleted, 1 is deleted
   *   string  $rule_no       bounce mail detect rule no.
   *   string  $rule_cat      bounce mail detect rule category
   *   int     $totalFetched  total number of messages in the mailbox
   * @var string
   */
  public $action_function = 'callbackAction';

  /**
   * Internal variable
   * The resource handler for the opened mailbox (POP3/IMAP/NNTP/etc.)
   * @var object
   */
  public $_mailbox_link = false;

  /**
   * Test mode, if true will not delete messages
   * @var boolean
   */
  public $testmode = false;

  /**
   * Purge the unknown messages (or not)
   * @var boolean
   */
  public $purge_unprocessed = false;

  /**
   * Control the debug output, default is VERBOSE_SIMPLE
   * @var int
   */
  public $verbose = VERBOSE_SIMPLE;

  /**
   * control the failed DSN rules output
   * @var boolean
   */
  public $debug_dsn_rule = false;

  /**
   * control the failed BODY rules output
   * @var boolean
   */
  public $debug_body_rule = false;

  /**
   * Control the method to process the mail header
   * if set true, uses the imap_fetchstructure function
   * otherwise, detect message type directly from headers,
   * a bit faster than imap_fetchstructure function and take less resources.
   * however - the difference is negligible
   * @var boolean
   */
  public $use_fetchstructure = true;

  /**
   * If disable_delete is equal to true, it will disable the delete function
   * @var boolean
   */
  public $disable_delete = false;

  /*
   * Defines new line ending
   */
  public $bmh_newline = "<br />\n";

  /*
   * Defines port number, default is '143', other common choices are '110' (pop3), '993' (gmail)
   * @var integer
   */
  public $port = 143;

  /*
   * Defines service, default is 'imap', choice includes 'pop3'
   * @var string
   */
  public $service = 'imap';

  /*
   * Defines service option, default is 'notls', other choices are 'tls', 'ssl'
   * @var string
   */
  public $service_option = 'notls';

  /*
   * Mailbox type, default is 'INBOX', other choices are (Tasks, Spam, Replies, etc.)
   * @var string
   */
  public $boxname = 'INBOX';

  /*
   * Determines if soft bounces will be moved to another mailbox folder
   * @var boolean
   */
  public $moveSoft = false;

  /*
   * Mailbox folder to move soft bounces to, default is 'soft'
   * @var string
   */
  public $softMailbox = 'INBOX.soft';

  /*
   * Determines if hard bounces will be moved to another mailbox folder
   * NOTE: If true, this will disable delete and perform a move operation instead
   * @var boolean
   */
  public $moveHard = false;

  /*
   * Mailbox folder to move hard bounces to, default is 'hard'
   * @var string
   */
  public $hardMailbox = 'INBOX.hard';

  /*
   * Deletes messages globally prior to date in variable
   * NOTE: excludes any message folder that includes 'sent' in mailbox name
   * format is same as MySQL: 'yyyy-mm-dd'
   * if variable is blank, will not process global delete
   * @var string
   */
  public $deleteMsgDate = '';

  /////////////////////////////////////////////////
  // METHODS
  /////////////////////////////////////////////////

  /**
   * Output additional msg for debug
   * @param string $msg,  if not given, output the last error msg
   * @param string $verbose_level,  the output level of this message
   */
  function output($msg=false,$verbose_level=VERBOSE_SIMPLE) {
    if ($this->verbose >= $verbose_level) {
      if (empty($msg)) {
        echo $this->error_msg . $this->bmh_newline;
      } else {
        echo $msg . $this->bmh_newline;
      }
    }
  }

  /**
   * Open a mail box
   * @return boolean
   */
  function openMailbox() {
    // before starting the processing, let's check the delete flag and do global deletes if true
    if ( trim($this->deleteMsgDate) != '' ) {
      echo "processing global delete based on date of " . $this->deleteMsgDate . "<br />";
      $this->globalDelete($nameRaw);
    }
    // disable move operations if server is Gmail ... Gmail does not support mailbox creation
    if ( stristr($this->mailhost,'gmail') ) {
      $this->moveSoft = false;
      $this->moveHard = false;
    }
    $port = $this->port . '/' . $this->service . '/' . $this->service_option;
    set_time_limit(6000);
    if (!$this->testmode) {
      $this->_mailbox_link = imap_open("{".$this->mailhost.":".$port."}" . $this->boxname,$this->mailbox_username,$this->mailbox_password,CL_EXPUNGE);
    } else {
      $this->_mailbox_link = imap_open("{".$this->mailhost.":".$port."}" . $this->boxname,$this->mailbox_username,$this->mailbox_password);
    }
    if (!$this->_mailbox_link) {
      $this->error_msg = 'Cannot create ' . $this->service . ' connection to ' . $this->mailhost . $this->bmh_newline . 'Error MSG: ' . imap_last_error();
      $this->output();
      return false;
    } else {
      $this->output('Connected to: ' . $this->mailhost . ' (' . $this->mailbox_username . ')');
      return true;
    }
  }

  /**
   * Open a mail box in local file system
   * @param string $file_path (The local mailbox file path)
   * @return boolean
   */
  function openLocal($file_path) {
    set_time_limit(6000);
    if (!$this->testmode) {
      $this->_mailbox_link = imap_open("$file_path",'','',CL_EXPUNGE);
    } else {
      $this->_mailbox_link = imap_open("$file_path",'','');
    }
    if (!$this->_mailbox_link) {
      $this->error_msg = 'Cannot open the mailbox file to ' . $file_path . $this->bmh_newline . 'Error MSG: ' . imap_last_error();
      $this->output();
      return false;
    } else {
      $this->output('Opened ' . $file_path);
      return true;
    }
  }

  /**
   * Process the messages in a mailbox
   * @param string $max       (maximum limit messages processed in one batch, if not given uses the property $max_messages
   * @return boolean
   */
  function processMailbox($max=false) {
    if ( empty($this->action_function) || !is_callable($this->action_function) ) {
      $this->error_msg = 'Action function not found!';
      $this->output();
      return false;
    }

    if ( $this->moveHard && ( $this->disable_delete === false ) ) {
      $this->disable_delete = true;
    }

    if (!empty($max)) {
      $this->max_messages=$max;
    }

    // initialize counters
    $c_total       = imap_num_msg($this->_mailbox_link);
    $c_fetched     = $c_total;
    $c_processed   = 0;
    $c_unprocessed = 0;
    $c_deleted     = 0;
    $c_moved       = 0;
    $this->output( 'Total: ' . $c_total . ' messages ');
    // proccess maximum number of messages
    if ($c_fetched > $this->max_messages) {
      $c_fetched = $this->max_messages;
      $this->output( 'Processing first ' . $c_fetched . ' messages ' );
    }

    if ($this->testmode) {
      $this->output( 'Running in test mode, not deleting messages from mailbox<br />' );
    } else {
      if ($this->disable_delete) {
        if ( $this->moveHard ) {
          $this->output( 'Running in move mode<br />' );
        } else {
          $this->output( 'Running in disable_delete mode, not deleting messages from mailbox<br />' );
        }
      } else {
        $this->output( 'Processed messages will be deleted from mailbox<br />' );
      }
    }
    for($x=1; $x <= $c_fetched; $x++) {
      /*
      $this->output( $x . ":",VERBOSE_REPORT);
      if ($x % 10 == 0) {
        $this->output( '.',VERBOSE_SIMPLE);
      }
      */
      // fetch the messages one at a time
      if ($this->use_fetchstructure) {
        $structure = imap_fetchstructure($this->_mailbox_link,$x);
        if ($structure->type == 1 && $structure->ifsubtype && $structure->subtype == 'REPORT' && $structure->ifparameters && $this->isParameter($structure->parameters, 'REPORT-TYPE','delivery-status')) {
          $processed = $this->processBounce($x,'DSN',$c_total);
        } else { // not standard DSN msg
          $this->output( 'Msg #' .  $x . ' is not a standard DSN message',VERBOSE_REPORT);
          if ($this->debug_body_rule) {
            if ($structure->ifdescription) {
              $this->output( "  Content-Type : {$structure->description}",VERBOSE_DEBUG);
            } else {
              $this->output( "  Content-Type : unsupported",VERBOSE_DEBUG);
            }
          }
          $processed = $this->processBounce($x,'BODY',$c_total);
        }
      } else {
        $header = imap_fetchheader($this->_mailbox_link,$x);
        // Could be multi-line, if the new line begins with SPACE or HTAB
        if (preg_match ("/Content-Type:((?:[^\n]|\n[\t ])+)(?:\n[^\t ]|$)/is",$header,$match)) {
          if (preg_match("/multipart\/report/is",$match[1]) && preg_match("/report-type=[\"']?delivery-status[\"']?/is",$match[1])) {
            // standard DSN msg
            $processed = $this->processBounce($x,'DSN',$c_total);
          } else { // not standard DSN msg
            $this->output( 'Msg #' .  $x . ' is not a standard DSN message',VERBOSE_REPORT);
            if ($this->debug_body_rule) {
              $this->output( "  Content-Type : {$match[1]}",VERBOSE_DEBUG);
            }
            $processed = $this->processBounce($x,'BODY',$c_total);
          }
        } else { // didn't get content-type header
          $this->output( 'Msg #' .  $x . ' is not a well-formatted MIME mail, missing Content-Type',VERBOSE_REPORT);
          if ($this->debug_body_rule) {
            $this->output( '  Headers: ' . $this->bmh_newline . $header . $this->bmh_newline,VERBOSE_DEBUG);
          }
          $processed = $this->processBounce($x,'BODY',$c_total);
        }
      }

      $deleteFlag[$x] = false;
      $moveFlag[$x]   = false;
      if ($processed) {
        $c_processed++;
        if ( ($this->testmode === false) && ($this->disable_delete === false) ) {
          // delete the bounce if not in test mode and not in disable_delete mode
          @imap_delete($this->_mailbox_link,$x);
          $deleteFlag[$x] = true;
          $c_deleted++;
        } elseif ( $this->moveHard ) {
          // check if the move directory exists, if not create it
          $this->mailbox_exist($this->hardMailbox);
          // move the message
          @imap_mail_move($this->_mailbox_link, $x, $this->hardMailbox);
          $moveFlag[$x] = true;
          $c_moved++;
        } elseif ( $this->moveSoft ) {
          // check if the move directory exists, if not create it
          $this->mailbox_exist($this->softMailbox);
          // move the message
          @imap_mail_move($this->_mailbox_link, $x, $this->softMailbox);
          $moveFlag[$x] = true;
          $c_moved++;
        }
      } else { // not processed
        $c_unprocessed++;
        if ( !$this->testmode && !$this->disable_delete && $this->purge_unprocessed ) {
          // delete this bounce if not in test mode, not in disable_delete mode, and the flag BOUNCE_PURGE_UNPROCESSED is set
          @imap_delete($this->_mailbox_link,$x);
          $deleteFlag[$x] = true;
          $c_deleted++;
        }
      }
      //flush();
    }
    $this->output( $this->bmh_newline . 'Closing mailbox, and purging messages' );
    imap_close($this->_mailbox_link);
    $this->output( 'Read: ' . $c_fetched . ' messages');
    $this->output( $c_processed . ' action taken' );
    $this->output( $c_unprocessed . ' no action taken' );
    $this->output( $c_deleted . ' messages deleted' );
    $this->output( $c_moved . ' messages moved' );
    return true;
  }

  /**
   * Function to determine if a particular value is found in a imap_fetchstructure key
   * @param array  $currParameters (imap_fetstructure parameters)
   * @param string $varKey         (imap_fetstructure key)
   * @param string $varValue       (value to check for)
   * @return boolean
   */
  function isParameter($currParameters, $varKey, $varValue) {
    foreach ($currParameters as $object) {
      if ( $object->attribute == $varKey ) {
        if ( $object->value == $varValue ) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Function to process each individual message
   * @param int    $pos            (message number)
   * @param string $type           (DNS or BODY type)
   * @param string $totalFetched   (total number of messages in mailbox)
   * @return boolean
   */
  function processBounce($pos,$type,$totalFetched) {
    $header      = imap_header($this->_mailbox_link,$pos);
    $subject     = strip_tags($header->subject);
    $body        = '';

    if ($type == 'DSN') {
      // first part of DSN (Delivery Status Notification), human-readable explanation
      $dsn_msg = imap_fetchbody($this->_mailbox_link,$pos,"1");
      $dsn_msg_structure = imap_bodystruct($this->_mailbox_link,$pos,"1");

      if ( $dsn_msg_structure->encoding == 4 ) {
        $dsn_msg = quoted_printable_decode($dsn_msg);
      } elseif ( $dsn_msg_structure->encoding == 3 ) {
        $dsn_msg = base64_decode($dsn_msg);
      }

      // second part of DSN (Delivery Status Notification), delivery-status
      $dsn_report = imap_fetchbody($this->_mailbox_link,$pos,"2");

      // process bounces by rules
      $result = bmhDSNRules($dsn_msg,$dsn_report,$this->debug_dsn_rule);
    } elseif ($type == 'BODY') {
      $structure = imap_fetchstructure($this->_mailbox_link,$pos);
      switch ($structure->type) {
        case 0: // Content-type = text
          $body = imap_fetchbody($this->_mailbox_link,$pos,"1");
          $result = bmhBodyRules($body,$structure,$this->debug_body_rule);
          break;
        case 1: // Content-type = multipart
          $body = imap_fetchbody($this->_mailbox_link,$pos,"1");
          // Detect encoding and decode - only base64
          if ( $structure->parts[0]->encoding == 4 ) {
            $body = quoted_printable_decode($body);
          } elseif ( $structure->parts[0]->encoding == 3 ) {
            $body = base64_decode($body);
          }
          $result = bmhBodyRules($body,$structure,$this->debug_body_rule);
          break;
        case 2: // Content-type = message
          $body = imap_body($this->_mailbox_link,$pos);
          if ( $structure->encoding == 4 ) {
            $body = quoted_printable_decode($body);
          } elseif ( $structure->encoding == 3 ) {
            $body = base64_decode($body);
          }
          $body=substr($body,0,1000);
          $result = bmhBodyRules($body,$structure,$this->debug_body_rule);
          break;
        default: // unsupport Content-type
          $this->output( 'Msg #' . $pos . ' is unsupported Content-Type:' . $structure->type,VERBOSE_REPORT);
          return false;
      }
    } else { // internal error
      $this->error_msg = 'Internal Error: unknown type';
      return false;
    }
    $email       = $result['email'];
    $bounce_type = $result['bounce_type'];
    if ( $this->moveHard && $result['remove'] == 1 ) {
      $remove      = 'moved (hard)';
    } elseif ( $this->moveSoft && $result['remove'] == 1 ) {
      $remove      = 'moved (soft)';
    } elseif ( $this->disable_delete ) {
      $remove      = 0;
    } else {
      $remove      = $result['remove'];
    }
    $rule_no     = $result['rule_no'];
    $rule_cat    = $result['rule_cat'];
    $xheader     = false;

    if ($rule_no == '0000') { // internal error      return false;
      // code below will use the Callback function, but return no value
      if ( trim($email) == '' ) {
        $email = $header->fromaddress;
      }
      $params = array($pos,$bounce_type,$email,$subject,$xheader,$remove,$rule_no,$rule_cat,$totalFetched,$body);
      call_user_func_array($this->action_function,$params);
    } else { // match rule, do bounce action
      if ($this->testmode) {
        $this->output('Match: ' . $rule_no . ':' . $rule_cat . '; ' . $bounce_type . '; ' . $email);
        return true;
      } else {
        $params = array($pos,$bounce_type,$email,$subject,$xheader,$remove,$rule_no,$rule_cat,$totalFetchedi,$body);
        return call_user_func_array($this->action_function,$params);
      }
    }
  }

  /**
   * Function to check if a mailbox exists
   * - if not found, it will create it
   * @param string  $mailbox        (the mailbox name, must be in 'INBOX.checkmailbox' format)
   * @param boolean $create         (whether or not to create the checkmailbox if not found, defaults to true)
   * @return boolean
   */
  function mailbox_exist($mailbox,$create=true) {
    if ( trim($mailbox) == '' || !strstr($mailbox,'INBOX.') ) {
      // this is a critical error with either the mailbox name blank or an invalid mailbox name
      // need to stop processing and exit at this point
      echo "Invalid mailbox name for move operation. Cannot continue.<br />\n";
      echo "TIP: the mailbox you want to move the message to must include 'INBOX.' at the start.<br />\n";
      exit();
    }
    $port = $this->port . '/' . $this->service . '/' . $this->service_option;
    $mbox = imap_open('{'.$this->mailhost.":".$port.'}',$this->mailbox_username,$this->mailbox_password,OP_HALFOPEN);
    $list = imap_getmailboxes($mbox,'{'.$this->mailhost.":".$port.'}',"*");
    $mailboxFound = false;
    if (is_array($list)) {
      foreach ($list as $key => $val) {
        // get the mailbox name only
        $nameArr = split('}',imap_utf7_decode($val->name));
        $nameRaw = $nameArr[count($nameArr)-1];
        if ( $mailbox == $nameRaw ) {
          $mailboxFound = true;
        }
      }
      if ( ($mailboxFound === false) && $create ) {
        @imap_createmailbox($mbox, imap_utf7_encode('{'.$this->mailhost.":".$port.'}' . $mailbox));
        imap_close($mbox);
        return true;
      } else {
        imap_close($mbox);
        return false;
      }
    } else {
      imap_close($mbox);
      return false;
    }
  }

  /**
   * Function to delete messages in a mailbox, based on date
   * NOTE: this is global ... will affect all mailboxes except any that have 'sent' in the mailbox name
   * @param string  $mailbox        (the mailbox name)
   * @return boolean
   */
  function globalDelete() {
    $dateArr = split('-', $this->deleteMsgDate); // date format is yyyy-mm-dd
    $delDate = mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]);

    $port  = $this->port . '/' . $this->service . '/' . $this->service_option;
    $mboxt = imap_open('{'.$this->mailhost.":".$port.'}',$this->mailbox_username,$this->mailbox_password,OP_HALFOPEN);
    $list  = imap_getmailboxes($mboxt,'{'.$this->mailhost.":".$port.'}',"*");
    $mailboxFound = false;
    if (is_array($list)) {
      foreach ($list as $key => $val) {
        // get the mailbox name only
        $nameArr = split('}',imap_utf7_decode($val->name));
        $nameRaw = $nameArr[count($nameArr)-1];
        if ( !stristr($nameRaw,'sent') ) {
          $mboxd = imap_open('{'.$this->mailhost.":".$port.'}'.$nameRaw,$this->mailbox_username,$this->mailbox_password,CL_EXPUNGE);
          $messages = imap_sort($mboxd, SORTDATE, 0);
          $i = 0;
          $check = imap_mailboxmsginfo($mboxd);
          foreach ($messages as $message) {
            $header = imap_header($mboxd, $message);
            $fdate  = date("F j, Y", $header->udate);
            // purge if prior to global delete date
            if ( $header->udate < $delDate ) {
              imap_delete($mboxd, $message);
            }
            $i++;
          }
          imap_expunge($mboxd);
          imap_close($mboxd);
        }
      }
    }
    return;
  }

    /* Callback (action) function
   * @param int     $msgnum        the message number returned by Bounce Mail Handler
   * @param string  $bounce_type   the bounce type: 'antispam','autoreply','concurrent','content_reject','command_reject','internal_error','defer','delayed'        => array('remove'=>0,'bounce_type'=>'temporary'),'dns_loop','dns_unknown','full','inactive','latin_only','other','oversize','outofoffice','unknown','unrecognized','user_reject','warning'
   * @param string  $email         the target email address
   * @param string  $subject       the subject, ignore now
   * @param string  $xheader       the XBounceHeader from the mail
   * @param boolean $remove        remove status, 1 means removed, 0 means not removed
   * @param string  $rule_no       Bounce Mail Handler detect rule no.
   * @param string  $rule_cat      Bounce Mail Handler detect rule category.
   * @param int     $totalFetched  total number of messages in the mailbox
   * @return boolean
   */
	static function callbackAction($msgnum, $bounce_type, $email, $subject, $xheader, $remove, $rule_no=false, $rule_cat=false, $totalFetched=0) {
	  global $REX;
	  $bounce_counter = '';

	  if ($remove == true || $remove == '1' ) {
		// increase bounce counter
		$sql = new rex_sql();
		$result = $sql->setQuery("UPDATE " . $REX['ADDON']['newsletter_bounce']['user_table'] . " SET " . $REX['ADDON']['newsletter_bounce']['user_table_bounce_counter_field'] . " = " . $REX['ADDON']['newsletter_bounce']['user_table_bounce_counter_field'] . " + 1 WHERE " . $REX['ADDON']['newsletter_bounce']['user_table_email_field'] . " = '" . $email . "'");

		if ($result) {
			$bounce_counter = ' | BounceCounter++ OK';
		} else {
			$bounce_counter = ' | BounceCounter++ FAILED!';
		}
	  }

	  $displayData = self::prepData($email, $bounce_type, $remove);
	  $bounce_type = $displayData['bounce_type'];
	  $emailName   = $displayData['emailName'];
	  $emailAddy   = $displayData['emailAddy'];
	  $remove      = $displayData['remove'];

	  echo $msgnum . ': '  . $rule_no . ' | '  . $rule_cat . ' | '  . $bounce_type . ' | '  . $remove . ' | ' . $email . ' | '  . $subject . $bounce_counter . "<br />\n";

	  return true;
	}

	/* Function to clean the data from the Callback Function for optimized display */
	static function prepData($email, $bounce_type, $remove) {
	  $data['bounce_type'] = trim($bounce_type);
	  $data['email']       = '';
	  $data['emailName']   = '';
	  $data['emailAddy']   = '';
	  $data['remove']      = '';
	  if ( strstr($email,'<') ) {
		$pos_start = strpos($email,'<');
		$data['emailName'] = trim(substr($email,0,$pos_start));
		$data['emailAddy'] = substr($email,$pos_start + 1);
		$pos_end   = strpos($data['emailAddy'],'>');
		if ( $pos_end ) {
		  $data['emailAddy'] = substr($data['emailAddy'],0,$pos_end);
		}
	  }

	  // replace the < and > able so they display on screen
	  $email = str_replace('<','&lt;',$email);
	  $email = str_replace('>','&gt;',$email);
	  $data['email']     = $email;

	  // account for legitimate emails that have no bounce type
	  if ( trim($bounce_type) == '' ) {
		$data['bounce_type'] = 'none';
	  }

	  // change the remove flag from true or 1 to textual representation
	  if ( stristr($remove,'moved') && stristr($remove,'hard') ) {
		$data['removestat'] = 'moved (hard)';
		$data['remove'] = '<span style="color:red;">' . 'moved (hard)' . '</span>';
	  } elseif ( stristr($remove,'moved') && stristr($remove,'soft') ) {
		$data['removestat'] = 'moved (soft)';
		$data['remove'] = '<span style="color:gray;">' . 'moved (soft)' . '</span>';
	  } elseif ( $remove == true || $remove == '1' ) {
		$data['removestat'] = 'deleted';
		$data['remove'] = '<span style="color:red;">' . 'deleted' . '</span>';
	  } else {
		$data['removestat'] = 'not deleted';
		$data['remove'] = '<span style="color:gray;">' . 'not deleted' . '</span>';
	  }

	  return $data;
	}

}

?>
