<?php

class rex_com_filebrowser
{

    private $path = '';
    private $file = '';
    private $admin = false;
    private $currentPath = '';
    private $fullPath;
    /** @type SplFileInfo[] */
    private $currentDirs = array();
    /** @type SplFileInfo[] */
    private $currentFiles = array();
    private $mimeTypes = array(
        'bmp' => 'mime-bmp.gif',
        'css' => 'mime-css.gif',
        'doc' => 'mime-doc.gif',
        'docx' => 'mime-docx.gif',
        'eps' => 'mime-eps.gif',
        'error' => 'mime-error.gif',
        'exe' => 'mime-exe.gif',
        'flv' => 'mime-flv.gif',
        'gif' => 'mime-gif.gif',
        'gz' => 'mime-gz.gif',
        'java' => 'mime-java.gif',
        'jpeg' => 'mime-jpeg.gif',
        'jpg' => 'mime-jpg.gif',
        'mov' => 'mime-mov.gif',
        'mp3' => 'mime-mp3.gif',
        'ogg' => 'mime-ogg.gif',
        'pdf' => 'mime-pdf.gif',
        'png' => 'mime-png.gif',
        'ppt' => 'mime-ppt.gif',
        'pptx' => 'mime-pptx.gif',
        'rtf' => 'mime-rtf.gif',
        'swf' => 'mime-swf.gif',
        'tar' => 'mime-tar.gif',
        'tif' => 'mime-tif.gif',
        'tiff' => 'mime-tiff.gif',
        'txt' => 'mime-txt.gif',
        'wma' => 'mime-wma.gif',
        'xls' => 'mime-xls.gif',
        'xlsx' => 'mime-xlsx.gif',
        'zip' => 'mime-zip.gif'
    );
    private $imageTypes = array('jpg', 'gif', 'jpeg', 'png', 'pdf');
    private $defaultSort = 'name-asc';


    function __construct($path = null)
    {
        $path = $path ?: rex_path::pluginData('community', 'filebrowser');
        rex_dir::create($path);
        $this->path = rtrim(realpath($path), '/');
        
        $this->setCurrentPath(rex_request('path', 'string'));
        $this->setCurrentFile(rex_request('file', 'string'));
        rex_login::startSession();
    }


    function setAdmin($admin = true)
    {
        $this->admin = $admin;
    }


    function isAdmin()
    {
        return $this->admin;
    }

    function setCurrentPath($current_path = '')
    {

        if ($this->isInRealm($current_path)) {
            $this->currentPath = $current_path;

        } else {
            $this->currentPath = '';

        }
        $this->fullPath = $this->path . $this->currentPath;
    }

    protected function isInRealm($path)
    {
        return $this->path == substr(realpath($this->path . '/' . $path), 0, strlen($this->path));
    }

    function setCurrentFile($file = '')
    {
        if ($this->isInRealm($this->getCurrentPath($file))) {
            $this->file = $file;
        } else {
            $this->file = '';
        }
        return $this->file;
    }


    function getCurrentPath($file = null)
    {
        return $this->currentPath . ($file ? '/' . $file : '');
    }

    function getFullPath($file = null)
    {
        return $this->fullPath . ($file ? '/' . $file : '');
    }

    public function setDefaultSort($type, $direction = 'asc')
    {
        $this->defaultSort = $type . '-' . $direction;
    }

    function scanDir()
    {
        $this->currentDirs = array();
        $this->currentFiles = array();

        foreach (rex_finder::factory($this->fullPath)->dirsOnly()->sort() as $dir) {
            $this->currentDirs[$this->getCurrentPath($dir->getBasename())] = $dir;
        }

        $sort = explode('-', rex_get('sort', 'string') ?: $this->defaultSort);
        $sort[1] = isset($sort[1]) ? $sort[1] : 'asc';
        $sortGetter = 'date' == $sort[0] ? 'getMTime' : 'getBasename';
        $finder = rex_finder::factory($this->fullPath)->filesOnly()->sort(function (SplFileInfo $a, SplFileInfo $b) use ($sort, $sortGetter) {
            $a = $a->$sortGetter();
            $b = $b->$sortGetter();
            if ($a == $b) {
                return 0;
            }
            if ('desc' == $sort[1]) {
                $temp = $a;
                $a = $b;
                $b = $temp;
            }
            if ('date' == $sort[0]) {
                return $a < $b ? -1 : 1;
            }
            return strnatcasecmp($a, $b);
        });
        if ($search = rex_get('search', 'string')) {
            $finder = new CallbackFilterIterator($finder->recursive()->getIterator()->getIterator(), function (SplFileInfo $file) use ($search) {
                return false !== stripos($file->getBasename(), $search);
            });
        }
        foreach ($finder as $file) {
            $this->currentFiles[$file->getRealPath()] = $file;
        }

    }


