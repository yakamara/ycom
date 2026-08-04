<?php

/**
 * The bundled documentation, rendered from the markdown files in `docs/`.
 *
 * @var Yakamara\YCom\YComAddon $this
 */

use Redaxo\Core\Backend\Controller;
use Redaxo\Core\Backend\Page;
use Redaxo\Core\Filesystem\File;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Translation\I18n;
use Redaxo\Core\Util\Markdown;
use Redaxo\Core\View\Fragment;

$mdFiles = [];
foreach (glob($this->getPath('docs') . '/*.md') ?: [] as $file) {
    $mdFiles[mb_substr(basename($file), 0, -3)] = $file;
}

if (1 > count($mdFiles)) {
    return;
}

$default = array_key_first($mdFiles);
$current = Request::request('mdfile', 'string', $default);

if (!array_key_exists($current, $mdFiles)) {
    $current = $default;
}

// One subpage per file, so the docs get their own navigation. The numeric prefix of the file name
// only orders them; the i18n key drops it.
$page = Controller::getPageObject('ycom/docs');

if (null !== $page) {
    foreach ($mdFiles as $key => $mdFile) {
        $page->addSubpage(
            new Page($key, I18n::msg('ycom_docs_' . mb_substr($key, 3)))
                ->setSubPath($mdFile)
                ->setHref(Url::backendPage('ycom/docs', ['mdfile' => $key]))
                ->setIsActive($key === $current),
        );
    }
}

[$toc, $content] = Markdown::factory()->parseWithToc(File::require($mdFiles[$current]), 2, 3, [
    Markdown::SOFT_LINE_BREAKS => false,
    Markdown::HIGHLIGHT_PHP => true,
]);

$fragment = new Fragment();
$fragment->setVar('content', $content, false);
$fragment->setVar('toc', $toc, false);
$docs = $fragment->parse('core/page/docs.php');

$fragment = new Fragment();
$fragment->setVar('title', I18n::msg('package_help') . ' ', false);
$fragment->setVar('body', $docs, false);
echo $fragment->parse('core/page/section.php');
