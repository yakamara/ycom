<?php

$info = array();
$error = array();

$tables = rex_com_newsletter::getAvailableTables();

if(count($tables) > 0) {

  $table = rex_request("nl_table","string");
  if(!in_array($table,$tables)) {
    $table = current($tables);
  }

  $tableselect = new rex_select();
	$tableselect->setName('nl_table');
	$tableselect->setSize(1);
	foreach($tables as $t) {
		$tableselect->addOption($I18N->msg($t).' ['.$t.']',$t);
	}

	$tableselect->setSelected($table);
  $tableselect = $tableselect->get();

} else {
  $tableselect = $I18N->msg("com_newsletter_no_table_available");
  $error[] = $I18N->msg("com_newsletter_no_table_available",implode(", ",rex_com_newsletter::getNeededColumns()));

}






$method = rex_request("method","string");
$method_all = rex_request("method_all","string","");


$nl_id = rex_request("nl_id","string");
if ($nl_id == "") {
  $nl_id = date("YmdHi");
}

$nl_filter_status = rex_request("nl_filter_status","int",1);
if($nl_filter_status != 1) {
  $nl_filter_status = 0;
}

$nl_groups = rex_request("nl_groups" ,"array");

  
// -------- E-Mail Typ / ob REDAXO oder XFORM

$nl_type = rex_request("nl_type","string");
if($nl_type != "xform") {
  $nl_type = "redaxo";
}

// -------- REDAXO article

$redaxo_nl_article_id = rex_request("redaxo_nl_article_id","int");
$redaxo_nl_article_name = "";
$redaxo_nl_article_link = "";
if($redaxo_nl_article_id > 0 && $m = OOArticle::getArticleById($redaxo_nl_article_id)) {
  $redaxo_nl_article_name = $m->getName();
  $redaxo_nl_article_link = rex_getUrl($redaxo_nl_article_id);
} else {
  $redaxo_nl_article_id = 0;  
}
$redaxo_nl_from_email = rex_request("redaxo_nl_from_email","string");
$redaxo_nl_from_name = rex_request("redaxo_nl_from_name","string");
$redaxo_nl_subject = rex_request("redaxo_nl_subject","string");


// -------- xform Template

$xform_nl_tpl = "";
$xform_nl_tpl_tmp = rex_request("xform_nl_tpl","string");
$xform_nl_sql = new rex_sql;
$xform_nl_sql->setQuery("select * from rex_xform_email_template");
$xform_nl_tpls = $xform_nl_sql->getArray();

$xform_nl_select = new rex_select;
$xform_nl_select->setName("xform_nl_tpl");
foreach($xform_nl_tpls as $tpl) {
  $xform_nl_select->addOption($tpl["name"],$tpl["name"]);
  if($xform_nl_tpl_tmp == $tpl["name"]) {
    $xform_nl_tpl = $tpl;
    $xform_nl_select->setSelected($tpl["name"]);  
  }
}


// -------- Testuser ID

$test_user_id = rex_request("test_user_id","int",0);
if ($test_user_id > 0) {
  $gu = new rex_sql();
  // $gu->debugsql = 1;
  $gu->setQuery('select * from `'.$table.'` where id='.$test_user_id);
  $test_users = $gu->getArray();
  if(count($test_users) == 1) {
    $test_user = $test_users[0];
  }
}


$send = FALSE;

// -------------------------------- 