    function getLink($params = array())
    {
        $defaultParams = array(
            'path' => $this->currentPath,
            'sort' => rex_get('sort'),
        );
        return rex_getUrl('', '', array_merge($defaultParams, $params));
    }

    function getDownloadLink($params = array())
    {
        return rex_getUrl('', '', $params);
    }


    protected function dirInfo($message, $params = array())
    {
        self::message('dir-info', $message, $params);
    }

    protected function dirError($message, $params = array())
    {
        $defaultParams = array(
            'func' => rex_get('func'),
            'folder' => rex_get('folder'),
            'edit_folder' => rex_request('edit_folder'),
        );
        self::message('dir-error', $message, array_merge($defaultParams, $params));
    }

    protected function fileInfo($message, $params = array())
    {
        self::message('file-info', $message, $params);
    }

    protected function fileError($message, $params = array())
    {
        $defaultParams = array(
            'func' => rex_get('func'),
            'file' => rex_get('file'),
        );
        self::message('file-error', $message, array_merge($defaultParams, $params));
    }

    private function message($type, $message, $params = array())
    {
        rex_set_session($type, $message);
        $defaultParams = array(
            'path' => $this->currentPath,
            'search' => rex_get('search'),
            'sort' => rex_get('sort'),
        );
        rex_redirect('', '', array_merge($defaultParams, $params));
    }

    private function getMessage($type)
    {
        $message = rex_session($type);
        rex_set_session($type, null);
        return $message;
    }

    public function getDownload($path, $file, $ext)
    {
        ob_end_clean();
        ob_end_clean();

        $ctype = 'application/force-download';
        switch ($ext) {
            case 'pdf': $ctype = 'application/pdf'; break;
            case 'exe': $ctype = 'application/octet-stream'; break;
            case 'zip': $ctype = 'application/zip'; break;
            case 'doc': $ctype = 'application/msword'; break;
            case 'xls': $ctype = 'application/vnd.ms-excel'; break;
            case 'ppt': $ctype = 'application/vnd.ms-powerpoint'; break;
            case 'gif': $ctype = 'image/gif'; break;
            case 'png': $ctype = 'image/png'; break;
            case 'jpe': case 'jpeg': case 'jpg': $ctype = 'image/jpg'; break;
        }

        if (!file_exists($path)) {
            die('NO FILE HERE');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        // header("Content-Type: $ctype");
        header('Content-Disposition: attachment; filename="' . basename($file) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . @filesize($path));

        // header("Cache-Control: private",false);
        set_time_limit(0);

        readfile($path) or die('File not found.');
        exit;

    }


    // ---------------------------------------- Views

    public function getStyle()
    {
        return '<style>

        #other #left {
        width: auto;
        }

       .rex-com-filebrowser table {
         width:100%;
       }

      .rex-com-filebrowser table td,
      .rex-com-filebrowser table th {
        vertical-align: middle;
      }

      .rex-com-filebrowser table td.image,
      .rex-com-filebrowser table th.image{
        background:#f0f0f0;
        width:50px;
        text-align: center;
      }

      .rex-com-filebrowser table td.func,
      .rex-com-filebrowser table th.func{
        background:#f0f0f0;
        width:80px;
      }

      .rex-com-filebrowser table td.name {
        font-size: 16px;
        line-height: 20px;
      }

      .rex-com-filebrowser .path a {
        padding: 0px 5px 0px 0px;
      }

      .rex-com-filebrowser p.info span,
      .rex-com-filebrowser p.error span{
        margin:5px 10px;
        display:block;
      }

      .rex-com-filebrowser span.time,
      .rex-com-filebrowser span.size {
        color: #bbbbbb
      }

      .rex-com-filebrowser span.size {
        float:right;
      }

      .rex-com-filebrowser p.info {
        background-color: #88DD88;
      border: 1px solid #009900;
      }

      .rex-com-filebrowser p.error {
        background-color: #dd8888;
      border: 1px solid #990000;
      }

      </style>';

    }


