<?php

namespace Yakamara\YCom;

use Override;
use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Backend\MainPage;
use Redaxo\Core\Backend\Page;
use Redaxo\Core\Cache;
use Redaxo\Core\Content\Article;
use Redaxo\Core\Content\Category;
use Redaxo\Core\Core;
use Redaxo\Core\Database\Column;
use Redaxo\Core\Database\ForeignKey;
use Redaxo\Core\Database\Index;
use Redaxo\Core\Database\Sql;
use Redaxo\Core\Database\Table as DbTable;
use Redaxo\Core\ExtensionPoint\Extension;
use Redaxo\Core\ExtensionPoint\ExtensionLevel;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;
use Redaxo\Core\Filesystem\File;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\MediaManager\MediaManager;
use Redaxo\Core\MediaPool\Media;
use Redaxo\Core\Security\Permission;
use Redaxo\Core\Translation\I18n;
use Redaxo\Core\View\Asset;
use Redaxo\Core\View\Fragment;
use Throwable;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Auth\Injection\Otp;
use Yakamara\YCom\Auth\Injection\PasswordChange;
use Yakamara\YCom\Auth\Injection\TermsOfUse;
use Yakamara\YCom\Auth\UserSession;
use Yakamara\YCom\Group\Group;
use Yakamara\YCom\MediaAuth\MediaAuth;
use Yakamara\YCom\MediaAuth\MediaAuthRules;
use Yakamara\YCom\YForm\Value\AuthPassword;
use Yakamara\YForm\FieldRegistry;
use Yakamara\YForm\Manager\Dataset;
use Yakamara\YForm\Manager\Table\Api as TableApi;
use Yakamara\YForm\Manager\Table\Table as YFormTable;
use Yakamara\YForm\View\Fragment as YFormFragment;
use Yakamara\YForm\YForm;

use function explode;
use function is_array;

/**
 * ycom — frontend user management: login, registration, profiles, and article/media permissions.
 *
 * REDAXO 5 split this into an addon plus three plugins (`auth`, `group`, `media_auth`). REDAXO 6 has
 * no plugin concept, so everything lives here. `auth` and `group` were system plugins and always
 * active anyway; `media_auth` was optional and is now switched by the `media_auth` config flag.
 */
class YComAddon extends Addon
{
    /** @var array<string, mixed> */
    public protected(set) array $defaultConfig = [
        'login_field' => 'email',
        'auth_rule' => 'login_try_5_pause',
        'auth_cookie_ttl' => 7,
        'session_duration' => 3600,
        'session_max_overall_duration' => 21600,
        'otp_auth_enforce' => 'disabled',
        'otp_auth_option' => 'all',
        'otp_auth_email_period' => 300,
        'media_auth' => 0,
        'media_auth_rule' => 'header_perm_denied',
        'log' => 0,
    ];

    #[Override]
    public function boot(): void
    {
        Auth::addInjection(new Otp(), 1);
        Auth::addInjection(new PasswordChange(), 4);
        Auth::addInjection(new TermsOfUse(), 8);

        Dataset::setModelClass(Core::getTable('ycom_user'), User::class);
        Dataset::setModelClass(Core::getTable('ycom_group'), Group::class);
        YCom::addTable(Core::getTable('ycom_user'));
        YCom::addTable(Core::getTable('ycom_group'));

        Permission::register('ycomArticlePermissions[]', null, Permission::OPTIONS);

        // ycom ships templates for the yform value fields it adds. Core's fragment loader finds them
        // because enlist() registered this addon's fragments/ dir; yform's resolver needs telling.
        YFormFragment::addSearchPath($this->getPath('fragments/'));

        Extension::register('SESSION_REGENERATED', UserSession::sessionRegenerated(...));

        // The group check on top of the plain logged-in/logged-out decision of Auth.
        Extension::register('YCOM_AUTH_USER_CHECK', $this->checkArticleGroupPerm(...));

        if (Core::isFrontend()) {
            $this->bootFrontend();
        } elseif (Core::isBackend()) {
            $this->bootBackend();
        }
    }