if($method != "") {
  if($nl_type == "xform") {

    // xform
    if($xform_nl_tpl != "") {
      $nl_from_email = $xform_nl_tpl['mail_from'];
      $nl_from_name = $xform_nl_tpl['mail_from_name'];
      $nl_subject = $xform_nl_tpl['subject'];
      $nl_body_text = $xform_nl_tpl['body'];
      $nl_body_html = $xform_nl_tpl['body_html'];
      $nl_attachments = NULL;
      if($xform_nl_tpl['attachments'] != "") {
        $nl_attachments = array();
        foreach(explode(",",$xform_nl_tpl['attachments']) as $attachment) {
        	$nl_attachments[$attachment] = $REX["INCLUDE_PATH"].'/../../files/'.$attachment;
        }  
      }
      $send = TRUE;
      
    }else {
      $error[] = $I18N->msg("com_newsletter_error_xformtemplate");;
    }
  
  }else {
  
    // redaxo
    $nl_from_email = $redaxo_nl_from_email;
    $nl_from_name = $redaxo_nl_from_name;
    $nl_subject = $redaxo_nl_subject;
    $nl_attachments = array();

    if($nl_from_email == "" || $nl_from_name == "" || $nl_subject == "" || $redaxo_nl_article_id == 0) {
      $error[] = $I18N->msg("com_newsletter_error_checkinfo");

    }else {
          
      $tmp_redaxo = $REX['REDAXO'];
    
       // ***** HTML
      $REX['REDAXO'] = true;
      $REX_ARTICLE = new rex_article($redaxo_nl_article_id,0);
      $REX['ADDON']['NEWSLETTER_TEXT'] = FALSE;
      $nl_body_html = $REX_ARTICLE->getArticleTemplate();
    
      // ***** TEXT
      $REX['REDAXO'] = true;
      $REX_ARTICLE = new rex_article($redaxo_nl_article_id,0);
      $REX['ADDON']['NEWSLETTER_TEXT'] = TRUE;
      $nl_body_text = $REX_ARTICLE->getArticle();
      $nl_body_text = strip_tags($nl_body_text);
      $nl_body_text = html_entity_decode($nl_body_text);
      
      $REX['REDAXO'] = $tmp_redaxo;
    
      $send = TRUE;
    }
  
  }
  
  if(isset($nl_body_html)) {
    $nl_body_html = rex_register_extension_point('COM_NEWSLETTER_SEND_HTML', $nl_body_html, array());
  }
  
  if(isset($nl_body_text)) {
    $nl_body_text = rex_register_extension_point('COM_NEWSLETTER_SEND_TEXT', $nl_body_text, array());
  }
  
}

// ---------- Testmail

if($method == "start" && $method_all != "all" && count($error) == 0 && $send)
{
  if($test_user_id == 0) {
    $error[] = $I18N->msg("com_newsletter_user_doesnt_exist");

  }else {
  
    $nl = new rex_com_newsletter();
    $nl->setSubject($nl_subject);
    $nl->setFrom($nl_from_email,$nl_from_name);
    $nl->setHTMLBody($nl_body_html);
    $nl->setTextBody($nl_body_text);
    $nl->setAttachment($nl_attachments);
    $nl->setReplace(array("newsletter_table" => $table, 'article_id' => $redaxo_nl_article_id, 'article_name' => $redaxo_nl_article_name, 'article_link' => $redaxo_nl_article_link));
    
    if($nl->sendToUser($test_user)) {
      $info[] = $I18N->msg("com_newsletter_info_testmail_ok");

    }else {
      $error[] = $I18N->msg("com_newsletter_info_testmail_failed");
      
    }
  
  }
}

