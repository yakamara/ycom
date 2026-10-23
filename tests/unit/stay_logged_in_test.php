<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_stay_logged_in_test extends TestCase
{
    private ?rex_ycom_user $user = null;
    private mixed $cookieTtl = null;

    protected function setUp(): void
    {
        $this->cookieTtl = rex_plugin::get('ycom', 'auth')->getConfig('auth_cookie_ttl');

        $user = rex_ycom_user::createUserByEmail(['email' => 'stay-logged-in-' . bin2hex(random_bytes(4)) . '@example.org']);
        self::assertInstanceOf(rex_ycom_user::class, $user);
        $this->user = $user;
    }

    protected function tearDown(): void
    {
        if (null !== $this->user) {
            rex_sql::factory()->setQuery('DELETE FROM ' . rex::getTable('ycom_user_session') . ' WHERE user_id = ?', [$this->user->getId()]);
            $this->user->delete();
        }

        $plugin = rex_plugin::get('ycom', 'auth');
        null === $this->cookieTtl ? $plugin->removeConfig('auth_cookie_ttl') : $plugin->setConfig('auth_cookie_ttl', $this->cookieTtl);
        $this->resetConfigCache();
    }

    public function testStayLoggedInIsIgnoredWhenCookieTtlIsInactive(): void
    {
        self::assertNull($this->loginWithStayAndGetCookieKey(0));
    }

    public function testStayLoggedInStoresCookieKeyWhenCookieTtlIsActive(): void
    {
        self::assertNotNull($this->loginWithStayAndGetCookieKey(7));
    }

    private function loginWithStayAndGetCookieKey(int $cookieTtl): ?string
    {
        rex_plugin::get('ycom', 'auth')->setConfig('auth_cookie_ttl', $cookieTtl);
        $this->resetConfigCache();

        $status = rex_ycom_auth::login([
            'loginName' => (string) $this->user->getValue((string) rex_ycom_config::get('login_field', 'email')),
            'loginPassword' => '',
            'filter' => [],
            'loginStay' => true,
            'ignorePassword' => true,
        ]);
        self::assertSame(rex_ycom_auth::STATUS_HAS_LOGGED_IN, $status);

        $session = rex_sql::factory()->getArray(
            'SELECT cookie_key FROM ' . rex::getTable('ycom_user_session') . ' WHERE user_id = ?',
            [$this->user->getId()],
        );
        self::assertCount(1, $session);

        return $session[0]['cookie_key'];
    }

    private function resetConfigCache(): void
    {
        $property = new ReflectionProperty(rex_ycom_config::class, 'config');
        $property->setValue(null, null);
    }
}
