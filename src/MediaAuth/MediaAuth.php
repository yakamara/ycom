<?php

namespace Yakamara\YCom\MediaAuth;

use Redaxo\Core\MediaPool\Media;
use Redaxo\Core\Security\BackendLogin;
use Yakamara\YCom\Group\Group;
use Yakamara\YCom\User;

use function explode;

/**
 * Access control for media pool files.
 *
 * In REDAXO 5 this was the optional `media_auth` plugin. REDAXO 6 has no plugin concept, so the
 * feature is part of the addon and switched on by the `media_auth` config flag — the media columns
 * exist either way, but nothing is enforced while it is off.
 */
final class MediaAuth
{
    /** @var array<string, string> the `ycom_auth_type` choices of a media file */
    public const array PERMS = [
        '0' => 'translate:ycom_perm_all',
        '1' => 'translate:ycom_perm_only_logged_in',
    ];

    private function __construct() {}

    /**
     * Whether the file behind a media-manager request may be delivered.
     */
    public static function checkPerm(string $fileName): bool
    {
        // A backend user is always allowed — they can see the file in the media pool anyway.
        if (BackendLogin::hasSession()) {
            return true;
        }

        $media = Media::get($fileName);

        if (null === $media) {
            return false;
        }

        return self::checkFrontendPerm($media);
    }

    public static function checkFrontendPerm(Media $media): bool
    {
        if (1 !== (int) $media->getValue('ycom_auth_type')) {
            return true;
        }

        // ----- from here on, only logged-in users
        $me = User::getMe();

        if (null === $me) {
            return false;
        }

        $groupType = (int) $media->getValue('ycom_group_type');

        $groups = [];
        if ('' !== (string) $media->getValue('ycom_groups')) {
            $groups = explode(',', (string) $media->getValue('ycom_groups'));
        }

        return Group::hasGroupPerm($groupType, $groups, $me->getGroups());
    }
}