    public function getView()
    {
        global $I18N, $REX;

        return
            '<div class="rex-com-filebrowser">' .
            // $this->getStyle() .
            $this->getPathView() .
            $this->getToolbarView() . 
            (rex_get('search', 'string') ? '' : $this->getFoldersView()) . 
            $this->getFilesView() .
            '</div>';

    }


    public function getPathView()
    {

        $return = '';
        $paths = explode('/', $this->currentPath);

        $path_connect = array();
        foreach ($paths as $path) {
            $path_connect[] = $path;
            if ($path == '') {
                $path = 'Start';
            }
            $return .= '<a href="' . $this->getLink(array('path' => implode('/', $path_connect))) . '">' . $path . '</a>';
        }

        return '<h2 class="path">Path: ' . $return . '</h2>';
        $return = '<h2>Path:' . $this->current_path . '</h2>';
        return $return;
    }


    public function getFoldersView()
    {

        $request_folder = stripslashes(rex_request('folder', 'string'));
        $request_add_folder = stripslashes(rex_request('add_folder', 'string'));
        $request_edit_folder = stripslashes(rex_request('edit_folder', 'string'));
        $request_func = rex_request('func', 'string');

        $replace_folder = array();

        $elements = array();

        if ($this->isAdmin()) {

            if (rex_post('submit_add_folder', 'bool')) {
                if ($request_add_folder == '') {
                    $this->dirError('Please enter a foldername');

                } elseif ($request_add_folder != basename($request_add_folder) ) {
                    $this->dirError('Please check the foldername <b>"' . htmlspecialchars($request_add_folder) . '"</b>');

                } elseif ( file_exists($this->path . $this->currentPath . '/' . $request_add_folder) ) {
                    $this->dirError('Folder <b>"' . htmlspecialchars($request_add_folder) . '"</b> exists.');

                } elseif ( @mkdir($this->path . $this->currentPath . '/' . $request_add_folder)) {
                    $this->dirInfo('Folder <b>"' . htmlspecialchars($request_add_folder) . '"</b> has been added');

                } else {
                    $this->dirError('folder <b>"' . htmlspecialchars($request_add_folder) . '"</b> could not be created');

                }
            } elseif (rex_post('submit_edit_folder', 'bool')) {

                if (!$request_edit_folder || $request_folder == $request_edit_folder) {
                    $this->dirError('Please enter a new foldername</b>');

                } elseif ($request_edit_folder != basename($request_edit_folder) ) {
                    $this->dirError('Please check the foldername <b>"' . htmlspecialchars($request_edit_folder) . '"</b>');

                } elseif ( file_exists($this->path . $this->currentPath . '/' . $request_edit_folder) ) {
                    $this->dirError('Folder <b>"' . htmlspecialchars($request_edit_folder) . '"</b> exists.');

                } elseif ( rename($this->path . $this->currentPath . '/' . $request_folder, $this->path . $this->currentPath . '/' . $request_edit_folder)) {
                    $this->dirInfo('Folder <b>"' . htmlspecialchars($request_edit_folder) . '"</b> has been updated');

                } else {
                    $this->dirError('folder <b>"' . htmlspecialchars($request_edit_folder) . '"</b> could not be updated');
                }

                $request_func = 'edit_folder';
            }

            switch ($request_func) {

                case 'edit_folder':
                    if ($request_edit_folder == '') {
                        $request_edit_folder = $request_folder;
                    }
                    $replace_folder[$this->currentPath . '/' . $request_folder] = '<tr>
                        <td class="image"><a class="folder" href="#"><img src="/files/addons/community/plugins/filebrowser/folder.gif" /></a></td>
                        <td class="name"><input type="text" size="70" name="edit_folder" value="' . htmlspecialchars(stripslashes($request_edit_folder)) . '" /></td>
                        <td class="func"><input type="submit" value="Edit Folder" name="submit_edit_folder"/></td>
                    </tr>';

                    break;

                case 'delete_folder':

                    if ( rmdir($this->path . $this->currentPath . '/' . $request_folder) ) {
                        $this->dirInfo('Folder <b>"' . htmlspecialchars($request_folder) . '"</b> has been deleted');

                    } else {
                        $this->dirError('Folder <b>"' . htmlspecialchars($request_folder) . '"</b> could not be deleted. Folder is not empty.');
                    }

                    break;

            }

            $elements['add_folder'] = '<tr>
                <td class="image"><a class="folder" href="#"><img src="/files/addons/community/plugins/filebrowser/folder.gif" /></a></td>
                <td class="name"><input type="text" size="70" name="add_folder" value="' . htmlspecialchars(stripslashes($request_add_folder)) . '" /></td>
                <td class="func"><input type="submit" value="Add Folder" name="submit_add_folder" /></td>
            </tr>';


        }


        foreach ($this->currentDirs as $path => $dir) {

            $fullpath = $this->currentPath . '/' . $dir->getBasename();

            $links = array();
            if ($this->isAdmin() && $dir->getBasename() != '..') {
                $links[] = '<a class="edit" href="' . $this->getLink(array('folder' => $dir->getBasename(), 'func' => 'edit_folder')) . '">edit</a>';
                $links[] = '<a class="delete" href="' . $this->getLink(array('folder' => $dir->getBasename(), 'func' => 'delete_folder')) . '">delete</a>';

            }

            $enter_link = $this->getLink(array('path' => $path));
            $basename = htmlspecialchars($dir->getBasename());
            $class = '';
            if ($basename == '..') {
                $class = ' class="folder-up"';
            }

            $elements[$fullpath] = '<tr' . $class . '>
            <td class="image"><a class="folder" href="' . $enter_link . '"><img src="/files/addons/community/plugins/filebrowser/folder.gif" /></a></td>
            <td class="name"><a href="' . $enter_link . '">' . $basename . '</a></td>
            <td class="func">' . implode('<br />', $links) . '</td>
            </tr>';

            if (isset($replace_folder[$fullpath])) {
                $elements[$fullpath] = $replace_folder[$fullpath];
            }



        }

        if (count($elements) == 0) {
            $elements[] = '<tr><td colspan="3">This folder has no subfolders</td></tr>';
        }

        $return = '<h2>Folders</h2>';

        if ($error = self::getMessage('dir-error')) {
            $return .= '<p class="error"><span>' . $error . '</span></p>';
        }

        if ($info = self::getMessage('dir-info')) {
            $return .= '<p class="info"><span>' . $info . '</span></p>';
        }

        $return .= '
            <form action="' . $this->getLink(array('func' => $request_func, 'folder' => $request_folder)) . '" method="post">
                <table>
                    <thead>
                        <tr>
                            <th class="image"></th>
                            <th class="name">Name</th>
                            <th class="func">Function</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . implode('', $elements) . '
                    </tbody>
                </table>
            </form>';

        return '<div class="folders">' . $return . '</div>';

    }





