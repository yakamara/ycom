<?php

use Symfony\Component\String\ByteString;

class rex_ycom_user_token
{
    use rex_singleton_trait;

    private ?int $user_id = null;
    private ?string $email = null;
    private ?string $type = null;
    private ?string $selector = null;
    private ?string $verifier = null;
    private ?string $hash;
    private ?string $createdate;
    private ?string $expiredate;
    private ?string $token;
    private ?int $duration;

    public function validateToken($token, $type): self
    {
        // split token into hash and verifier
        $user_verifier = substr($token, 0, 20);
        $user_hash = substr($token, 20);

        $TokenData = rex_sql::factory()
            ->getArray(
                'SELECT * FROM ' . rex::getTable('ycom_user_token') . ' WHERE hash = ? and type = ? and expiredate > ?',
                [
                    $user_hash,
                    $type,
                    rex_sql::datetime(time()),
                ]);

        if (1 !== count($TokenData)) {
            throw new Exception('Token not found');
        }

        // check hashes
        $this->hash = $this->generateHash($TokenData[0]['selector'], $user_verifier);

        if (!hash_equals($this->hash, $user_hash)) {
            throw new Exception('Token is invalid');
        }

        $this->user_id = $TokenData[0]['user_id'];
        $this->email = $TokenData[0]['email'];
        $this->type = $TokenData[0]['type'];
        $this->selector = $TokenData[0]['selector'];
        $this->createdate = $TokenData[0]['createdate'];
        $this->expiredate = $TokenData[0]['expiredate'];

        return $this;
    }

    public function getId(): ?string
    {
        return $this->user_id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function delete(): bool
    {
        $sql = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_token'))
            ->setWhere('hash = ?', [$this->hash])
            ->delete();
        return $sql->getRows() > 0;
    }

    public function generateHash($selector, $verifier): string
    {
        return base64_encode(hash_hmac('sha256', $selector, $verifier, false));
    }

    public function createToken($user_id, $email, $type, $duration): array
    {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->type = $type;
        $this->duration = $duration;

        $this->selector = ByteString::fromRandom(20)->toString();
        $this->verifier = ByteString::fromRandom(20)->toString(); // do not store in database!

        $this->hash = $this->generateHash($this->selector, $this->verifier); // base64_encode(hash_hmac('sha256', $this->selector, $this->verifier, false));
        $this->createdate = rex_sql::datetime(time());
        $this->expiredate = rex_sql::datetime(time() + $this->duration);
        $this->token = $this->verifier . $this->hash;

        try {
            rex_sql::factory()
                ->setDebug(false)
                ->setTable(rex::getTable('ycom_user_token'))
                ->setValue('hash', $this->hash)
                ->setValue('user_id', $this->user_id)
                ->setValue('email', $this->email)
                ->setValue('type', $this->type)
                ->setValue('selector', $this->selector)
                ->setValue('createdate', $this->createdate)
                ->setValue('expiredate', $this->expiredate)
                ->insert();
        } catch (Exception $e) {
            throw new Exception('Token could not be created');
        }

        return [
            'hash' => $this->hash,
            'user_id' => $this->user_id,
            'email' => $this->email,
            'type' => $this->type,
            'selector' => $this->selector,
            'verifier' => $this->verifier,
            'token' => $this->token,
            'createdate' => $this->createdate,
            'expiredate' => $this->expiredate,
        ];
    }

    public function getTokenByHash(string $hash): array
    {
        $sql = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_token'))
            ->setWhere('hash = ?', [$hash])
            ->select();
        return $sql->getArray();
    }

    public static function clearExpiredTokens(): void
    {
        rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_token'))
            ->setWhere('expiredate < :current_datetime', [
                ':current_datetime' => rex_sql::datetime(time()),
            ])
            ->delete();
    }

    public static function removeTokenByHash(string $hash): bool
    {
        $sql = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_token'))
            ->setWhere('hash = ?', [$hash])
            ->delete();
        return $sql->getRows() > 0;
    }

    public static function deleteAllTokens(): bool
    {
        $sql = rex_sql::factory()
            ->setTable(rex::getTable('ycom_user_token'))
            ->delete();
        return $sql->getRows() > 0;
    }
}