    private function bootFrontend(): void
    {
        // Media delivery has to be decided *before* the media manager sends the file. Core registers
        // MediaManager::init() on PACKAGES_INCLUDED/Early through #[AsExtension], which happens in
        // Extension::registerByAttribute() — i.e. after every addon's boot(). Registering here, at
        // the same level, therefore puts this listener ahead of it.
        Extension::register('PACKAGES_INCLUDED', $this->guardMediaManager(...), ExtensionLevel::Early);

        Extension::register('PACKAGES_INCLUDED', static function (): void {
            $redirect = Auth::init();

            if ('' !== $redirect) {
                Response::sendCacheControl();
                Response::sendRedirect($redirect);
            }
        });

        Extension::register(['ART_IS_PERMITTED', 'CAT_IS_PERMITTED'], static function (ExtensionPoint $ep) {
            $element = $ep->getParam('element');

            if (!$element instanceof Article && !$element instanceof Category) {
                return $ep->subject;
            }

            return Auth::articleIsPermitted($element, (bool) $ep->subject);
        });

        Extension::register('MEDIA_IS_PERMITTED', static function (ExtensionPoint $ep) {
            if (!$ep->subject || 1 !== (int) Addon::require('ycom')->getConfig('media_auth')) {
                return $ep->subject;
            }

            $media = $ep->getParam('element');

            return $media instanceof Media ? MediaAuth::checkFrontendPerm($media) : $ep->subject;
        });

        // Fans the generic matching point out into one per external auth type.
        Extension::register('YCOM_AUTH_MATCHING', static function (ExtensionPoint $ep) {
            $point = match ($ep->getParam('AuthType')) {
                'oauth2' => 'YCOM_AUTH_OAUTH2_MATCHING',
                'saml' => 'YCOM_AUTH_SAML_MATCHING',
                default => null,
            };

            if (null === $point) {
                return $ep->subject;
            }

            return Extension::dispatch(new ExtensionPoint($point, $ep->subject, [
                'Userdata' => $ep->getParam('Userdata'),
            ]));
        }, ExtensionLevel::Early);
    }

    private function bootBackend(): void
    {
        Asset::addCssFile($this->getAssetsUrl('styles.css'));

        // Read the page from the request rather than through Controller/Url: Core::isBackend() is
        // also true on the command line, where the backend page has not been set and
        // Url::currentBackendPage() throws.
        $page = Request::request('page', 'string', '');

        if ('content/edit' === $page || str_starts_with($page, 'mediapool')) {
            Asset::addJsFile($this->getAssetsUrl('ycom_backend.js'));
        }

        // The per-article permission panel in the structure sidebar.
        Extension::register('STRUCTURE_CONTENT_SIDEBAR', function (ExtensionPoint $ep) {
            $panel = $this->includeFile($this->getPath('pages/content.perm.php'), [
                'article_id' => (int) $ep->getParam('article_id'),
                'clang' => (int) $ep->getParam('clang'),
                'ctype' => (int) $ep->getParam('ctype'),
            ]);

            $fragment = new Fragment();
            $fragment->setVar('title', '<i class="fa fa-user"></i> ' . $this->i18n('ycom_page_perm'), false);
            $fragment->setVar('body', $panel, false);
            $fragment->setVar('collapse', true);
            $fragment->setVar('collapsed', false);

            return $ep->subject . $fragment->parse('core/page/section.php');
        });

        // The group fields on the article permission panel.
        Extension::register('YCOM_ARTICLE_PERM_SELECT', static function (ExtensionPoint $ep) {
            $yform = $ep->subject;

            if (!$yform instanceof YForm) {
                return $yform;
            }

            $yform->setValueField('choice', [
                'name' => 'ycom_group_type',
                'label' => I18n::msg('ycom_group_type'),
                'choices' => Group::PERMS,
            ]);
            $yform->setValueField('choice', [
                'name' => 'ycom_groups',
                'label' => I18n::msg('ycom_groups'),
                'choices' => Group::getGroups(),
                'size' => 5,
                'multiple' => true,
            ]);

            return $yform;
        });

        // "Log in as this user" on the user data list.
        Extension::register('YFORM_DATA_LIST_ACTION_BUTTONS', static function (ExtensionPoint $ep) {
            $table = $ep->getParam('table');

            if (!$table instanceof YFormTable || Core::getTable('ycom_user') !== $table->getTableName()) {
                return $ep->subject;
            }

            if (!Core::getUser()?->admin) {
                return $ep->subject;
            }

            $buttons = is_array($ep->subject) ? $ep->subject : [];
            $buttons['ycom_impersonate'] = [
                'params' => [],
                'content' => '<i class="rex-icon rex-icon-sign-in"></i> ' . I18n::msg('ycom_impersonate'),
                'attributes' => [
                    'data-confirm' => I18n::msg('ycom_impersonate_alert'),
                ],
                'url' => Url::backendController([
                    'page' => 'ycom/sessions',
                    'user_id' => '___id___',
                    'func' => 'create_session',
                ]),
            ];

            return $buttons;
        });

        $this->bootMediaPoolFields();
    }

