<?php

class rex_ycom_user_session
{
    use rex_singleton_trait;

    public function storeCurrentSession(rex_ycom_user $user): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        // $userId = rex_ycom_auth::getSessionVar(rex_login::SESSION_IMPERSONATOR, null);
        // if (null === $userId) {
        //     $userId = rex_ycom_auth::getSessionVar(rex_login::SESSION_USER_ID);
        // }

        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setValue('session_id', session_id())
            ->setValue('user_id', $user->getId())
            ->setValue('ip', rex_request::server('REMOTE_ADDR', 'string'))
            ->setValue('useragent', rex_request::server('HTTP_USER_AGENT', 'string'))
            ->setValue('starttime', rex_sql::datetime(time()))
            ->setValue('last_activity', rex_sql::datetime(time()))
            ->insertOrUpdate();

        // $login->setSessionVar(self::SESSION_VAR_LAST_DB_UPDATE, time());
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

    public static function clearExpiredSessions(): void
    {
        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_session'))
            ->setWhere('(UNIX_TIMESTAMP(last_activity) < :last or UNIX_TIMESTAMP(starttime) < :start)', [
                ':last' => (time() - ((int) rex_plugin::get('ycom','auth')->getConfig('session_duration', 3600))),
                ':start' => (time() - ((int) rex_plugin::get('ycom','auth')->getConfig('session_max_overall_duration', 21600)))
            ])
            ->delete();
    }

    public function removeSession(string $sessionId, int $userId): bool
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
}