    public function getFilesView()
    {
        $elements = array();
        $func = rex_request('func', 'string');
        $file = rex_request('file', 'string');
        $search = rex_request('search', 'string');
        $targetDir = rex_post('target_dir', 'string', null);
        $targetFile = rex_post('target_file', 'string');

        if ($func == 'download' && isset($this->currentFiles[$this->getFullPath($file)])) {
            $fileInfo = $this->currentFiles[$this->getFullPath($file)];
            $this->getDownload($this->getFullPath($file), $fileInfo->getBasename(), $fileInfo->getExtension());
            exit;
        } elseif ($this->isAdmin()) {
            if (rex_post('submit_upload', 'bool')) {
                $name = @$_FILES['file']['name'][0];
                $tmp_name = @$_FILES['file']['tmp_name'][0];

                if ($name != '' && $tmp_name != '') {

                    $file_name = $name;
                    $counter = 1;
                    while (file_exists($this->path . $this->currentPath . '/' . $file_name)) {
                        $file_name = $counter . '_' . $name;
                        $counter++;
                    }

                    if ( move_uploaded_file($tmp_name, $this->path . $this->currentPath . '/' . $file_name )) {
                        $this->fileInfo('File <b>"' . htmlspecialchars($name) . '"</b> has been uploaded');
                    } else {
                        $this->fileError('File <b>"' . htmlspecialchars($name) . '"</b> could not be uploaded');
                    }

                } else {
                    $this->fileError('Please select an uploadfile');

                }

            } elseif (isset($this->currentFiles[$this->getFullPath($file)])) {

                $fileInfo = $this->currentFiles[$this->getFullPath($file)];
                if ('delete' == $func) {
                    unlink($this->getFullPath($file));
                    $this->fileInfo('File <b>"' . htmlspecialchars($fileInfo->getBasename()) . '"</b> has been deleted');
                } elseif ((($copy = rex_post('submit_copy', 'bool')) || rex_post('submit_move', 'bool')) && !is_null($targetDir) && $targetFile) {
                    $func = $copy ? 'copy' : 'move';
                    $targetFullFile = basename($targetFile) . '.' . $fileInfo->getExtension();
                    $target = $this->path . '/' . $targetDir . '/' . $targetFullFile;
                    if (!$this->isInRealm($targetDir)) {
                        $this->fileError('Select an existing target folder');
                    } elseif (file_exists($target)) {
                        $this->fileError('File <b>"' . htmlspecialchars($targetFullFile) . '"</b> already exists in selected directory');
                    } else {
                        if ($copy) {
                            rex_file::copy($this->getFullPath($file), $target);
                            $this->fileInfo('File <b>"' . htmlspecialchars($file) . '"</b> has been copied');
                        } else {
                            rename($this->getFullPath($file), $target);
                            $this->fileInfo('File <b>"' . htmlspecialchars($file) . '"</b> has been moved');
                        }
                    }
                }

            }
        }

        foreach ($this->currentFiles as $path => $fileInfo) {


            // $name = substr($fileInfo->getRealPath(), strlen($this->fullPath) + 1);
            $name = $fileInfo->getFilename();
            $subpath = substr($fileInfo->getPath(), strlen($this->fullPath) + 1);
            $subpath .= $subpath ? '/' : '';
            $ext = $fileInfo->getExtension();
            $imagepath = substr($fileInfo->getPath(), strlen($this->path));

            if (in_array($func, array('copy', 'move')) && $file == $name) {
                $select = new rex_select();
                $select->setSize(1);
                $select->setName('target_dir');
                $select->setStyle('class="functarget"');
                $iterator = rex_finder::factory($this->path)
                    ->dirsOnly()
                    ->recursive()
                    ->getIterator();
                $select->addOption('Start', '');
                $strip = strlen($this->path);
                foreach ($iterator as $p => $dir) {
                    $select->addOption(str_repeat('°', $iterator->getDepth() + 1) . $dir->getBasename(), substr($p, $strip));
                }
                $select->setSelected($targetDir ?: rtrim($this->getCurrentPath($subpath), '/'));
                $select = str_replace('°', '&nbsp;&nbsp;', $select->get());
                $elements[] = '<tr>
                    <td class="image"><img src="' . $this->getImage($imagepath, $name, $ext) . '" /></td>
                    <td class="name">
                        <span class="functext">' . $func . '</span>
                        <span class="funcfilename">' . htmlspecialchars($name) . '</span>
                        <span class="functext"> to </span>
                        ' . $select . '
                        
                        <p class="formtext">
                            <span class="formgroup">
                                <input type="text" name="target_file" value="' . htmlspecialchars($targetFile ?: substr($fileInfo->getBasename(), 0, -strlen($ext) - 1)) . '" />
                                <span class="formgroup-addon">
                                    .' . $ext . '
                                </span>
                            </span>
                        </p>
                        
                        <ul class="piped">
                            <li><span class="time">' . $this->readableTime($fileInfo->getMTime()) . '</span></li>
                            <li><span class="size">' . $this->readableFilesize($fileInfo->getSize()) . '</span><li>
                        </ul>
                    </td>
                    <td class="func"><input type="submit" value="' . ucfirst($func) . '" name="submit_' . $func . '"/></td>
                </tr>';
            } else {
                $download_link = $this->getDownloadLink(array('file' => $name, 'path' => $imagepath, 'func' => 'download'));
                $links = array();
                $links[] = '<a href="' . $download_link . '">download</a>';
                if ($this->isAdmin()) {
                    $links[] = '<a href="' . $this->getLink(array('file' => $name, 'func' => 'move', 'search' => $search)) . '">edit/move</a>';
                    $links[] = '<a href="' . $this->getLink(array('file' => $name, 'func' => 'copy', 'search' => $search)) . '">copy</a>';
                    $links[] = '<a href="' . $this->getLink(array('file' => $name, 'func' => 'delete', 'search' => $search)) . '">delete</a>';
                }
                $elements[] = '<tr>
                    <td class="image"><a href="' . $download_link . '"><img src="' . $this->getImage($imagepath, $name, $ext) . '" /></a></td>
                    <td class="name">
                        <p>
                            '.$subpath.'<a href="' . $download_link . '">' . htmlspecialchars($fileInfo->getBasename()) . '</a>
                        </p>                        
                        <ul class="piped">
                            <li><span class="time">' . $this->readableTime($fileInfo->getMTime()) . '</span></li>
                            <li><span class="size">' . $this->readableFilesize($fileInfo->getSize()) . '</span><li>
                        </ul>
                    </td>
                    <td class="func">' . implode('<br />', $links) . '</td>
                </tr>';
            }
        }

        $return = '<h2>Files</h2>';


        if ($error = self::getMessage('file-error')) {
            $return .= '<p class="error"><span>' . $error . '</span></p>';
        }

        if ($info = self::getMessage('file-info')) {
            $return .= '<p class="info"><span>' . $info . '</span></p>';
        }

        if (count($elements) == 0) {
            $elements[] = '<tr><td colspan="3">This folder has no files</td></tr>';
        }

        if ($this->isAdmin()) {
            $elements = array_merge(array($this->getUploadView()), $elements);
        }

        $return .= '
<div class="xform xform-expand xform-clean">
<form action="' . $this->getLink(array('func' => $func, 'file' => $file, 'search' => $search)) . '" method="post" enctype="multipart/form-data">
    <table>
    <thead>
      <tr>
        <th class="image"></th>
        <th class="name">Name</th>
        <th class="func">Function</th>
      </tr>
    </thead>
    <tbody>
      ' . implode('', $elements) . '
    </tbody>
    </table>
</form>
</div>';
        return '<div class="files">' . $return . '</div>';
    }