    /**
     * Adds the permission fields to the media pool form and saves them.
     */
    private function bootMediaPoolFields(): void
    {
        Extension::register(
            ['MEDIA_FORM_ADD', 'MEDIA_FORM_EDIT', 'MEDIA_ADDED', 'MEDIA_UPDATED'],
            fn (ExtensionPoint $ep) => $this->includeFile($this->getPath('pages/mediapool.perm.php'), ['ep' => $ep]),
        );
    }

    /**
     * Blocks a media-manager request for a file the visitor may not see.
     *
     * Runs on `PACKAGES_INCLUDED` before core's own media-manager handler; see the note in
     * {@see self::bootFrontend()} on why that ordering holds.
     */
    private function guardMediaManager(): void
    {
        if (1 !== (int) $this->getConfig('media_auth')) {
            return;
        }

        $file = MediaManager::getMediaFile();
        $type = MediaManager::getMediaType();

        if ('' === $file || '' === $type) {
            return;
        }

        // The permission check needs to know who is logged in, and the media request never goes
        // through the regular Auth::init() listener because this one runs earlier.
        Auth::init();

        if (!MediaAuth::checkPerm($file)) {
            new MediaAuthRules()->check((string) $this->getConfig('media_auth_rule'));
        }
    }

    /**
     * The group part of the article permission check, on top of Auth's logged-in decision.
     */
    private function checkArticleGroupPerm(ExtensionPoint $ep): bool
    {
        if (!$ep->subject) {
            return false;
        }

        $article = $ep->getParam('article');

        if (!$article instanceof Article && !$article instanceof Category) {
            return (bool) $ep->subject;
        }

        // Groups only narrow the "logged-in users only" case.
        if (1 !== (int) $article->getValue('ycom_auth_type')) {
            return (bool) $ep->subject;
        }

        $me = $ep->getParam('me');
        $articleGroups = (string) $article->getValue('ycom_groups');

        return Group::hasGroupPerm(
            (string) $article->getValue('ycom_group_type'),
            '' === $articleGroups ? [] : explode(',', $articleGroups),
            $me instanceof User ? $me->getGroups() : [],
        );
    }

