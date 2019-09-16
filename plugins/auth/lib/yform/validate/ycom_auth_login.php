<?php

class rex_yform_validate_ycom_auth_login extends rex_yform_validate_abstract
{
    public function enterObject()
    {
        if (rex::isBackend()) {
            $this->params['warning'][] = 1;
            $this->params['warning_messages'][] = rex_i18n::translate($this->getElement(4));
            return;
        }

        $vars = [];

        $e = explode(',', $this->getElement(2));
        foreach ($e as $v) {
            $w = explode('=', $v);
            $label = $w[0];
            $value = trim(rex_request($w[1], 'string', ''));
            $vars[$label] = $value;
        }

        $filter = null;
        if ('' != $this->getElement(3)) {
            $filter_query = $this->getElement(3);
            $filter = static function (rex_yform_manager_query $query) use ($filter_query) {
                $query->whereRaw($filter_query);
            };
        }

        rex_ycom_auth::clearUserSession();
        rex_ycom_auth::loginWithParams($vars, $filter);

        if (!rex_ycom_auth::getUser()) {
            $this->params['warning'][] = 1;
            $this->params['warning_messages'][] = rex_i18n::translate($this->getElement(4));
            rex_ycom_auth::clearUserSession();
        } else {
            // Load fields for eMail or DB
            $fields = $this->getElement(5);
            if ('' != $fields) {
                $fields = explode(',', $fields);
                foreach ($fields as $field) {
                    $this->params['value_pool']['email'][$field] = rex_ycom_auth::getUser()->getValue($field);
                    if ('no_db' != $this->getElement(6)) {
                        $this->params['value_pool']['sql'][$field] = rex_ycom_auth::getUser()->getValue($field);
                    }
                }
            }
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_login -> prüft ob leer, beispiel: validate|ycom_auth_login|label1=request1,label2=request2|status>0|warning_message|opt:load_field1,load_field2,load_field3|[no_db] ';
    }
}