// ---------- Versand an alle
if($method == "start" && $method_all == "all" && count($error) == 0 && $send) {


	$qry_groups = '';
	if(count($nl_groups) > 0) {
		$qry_groups = array();
		foreach($nl_groups as $group) {
			$qry_groups[] = 'FIND_IN_SET('.$group.',rex_com_group) > 0';
		}
		$qry_groups = 'AND ( '. implode(' OR ', $qry_groups) .')';
	}
	
  $users = new rex_sql;
  // $nl->debugsql = 1;
  $users->setQuery('select * from `'.$table.'` where (newsletter_last_id <> "'.$nl_id.'" OR newsletter_last_id IS NULL) '.$qry_groups.' and email<>"" and email IS NOT NULL and newsletter=1 and status='.$nl_filter_status.' LIMIT 50');
  
  if($users->getRows()>0) {
  
    $i = $I18N->msg("com_newsletter_send_reload",date("H:i:s"));

    ?><script>
    function win_reload(){ window.location.reload(); }
    setTimeout("win_reload()",5000); // Millisekunden 1000 = 1 Sek * 80
    </script><?php

    $i = array(); 

    $nl = new rex_com_newsletter();
    $nl->setSubject($nl_subject);
    $nl->setFrom($nl_from_email,$nl_from_name);
    $nl->setHTMLBody($nl_body_html);
    $nl->setTextBody($nl_body_text);
    $nl->setAttachment($nl_attachments);
    $nl->setReplace(array("newsletter_table" => $table, 'article_id' => $redaxo_nl_article_id, 'article_name' => $redaxo_nl_article_name, 'article_link' => $redaxo_nl_article_link));

    foreach($users->getArray() as $user) {

      if($nl->sendToUser($user)) {
      } else {
      }

      $i[] = $user["email"];
      
      $up = new rex_sql;
      $up->setQuery('update `'.$table.'` set newsletter_last_id="'.mysql_real_escape_string($nl_id).'" where id='.$user["id"]);

    }
    
    $info[] = $I18N->msg("com_newsletter_send_to", implode(",",$i));;
    

  }else {
    $info[] = $I18N->msg("com_newsletter_send_all");

  } 

}

if (count($error)>0) {
  foreach($error as $e) {
    echo rex_warning($e);
  }   
}

if (count($info)>0) {
  foreach($info as $i) {
    echo rex_info($i);
  }
}

?>



<table class="rex-table" cellpadding="5" cellspacing="1">

  <form action="index.php" method="get" name="REX_FORM">
  <input type="hidden" name="page" value="community" />
  <input type="hidden" name="subpage" value="plugin.newsletter" />
  <input type="hidden" name="method" value="start" />

  <tr>
    <th class="rex-icon">&nbsp;</th>
    <th colspan="2"><b><?php echo $I18N->msg("com_newsletter_type"); ?></b></th>
  </tr>
  <tr>
    <td class="rex-icon"><input type="radio" name="nl_type" id="nl_type_redaxo" value="article" <?php if($nl_type != "xform") echo 'checked="checked"'; ?> /></td>
    <td width="200"><label for="nl_type_redaxo"><?php echo $I18N->msg("com_newsletter_article"); ?></label></td>
    <td>
      <div class="rex-wdgt">
      <div class="rex-wdgt-lnk">
      <p>
        <input type="hidden" name="redaxo_nl_article_id" id="LINK_1" value="<?php echo $redaxo_nl_article_id; ?>" />
        <input type="text" size="30" name="redaxo_nl_article_name" value="<?php echo stripslashes(htmlspecialchars($redaxo_nl_article_name)); ?>" id="LINK_1_NAME" readonly="readonly" />
        <a href="#" onclick="openLinkMap('LINK_1', '&clang=0');return false;" tabindex="23"><img src="media/file_open.gif" width="16" height="16" alt="Open Linkmap" title="Open Linkmap" /></a>
        <a href="#" onclick="deleteREXLink(1);return false;" tabindex="24"><img src="media/file_del.gif" width="16" height="16" title="Remove Selection" alt="Remove Selection" /></a>
      </p>
      </div>
      </div>
    </td>
  </tr>

  <tr>
    <td class=rex-icon>&nbsp;</td>
    <td><?php echo $I18N->msg("com_newsletter_from_email"); ?></td>
    <td><input type="text" size="30" name="redaxo_nl_from_email" value="<?php echo stripslashes(htmlspecialchars($redaxo_nl_from_email)); ?>" class="inp100" /></td>
  </tr>
  <tr>
    <td class=rex-icon>&nbsp;</td>
    <td><?php echo $I18N->msg("com_newsletter_from_name"); ?></td>
    <td><input type="text" size="30" name="redaxo_nl_from_name" value="<?php echo stripslashes(htmlspecialchars($redaxo_nl_from_name)); ?>" class="inp100" /></td>
  </tr>
  <tr>
    <td class=rex-icon>&nbsp;</td>
    <td><?php echo $I18N->msg("com_newsletter_subject"); ?></td>
    <td><input type="text" size="30" name="redaxo_nl_subject" value="<?php echo stripslashes(htmlspecialchars($redaxo_nl_subject)); ?>" />
    <br /><?php echo $I18N->msg("com_newsletter_subject_info"); ?>
    </td>
  </tr>
  
  <tr>
    <td class="rex-icon"><input type="radio" name="nl_type" id="nl_type_xform" value="xform" <?php if($nl_type == "xform") echo 'checked="checked"'; ?> /></td>
    <td width="200"><label for="nl_type_xform"><?php echo $I18N->msg("com_newsletter_xform_templates"); ?></label></td>
    <td><?php echo $xform_nl_select->get(); ?></td>
  </tr>

  <tr>
    <th class="rex-icon">&nbsp;</th>
    <th colspan="2"><b><?php echo $I18N->msg("com_newsletter_willbesend_to"); ?></b></th>
  </tr>

  <tr>
    <td></td>
    <td width="200"><label for="nl_type_xform"><?php echo $I18N->msg("com_newsletter_use_this_table"); ?></label></td>
    <td><?php echo $tableselect; ?></td>
  </tr>
  
  <tr>
    <td class=rex-icon>&nbsp;</td>
    <td><?php echo $I18N->msg("com_newsletter_id"); ?></td>
    <td><input type="text" size="30" name="nl_id" value="<?php echo stripslashes(htmlspecialchars($nl_id)); ?>" class="inp100" />
     <?php echo $I18N->msg("com_newsletter_id_info"); ?><br /></td>
  </tr>
  
