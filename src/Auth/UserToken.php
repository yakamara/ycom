<?php

namespace Yakamara\YCom\Auth;

use Redaxo\Core\Base\SingletonTrait;
use Redaxo\Core\Core;
use Redaxo\Core\Database\Sql;
use Redaxo\Core\Exception\RuntimeException;
use Symfony\Component\String\ByteString;
use Throwable;

use function base64_encode;
use function count;
use function hash_equals;
use function hash_hmac;
use function substr;
use function time;

/**
 * Single-use tokens for magic links: registration confirmation, password reset, direct login.
 *
 * A token is a selector plus a verifier. Only the *hash* of both is stored, so a stolen database row
 * cannot be turned back into a usable link.
 */
final class UserToken
{
    use SingletonTrait;

    private const int VERIFIER_LENGTH = 20;

    private ?int $userId = null;
    private ?string $email = null;
    private ?string $type = null;
    private ?string $selector = null;
    private ?string $verifier = null;
    private ?string $hash = null;
    private ?string $createDate = null;
    private ?string $expireDate = null;
    private ?string $token = null;

    /**
     * @throws RuntimeException when the token is unknown, expired or does not verify
     */
    public function validateToken(string $token, string $type): self
    {
        $userVerifier = substr($token, 0, self::VERIFIER_LENGTH);
        $userHash = substr($token, self::VERIFIER_LENGTH);

        $tokenData = Sql::factory()->getArray(
            'SELECT * FROM ' . Core::getTable('ycom_user_token') . ' WHERE hash = ? AND type = ? AND expiredate > ?',
            [$userHash, $type, Sql::datetime(time())],
        );

        if (1 !== count($tokenData)) {
            throw new RuntimeException('Token not found');
        }

        $this->hash = $this->generateHash((string) $tokenData[0]['selector'], $userVerifier);

        if (!hash_equals($this->hash, $userHash)) {
            throw new RuntimeException('Token is invalid');
        }

        $this->userId = null === $tokenData[0]['user_id'] ? null : (int) $tokenData[0]['user_id'];
        $this->email = (string) $tokenData[0]['email'];
        $this->type = (string) $tokenData[0]['type'];
        $this->selector = (string) $tokenData[0]['selector'];
        $this->createDate = (string) $tokenData[0]['createdate'];
        $this->expireDate = (string) $tokenData[0]['expiredate'];

        return $this;
    }

    public function getId(): ?int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function delete(): bool
    {
        return Sql::factory()
            ->setTable(Core::getTable('ycom_user_token'))
            ->setWhere('hash = ?', [$this->hash])
            ->delete()
            ->getRows() > 0;
    }

    public function generateHash(string $selector, string $verifier): string
    {
        return base64_encode(hash_hmac('sha256', $selector, $verifier, false));
    }

    /**
     * @param int $duration lifetime in seconds
     * @return array<string, string|int|null> the created token, including the plain `token` to put into a link
     */
    public function createToken(?int $userId, string $email, string $type, int $duration): array
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->type = $type;

        $this->selector = ByteString::fromRandom(self::VERIFIER_LENGTH)->toString();
        // Never stored — only the hash of selector+verifier is.
        $this->verifier = ByteString::fromRandom(self::VERIFIER_LENGTH)->toString();

        $this->hash = $this->generateHash($this->selector, $this->verifier);
        $this->createDate = Sql::datetime(time());
        $this->expireDate = Sql::datetime(time() + $duration);
        $this->token = $this->verifier . $this->hash;

        try {
            Sql::factory()
                ->setTable(Core::getTable('ycom_user_token'))
                ->setValue('hash', $this->hash)
                ->setValue('user_id', $this->userId)
                ->setValue('email', $this->email)
                ->setValue('type', $this->type)
                ->setValue('selector', $this->selector)
                ->setValue('createdate', $this->createDate)
                ->setValue('expiredate', $this->expireDate)
                ->insert();
        } catch (Throwable $e) {
            throw new RuntimeException('Token could not be created', 0, $e);
        }

        return [
            'hash' => $this->hash,
            'user_id' => $this->userId,
            'email' => $this->email,
            'type' => $this->type,
            'selector' => $this->selector,
            'verifier' => $this->verifier,
            'token' => $this->token,
            'createdate' => $this->createDate,
            'expiredate' => $this->expireDate,
        ];
    }

    /** @return list<array<string, mixed>> */
    public function getTokenByHash(string $hash): array
    {
        return Sql::factory()
            ->setTable(Core::getTable('ycom_user_token'))
            ->setWhere('hash = ?', [$hash])
            ->select()
            ->getArray();
    }

    public static function clearExpiredTokens(): void
    {
        Sql::factory()
            ->setTable(Core::getTable('ycom_user_token'))
            ->setWhere('expiredate < :current_datetime', ['current_datetime' => Sql::datetime(time())])
            ->delete();
    }

    public static function removeTokenByHash(string $hash): bool
    {
        return Sql::factory()
            ->setTable(Core::getTable('ycom_user_token'))
            ->setWhere('hash = ?', [$hash])
            ->delete()
            ->getRows() > 0;
    }

    public static function deleteAllTokens(): int
    {
        return Sql::factory()
            ->setTable(Core::getTable('ycom_user_token'))
            ->delete()
            ->getRows();
    }
}
