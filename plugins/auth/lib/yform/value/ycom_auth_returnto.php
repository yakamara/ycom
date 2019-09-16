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

        if ('' == $returnTo) {
            $returnTo = rex_request('returnTo', 'string', '');
        }

        if ('' == $returnTo) {
            $returnTo = $this->getElement(3);
        }

        if ('' == $returnTo) {
            $returnTo = rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_ok'), '', [], '&');
        }

        if ('' != $returnTo) {
            if( !preg_match('/http(s?)\:\/\//i', $returnTo) ) {
                $returnTo = rex_yrewrite::getFullPath( (substr($returnTo,0,1) == '/' ? substr($returnTo, 1) : $returnTo ) );
            }
        }

        if ('' != $this->getElement(2)) {
            $domains = explode(',', $this->getElement(2));

            foreach(rex_yrewrite::getDomains() as $ydomain) {
                $domains[] = $ydomain->getUrl();
            }

            $treturnTo = $returnTo;
            $returnTo = '';
            foreach ($domains as $domain) {
                if (substr($treturnTo,0, strlen($domain)) == $domain) {
                    $returnTo = $treturnTo;
                }
            }
        }

        $this->setValue($returnTo);

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.hidden.tpl.php');
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();

    }

    public function executeAction()
    {

        if ('' != $this->getValue()) {
            rex_response::cleanOutputBuffers();
            header('Location: ' . $this->getValue());
            // no exit(); because there could be other actions and also post action triggers.
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_returnto|label|[allowed domains: DomainA,DomainB]|[URL]';
    }
}
