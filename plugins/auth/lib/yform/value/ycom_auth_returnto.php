<?php

/**
 * ycom.
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_yform_value_ycom_auth_returnto extends rex_yform_value_abstract
{
    public function enterObject()
    {
        $returnTo = $this->getValue();

        $returnTos[] = (string) $returnTo;
        $returnTos[] = rex_request('returnTo', 'string', '');
        $returnTos[] = $this->getElement(3);
        $returnTos[] = rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_ok'), '', [], '&');
        $allowedDomains = ('' != $this->getElement(2)) ? explode(',', $this->getElement(2)) : [];
        $returnTo = rex_ycom_auth::getReturnTo($returnTos, $allowedDomains);

        $this->setValue($returnTo);

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.hidden.tpl.php');
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
    }

    public function executeAction()
    {
        if ('' != $this->getValue()) {
            header('Location: ' . $this->getValue());
            $this->params['form_exit'] = true;
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_returnto|label|[allowed domains: DomainA,DomainB]|[URL]';
    }

    public static function ycom_auth_returnto_ReturnTo()
    {
    }
}