    /**
     * @return iterable<Page>
     */
    #[Override]
    public function getPages(): iterable
    {
        $main = new MainPage('ycom', 'ycom', $this->i18n('ycom_title'));
        $main->setIcon('rex-icon fa-group');

        $main->addSubpage(new Page('settings', $this->i18n('ycom_navi_title'))->setRequiredPermissions('admin'));
        $main->addSubpage(new Page('sessions', $this->i18n('ycom_sessions'))->setRequiredPermissions('admin'));
        $main->addSubpage(new Page('tokens', $this->i18n('ycom_tokens'))->setRequiredPermissions('admin'));
        $main->addSubpage(new Page('log', $this->i18n('ycom_log'))->setRequiredPermissions('admin'));
        $main->addSubpage(new Page('media_auth', $this->i18n('ycom_media_auth'))->setRequiredPermissions('admin'));

        $main->addSubpage(
            new Page('docs', $this->i18n('ycom_docs'))
                ->setRequiredPermissions('admin')
                ->addItemClass('pull-right')
                ->setIcon('rex-icon fa-info-circle'),
        );

        yield $main;

        // REDAXO 5 moved the yform data pages of the ycom tables into the `ycom` navigation block
        // from a PACKAGES_INCLUDED listener, because the page tree came from package.yml. Here the
        // entries are simply built in this block to begin with.
        yield from $this->getTablePages();
    }

    /**
     * One navigation entry per ycom table, pointing at yform's data list.
     *
     * @return iterable<Page>
     */
    private function getTablePages(): iterable
    {
        $user = Core::getUser();

        if (null === $user) {
            return;
        }

        $activeTableName = 'yform/manager/data_edit' === Request::request('page', 'string')
            ? Request::request('table_name', 'string')
            : null;

        $prio = 10;

        foreach (YCom::getTables() as $tableName) {
            try {
                $table = YFormTable::get($tableName);
            } catch (Throwable) {
                // Before install/migrate has run the tables are unknown.
                return;
            }

            if (null === $table || !$table->isActive() || !$table->isGranted('VIEW', $user)) {
                continue;
            }

            $page = new MainPage('ycom', 'ycom_table_' . $tableName, $table->getNameLocalized());
            $page->setHref(Url::backendPage('yform/manager/data_edit', ['table_name' => $tableName]));
            $page->setIcon('rex-icon ' . ($table->getCustomIcon() ?: 'rex-icon-module'));
            $page->setPrio($prio++);

            if ($tableName === $activeTableName) {
                $page->setIsActive();
            }

            yield $page;
        }
    }

    #[Override]
    public function install(): void
    {
        YFormTable::deleteCache();

        $this->installUserTables();
        $this->installSessionTable();
        $this->installTokenTable();
        $this->installStructureColumns();
        $this->installMediaColumns();
        $this->copyExternalAuthConfigs();

        YFormTable::deleteCache();

        // The article and media caches hold the rows whose columns just changed.
        Cache::delete();
    }

    /**
     * Imports the yform table definitions of `rex_ycom_user` and `rex_ycom_group`.
     */
    private function installUserTables(): void
    {
        $content = File::get($this->getPath('install/tablesets/yform_user.json'));

        if (null === $content || '' === $content) {
            return;
        }

        // The tableset uses ycom's own `ycom_auth_password` field type. Discovery caches its results
        // per request, and during a fresh install the class was not yet visible when the cache was
        // filled — touching the class here is not enough, so the cache is dropped instead.
        class_exists(AuthPassword::class);
        FieldRegistry::reset();

        TableApi::importTablesets($content);
    }

    private function installSessionTable(): void
    {
        DbTable::get(Core::getTable('ycom_user_session'))
            ->ensureColumn(new Column('session_id', 'varchar(255)'))
            ->ensureColumn(new Column('user_id', 'int(10) unsigned'))
            ->ensureColumn(new Column('ip', 'varchar(39)')) // enough for IPv6
            ->ensureColumn(new Column('useragent', 'varchar(255)', true))
            ->ensureColumn(new Column('starttime', 'datetime', true))
            ->ensureColumn(new Column('last_activity', 'datetime', true))
            ->ensureColumn(new Column('otp_verified', 'tinyint(1)', false, '0'))
            ->ensureColumn(new Column('cookie_key', 'varchar(255)', true))
            ->ensureIndex(new Index('cookie_key', ['cookie_key'], Index::UNIQUE))
            ->setPrimaryKey('session_id')
            ->ensureForeignKey(new ForeignKey(
                'ycom_user_session_id',
                Core::getTable('ycom_user'),
                ['user_id' => 'id'],
                ForeignKey::CASCADE,
                ForeignKey::CASCADE,
            ))
            ->ensure();
    }

