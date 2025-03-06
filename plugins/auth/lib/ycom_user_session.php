<?php

class rex_ycom_user_session
{
    use rex_singleton_trait;

    /**
     * @param rex_ycom_user|rex_yform_manager_dataset $user
     * @throws rex_exception
     * @throws rex_sql_exception
     */
    public function storeCurrentSession($user, ?string $cookieKey = null): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setValue('session_id', session_id())
            ->setValue('cookie_key', $cookieKey)
            ->setValue('user_id', $user->getId())
            ->setValue('ip', rex_request::server('REMOTE_ADDR', 'string'))
            ->setValue('useragent', rex_request::server('HTTP_USER_AGENT', 'string'))
            ->setValue('starttime', rex_sql::datetime(time()))
            ->setValue('last_activity', rex_sql::datetime(time()))
            ->insertOrUpdate();
    }

    /**
     * @param rex_ycom_user|rex_yform_manager_dataset $user
     * @throws rex_exception
     * @throws rex_sql_exception
     */
    public function getCurrentSession($user): ?array
    {
        $Sessions = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('session_id = ? and user_id = ?', [session_id(), $user->getId()])
            ->select()
            ->getArray();
        return (0 == count($Sessions)) ? null : $Sessions[0];
    }

    public function clearCurrentSession(): self
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return $this;
        }

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('session_id = ?', [session_id()])
            ->delete();
        return $this;
    }

    public function updateLastActivity(rex_ycom_user $user): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setValue('session_id', session_id())
            ->setValue('user_id', $user->getId())
            ->setValue('ip', rex_request::server('REMOTE_ADDR', 'string'))
            ->setValue('useragent', rex_request::server('HTTP_USER_AGENT', 'string'))
            ->setValue('last_activity', rex_sql::datetime(time()))
        ->insertOrUpdate();
    }

    public function setOTPverified(rex_ycom_user $user, $sessionId = null): void
    {
        if (null === $sessionId) {
            $sessionId = session_id();
            if (false === $sessionId || '' === $sessionId) {
                return;
            }
        }

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setValue('otp_verified', 1)
            ->setWhere('session_id = :session_id and user_id = :user_id', ['session_id' => $sessionId, 'user_id' => $user->getId()])
            ->update();
    }

    public static function clearExpiredSessions(): void
    {
        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('(UNIX_TIMESTAMP(last_activity) < :last or UNIX_TIMESTAMP(starttime) < :start) AND cookie_key IS NULL', [
                ':last' => (time() - ((int) rex_ycom_config::get('session_duration', 3600))),
                ':start' => (time() - ((int) rex_ycom_config::get('session_max_overall_duration', 21600))),
            ])
            ->delete();

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('cookie_key IS NOT NULL')
            ->setWhere(' (UNIX_TIMESTAMP(last_activity)   ) < :last', [
                ':last' => (time() - ((int) rex_ycom_config::get('auth_cookie_ttl', 7) * 24 * 60 * 60)),
            ])
            ->delete();
    }

    public function removeSession(string $sessionId, string $userId): bool
    {
        $sql = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('session_id = ? and user_id = ?', [$sessionId, $userId])
            ->delete();
        return $sql->getRows() > 0;
    }

    public function removeSessionsExceptCurrent(int $userId): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('session_id != ? and user_id = ?', [$sessionId, $userId])
            ->delete();
    }

    /**
     * @param rex_extension_point<null> $ep
     */
    public static function sessionRegenerated(rex_extension_point $ep): void
    {
        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('session_id = :my_session_id', [':my_session_id' => rex_type::string($ep->getParam('previous_id'))])
            ->setValue('session_id', rex_type::string($ep->getParam('new_id')))
            ->update();
    }

    public static function deleteAllSessions(): bool
    {
        $sql = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->delete();
        return $sql->getRows() > 0;
    }
}
