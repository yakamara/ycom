<?php

namespace Yakamara\YCom\Auth;

use Redaxo\Core\Base\SingletonTrait;
use Redaxo\Core\Core;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;
use Redaxo\Core\Database\Sql;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Util\Type;
use Yakamara\YCom\Config;
use Yakamara\YCom\User;

use function count;
use function session_id;
use function time;

/**
 * The `rex_ycom_user_session` table: one row per logged-in browser session.
 *
 * Sessions are tracked in the database rather than in PHP's session store alone, so the backend can
 * list and revoke them and so "stay logged in" cookies can be tied to a specific session.
 */
final class UserSession
{
    use SingletonTrait;

    public function storeCurrentSession(User $user, ?string $cookieKey = null): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setValue('session_id', $sessionId)
            ->setValue('cookie_key', $cookieKey)
            ->setValue('user_id', $user->getId())
            ->setValue('ip', Request::server('REMOTE_ADDR', 'string', ''))
            ->setValue('useragent', Request::server('HTTP_USER_AGENT', 'string', ''))
            ->setValue('starttime', Sql::datetime(time()))
            ->setValue('last_activity', Sql::datetime(time()))
            ->insertOrUpdate();
    }

    /** @return array<string, mixed>|null */
    public function getCurrentSession(User $user): ?array
    {
        $sessions = Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere('session_id = ? AND user_id = ?', [session_id(), $user->getId()])
            ->select()
            ->getArray();

        return 1 > count($sessions) ? null : $sessions[0];
    }

    public function clearCurrentSession(): self
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return $this;
        }

        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere('session_id = ?', [$sessionId])
            ->delete();

        return $this;
    }

    public function updateLastActivity(User $user): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setValue('session_id', $sessionId)
            ->setValue('user_id', $user->getId())
            ->setValue('ip', Request::server('REMOTE_ADDR', 'string', ''))
            ->setValue('useragent', Request::server('HTTP_USER_AGENT', 'string', ''))
            ->setValue('last_activity', Sql::datetime(time()))
            ->insertOrUpdate();
    }

    public function setOTPverified(User $user, ?string $sessionId = null): void
    {
        if (null === $sessionId) {
            $sessionId = session_id();
            if (false === $sessionId || '' === $sessionId) {
                return;
            }
        }

        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setValue('otp_verified', 1)
            ->setWhere('session_id = :session_id AND user_id = :user_id', [
                'session_id' => $sessionId,
                'user_id' => $user->getId(),
            ])
            ->update();
    }

    /**
     * Removes sessions that timed out.
     *
     * Sessions without a "stay logged in" cookie expire after `session_duration` of inactivity or
     * `session_max_overall_duration` overall; sessions with one live for `auth_cookie_ttl` days.
     */
    public static function clearExpiredSessions(): void
    {
        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere(
                '(UNIX_TIMESTAMP(last_activity) < :last OR UNIX_TIMESTAMP(starttime) < :start) AND cookie_key IS NULL',
                [
                    'last' => time() - (int) Config::get('session_duration', 3600),
                    'start' => time() - (int) Config::get('session_max_overall_duration', 21600),
                ],
            )
            ->delete();

        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere(
                'cookie_key IS NOT NULL AND UNIX_TIMESTAMP(last_activity) < :last',
                ['last' => time() - ((int) Config::get('auth_cookie_ttl', 7) * 24 * 60 * 60)],
            )
            ->delete();
    }

    public function removeSession(string $sessionId, string $userId): bool
    {
        return Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere('session_id = ? AND user_id = ?', [$sessionId, $userId])
            ->delete()
            ->getRows() > 0;
    }

    public function removeSessionsExceptCurrent(int $userId): void
    {
        $sessionId = session_id();
        if (false === $sessionId || '' === $sessionId) {
            return;
        }

        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere('session_id != ? AND user_id = ?', [$sessionId, $userId])
            ->delete();
    }

    /**
     * Keeps the stored session id in sync when core regenerates it (login, privilege change).
     */
    public static function sessionRegenerated(ExtensionPoint $ep): void
    {
        Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->setWhere('session_id = :my_session_id', ['my_session_id' => Type::string($ep->getParam('previous_id'))])
            ->setValue('session_id', Type::string($ep->getParam('new_id')))
            ->update();
    }

    public static function deleteAllSessions(): int
    {
        return Sql::factory()
            ->setTable(Core::getTable('ycom_user_session'))
            ->delete()
            ->getRows();
    }
}