    private function installTokenTable(): void
    {
        DbTable::get(Core::getTable('ycom_user_token'))
            ->ensureColumn(new Column('hash', 'varchar(255)'))
            ->ensureColumn(new Column('user_id', 'int(10) unsigned', true))
            ->ensureColumn(new Column('email', 'varchar(255)', true))
            ->ensureColumn(new Column('type', 'varchar(255)'))
            ->ensureColumn(new Column('selector', 'varchar(255)'))
            ->ensureColumn(new Column('createdate', 'datetime', true))
            ->ensureColumn(new Column('expiredate', 'datetime', true))
            ->setPrimaryKey('hash')
            ->ensureIndex(new Index('token', ['selector'], Index::UNIQUE))
            ->ensureForeignKey(new ForeignKey(
                'ycom_user_token_id',
                Core::getTable('ycom_user'),
                ['user_id' => 'id'],
                ForeignKey::CASCADE,
                ForeignKey::CASCADE,
            ))
            ->ensure();
    }

    /**
     * The permission columns on articles and categories.
     */
    private function installStructureColumns(): void
    {
        DbTable::get(Core::getTable('article'))
            ->ensureColumn(new Column('ycom_auth_type', 'int', false, '0'))
            ->ensureColumn(new Column('ycom_group_type', 'int', false, '0'))
            ->ensureColumn(new Column('ycom_groups', 'text', true))
            ->alter();
    }

    private function installMediaColumns(): void
    {
        DbTable::get(Core::getTable('media'))
            ->ensureColumn(new Column('ycom_auth_type', 'int(11)', false, '0'))
            ->ensureColumn(new Column('ycom_group_type', 'int(11)', false, '0'))
            ->ensureColumn(new Column('ycom_groups', 'text', true))
            ->alter();
    }

    /**
     * Puts the example configurations for the external auth providers into the addon's data dir,
     * where a project edits them.
     */
    private function copyExternalAuthConfigs(): void
    {
        foreach (['saml', 'oauth2'] as $type) {
            $target = $this->getDataPath($type . '.php');

            if (!is_file($target)) {
                File::copy($this->getPath('install/' . $type . '.php'), $target);
            }
        }
    }

    #[Override]
    public function uninstall(): void
    {
        // The yform bookkeeping for ycom's own tables.
        foreach ([Core::getTable('ycom_user'), Core::getTable('ycom_group')] as $tableName) {
            foreach (['yform_table', 'yform_field', 'yform_history'] as $bookkeeping) {
                Sql::factory()->setQuery(
                    'DELETE FROM `' . Core::getTable($bookkeeping) . '` WHERE `table_name` = ?',
                    [$tableName],
                );
            }
        }

        DbTable::get(Core::getTable('article'))
            ->removeColumn('ycom_auth_type')
            ->removeColumn('ycom_group_type')
            ->removeColumn('ycom_groups')
            ->alter();

        DbTable::get(Core::getTable('media'))
            ->removeColumn('ycom_auth_type')
            ->removeColumn('ycom_group_type')
            ->removeColumn('ycom_groups')
            ->alter();

        // Order matters: both reference rex_ycom_user.
        foreach (['ycom_user_session', 'ycom_user_token', 'ycom_group', 'ycom_user'] as $name) {
            $table = DbTable::get(Core::getTable($name));

            if ($table->exists()) {
                $table->drop();
            }
        }

        YFormTable::deleteCache();
        Cache::delete();
    }
}