    public function getUploadView()
    {
        $return = '<tr>
        <td class="image">Upload</td>
        <td class="name"><input name="file[]" type="file"></td>
        <td class="func"><input type="submit" value="Send" name="submit_upload"></td>
      </tr>';
        //
        return $return;

    }





    public function getToolbarView()
    {

        $select = new rex_select();
        $select->setSize(1);
        $select->setName('sort');
        $select->setAttribute('onchange', 'this.form.submit()');
        $select->addOption('Name aufsteigend', 'name-asc');
        $select->addOption('Name absteigend', 'name-desc');
        $select->addOption('Datum aufsteigend', 'date-asc');
        $select->addOption('Datum absteigend', 'date-desc');
        $select->setSelected(rex_get('sort'));


        $return = '
        <div class="xform xform-expand toolbar">
            <form method="GET" action="'.$this->getLink().'">
                <div class="grid-1-3">
                    <div>
                        <p class="formselect">
                            <label>Sortierung:</label>
                            '.$select->get().'
                        </p>
                    </div>
                    <div>
                        <p class="formtext">
                            <span class="formgroup">
                                <input type="text" name="search" value="' . rex_get('search', 'string') . '" />
                                <span class="formgroup-button">
                                    <input type="submit" value="Suchen" />
                                </span>
                            </span>

                            <input type="hidden" name="path" value="'.htmlspecialchars($this->getCurrentPath()).'" />
                        </p>
                    </div>
                </div>
            </form>
        </div>

        ';

        return $return;
    }

    public function getImage($path, $image, $ext)
    {

        if (in_array(strtolower($ext), $this->imageTypes)) {
            return 'index.php?rex_img_type=filebrowser_preview&amp;rex_img_file=' . urlencode($image) . '&amp;rex_com_filebrowser_path=' . urlencode($path);

        } elseif (array_key_exists($ext, $this->mimeTypes)) {
            return '/redaxo/media/' . $this->mimeTypes[$ext];
        }
        return '/redaxo/media/mime-default.gif';

    }


    public function readableTime($time)
    {
        $date = DateTime::createFromFormat('U', $time);
        return $date->format('Y-m-d H:i');

    }


    public function readableFilesize($size)
    {
        $size = $size + 0;
        if ($size == 0) {
            return '0 Bytes';
        }
        $filesizename = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
    }

}
