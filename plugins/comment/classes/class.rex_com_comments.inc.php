<?php

class rex_com_comments {
	
	private $ckey = "none",
			$showAddForm = false,
			$debug = false,
			$pageLink = '',
			$defaultUserImage = '',
			$ArticleName = ''
			;
	
	public function setCommentKey($ckey = 'none')
	{
		$this->ckey = $ckey;
	}

	public function setShowAddForm($show = true)
	{
		$this->showAddForm = $show;
	}
	
	public function setShowComments($show = true)
	{
		$this->showComments = $show;
	}	

	public function setPageLink($pageLink = "")
	{
		$this->pageLink = $pageLink;
	}

	public function setDefaultUserImage($defaultUserImage = "")
	{
		$this->defaultUserImage = $defaultUserImage;
	}

	public function setArticleName($ArticleName = "")
	{
		$this->ArticleName = $ArticleName;
	}

	public function getArticleName()
	{
		return $this->ArticleName;
	}

	public function getDefaultUserImage()
	{
		return $this->defaultUserImage;
	}

	public function getCommentKey()
	{
		return $this->ckey;
	}

	public function getPageLink()
	{
		return $this->pageLink;
	}

	public function getComments()
	{
		$comments = array();
		$cs = rex_sql::factory();
		if($this->debug)
			$cs->debugsql = 1;
		$cs->setQuery('select * from rex_com_comment where ckey="'.addslashes($this->getCommentKey()).'" order by create_datetime');
		foreach($cs->getArray() as $c)
		{
			if(($comment = rex_com_comment::get($c)) && $comment)
			{
				$comment->setDefaultUserImage($this->getDefaultUserImage());
				$comments[] = $comment;
					
			}
		}
		return $comments;
	}

	public function getCommentsView()
	{
		global $REX,$I18N;
		$return = '';
		
		// Show AddForm
		$add_view = '';
		if($this->showAddForm)
		{	
			$xform = new rex_xform;
			
			$xform->setObjectparams("form_action",$this->getPageLink().'#reply-title');
			$xform->setObjectparams("submit_btn_label",$I18N->msg('com_comment_submitcomment'));

			$xform->setValueField("textarea",array("comment","translate:com_comment_name"));
			$xform->setValidateField("empty",array("comment","translate:com_comment_enter_comment"));
			$xform->setValueField("text",array("email","translate:email"));
			$xform->setValidateField("type",array("email","email","translate:com_comment_enteremail","0"));
			$xform->setValueField("text",array("name","translate:name"));
			$xform->setValidateField("empty",array("name","translate:com_comment_enter_name"));

			// $xform->setValueField("be_manager_relation",array("user_id","translate:com_user","rex_com_user","name","0","1","","",""));

			$xform->setValueField("text",array("www","translate:com_comment_www"));
			$xform->setValueField("datestamp",array("create_datetime","mysql","","1"));
			$xform->setValueField("datestamp",array("update_datetime","mysql","","0"));

			$xform->setValueField("hidden",array("status",1));
			$xform->setValueField("hidden",array("ckey",$this->getCommentKey()));

			// $xform->setValueField("checkbox",array("info_email","translate:com_comment_infomail","","0"));
			$xform->setValueField("index",array("ukey","email,user_id,name,comment,ckey,www","","sha1"));

			$reply_to = rex_request("rex_com_comment_replyto",'int',0);
			if($c = new rex_com_comment($reply_to) && !isset($c))
			{
				$reply_to = 0;
			}
			$xform->setValueField("hidden",array("reply_to",$reply_to));

			$xform->setValueField("captcha",array("translate:com_comment_captcha","translate:com_comment_enter_captcha"));

			$xform->setActionField("db",array('rex_com_comment'));
			$xform->setActionField("showtext",array('translate:com_comment_thankyourforentry','<div class="comment-add-answer">','</div>',2));

			$add_view = '<div id="comment-form-'.$this->getCommentKey().'" class="commentform"><h3 id="reply-title">'.$I18N->msg('com_comment_addcomment').'</h3>'.$xform->getForm().'</div>';
			
		}


		// Show Comments

		$comments = $this->getComments();
		$return = '<h2 id="comments-title">'.$I18N->msg('com_comment_commentsto',count($comments),$this->getArticleName()).'</h2>';

		$comments_view = '';
		foreach($comments as $comment)
		{
			$comment->setPageLink($this->getPageLink());

			$comment_view = '';
			$comment_view = $comment->getCommentView();
			$comment_view = '<li id="li-comment-'.$comment->getId().'" class="comment depth-'.$comment->getDepth().'">'.$comment_view.'</li>';
			$comments_view .= $comment_view;
		}

		if($comments_view != "")
			$comments_view = '<ol id="commentList">'.$comments_view.'</ol>';	
		

		// Out

		$return .= $comments_view.$add_view;
		$return = '<div class="comments">'.$return.'</div>';
		
		return $return;	
	}
	
	
	
	
	
}