<?php 
//
// Generating Selectbox for User-Groups
//
if(OOPlugin::isAvailable('community','group') && $table = "rex_com_user")
{
	$sql = new rex_sql();
	$sql->setQuery('SELECT id,name FROM rex_com_group');

	$groupselect = new rex_select();
	$groupselect->setName('nl_groups[]');
	$groupselect->setSize(6);
	$groupselect->setMultiple(true);
	foreach($sql->getArray() as $group) {
		$groupselect->addOption($group['name'].' ['.$group['id'].']',$group['id']);
	}

	$groupselect->setSelected($nl_groups);

  echo '
    <tr>
     <td class=rex-icon>&nbsp;</td>
     <td>'.$I18N->msg("com_newsletter_in_group").'</td>
     <td>'.$groupselect->get().'<p>'.$I18N->msg("com_newsletter_nogroup_info").'</td>
    </tr>';
}

?>
  
  <tr>
    <td class="rex-icon"></td>
    <td align="right"><input type="checkbox" name="nl_filter_status" id="nl_filter_status" value="1" <?php if($nl_filter_status == "1") echo 'checked="checked"'; ?> /></td>
    <td><label for="nl_filter_status"><?php echo $I18N->msg("com_newsletter_filter_status_active_info"); ?></label></td>
    <td></td>
  </tr>
  
  <tr>
    <th class=rex-icon>&nbsp;</th>
    <th colspan=2><b><?php echo $I18N->msg("com_newsletter_enter_testmail"); ?></b></th>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td><?php echo $I18N->msg("com_newsletter_testuser_id"); ?></td>
    <td><input type="text" size="30" name="test_user_id" value="<?php echo stripslashes(htmlspecialchars($test_user_id)); ?>" /></td>
  </tr>
  
  <?php if ($method == "start" && count($error) == 0) { ?>
    <tr>
      <td>&nbsp;</td>
      <td><?php echo $I18N->msg("com_newsletter_testmail_ok_info"); ?></td>
      <td><input type="checkbox" name="method_all" value="all" /></td>
    </tr>
  <?php } ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" value="<?php echo $I18N->msg("com_newsletter_send_button"); ?>" class="submit" /></td>
  </tr>
  </form>
</table>


