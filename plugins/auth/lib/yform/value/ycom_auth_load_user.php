<?php

class rex_yform_value_ycom_auth_load_user extends rex_yform_value_abstract
{
    public function enterObject()
    {
        if (rex_ycom_auth::getUser() && !$this->params['send']) {
            $fields = $this->getElement(2);
            if ('' != $fields) {
                $fields = explode(',', $this->getElement(2));
            } else {
                $fields = [];
            }
            foreach ($this->params['values'] as $o) {
                if ((0 == count($fields) || in_array($o->getName(), $fields)) && $o->getName() != $this->getName()) {
                    $o->setValue(@rex_ycom_auth::getUser()->getValue($o->getName()));
                }
            }
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_load_user -> Beispiel: ycom_auth_load_user|label|opt:field1,field2,field3';
    }
}
