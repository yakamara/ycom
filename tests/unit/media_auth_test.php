<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_media_auth_test extends TestCase
{
    private ?string $filename = null;
    private int $authType = 0;

    protected function setUp(): void
    {
        if (!rex_plugin::get('ycom', 'media_auth')->isAvailable()) {
            self::markTestSkipped('ycom/media_auth is not available');
        }

        $media = rex_sql::factory()->getArray('SELECT filename FROM ' . rex::getTable('media') . ' WHERE filetype IN (?, ?) LIMIT 1', ['image/jpeg', 'image/png']);
        $filename = (string) ($media[0]['filename'] ?? '');
        if ('' === $filename || !is_file(rex_path::media($filename))) {
            self::markTestSkipped('no image in media pool');
        }
        $this->filename = $filename;
        $this->authType = (int) rex_media::get($filename)->getValue('ycom_auth_type');

        rex_ycom_auth::$me = null;
    }

    protected function tearDown(): void
    {
        if (null !== $this->filename) {
            $this->setAuthType($this->authType);
        }
    }

    public function testProtectedMediaIsDenied(): void
    {
        $this->setAuthType(1);

        self::assertFalse(rex_ycom_media_auth::checkPerm($this->createMediaManager(false)));
    }

    public function testProtectedMediaIsDeniedAfterFormatConversion(): void
    {
        $this->setAuthType(1);

        self::assertFalse(rex_ycom_media_auth::checkPerm($this->createMediaManager(true)));
    }

    public function testUnprotectedMediaIsPermittedAfterFormatConversion(): void
    {
        $this->setAuthType(0);

        self::assertTrue(rex_ycom_media_auth::checkPerm($this->createMediaManager(true)));
    }

    private function createMediaManager(bool $convertFormat): rex_media_manager
    {
        $media = new rex_managed_media(rex_path::media($this->filename));

        // what the effect "image_format" does with the filename
        if ($convertFormat) {
            $media->setMediaFilename(pathinfo($this->filename, PATHINFO_FILENAME) . '.webp');
        }

        return new rex_media_manager($media);
    }

    private function setAuthType(int $authType): void
    {
        rex_sql::factory()
            ->setTable(rex::getTable('media'))
            ->setWhere(['filename' => $this->filename])
            ->setValue('ycom_auth_type', $authType)
            ->update();
        rex_media_cache::delete($this->filename);
    }
}
