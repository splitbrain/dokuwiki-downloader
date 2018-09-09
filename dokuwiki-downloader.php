<?php
/*> <div style="width:38em; margin: 1em auto; border-left: 3px solid #a00; padding: 0 1em;">

    <h1>Error: No PHP Support</h1>

    <p>
        It seems this server has no PHP support enabled. You need to install and enable PHP before DokuWiki or this
        script will run at all.
    </p>

    <p>
        Contact your hosting provider if you are unsure what this means.
    <p>


    <p>You can learn more at <a href="http://doku.wiki/php">http://doku.wiki/php</a>.</p>
</div> <!--*/
// built 2018-09-09 12:0:14
define('ISBUILD', true);





class GUI
{
    /** @var string the branch to download */
    protected $tag;
    /** @var int permission for directories */
    protected $dperm;
    /** @var int permisson for files */
    protected $fperm;
    /** @var string where to install to */
    protected $install_dir;
    /** @var string where to download from */
    protected $download;
    /** @var string full path to the local download */
    protected $tgz;


    /**
     * Initialize internals
     *
     * @param string $tag the branch to download
     * @param int $dperm permission for directories
     * @param int $fperm permission for files
     */
    public function __construct($tag = 'stable', $dperm = 0777, $fperm = 0666)
    {
        $this->tag = $tag;
        $this->dperm = $dperm;
        $this->fperm = $fperm;

        $this->download = 'https://github.com/splitbrain/dokuwiki/tarball/' . $this->tag;
        $this->install_dir = __DIR__;
        $this->tgz = $this->install_dir . '/dokuwiki-' . $this->tag . '.tgz';
    }

    /**
     * Run the script
     */
    public function run()
    {
        header('Content-type: text/html; charset=utf-8');
        header('X-Robots-Tag: noindex');
        echo '<html>
<head>
    <title>DokuWiki Downloader</title>
    <meta name="robots" content="noindex">
    <style type="text/css">
        /* Sakura.css v1.0.0
 * ================
 * Minimal css theme.
 * Project: https://github.com/oxalorg/sakura
 */
/* Body */
html {
  font-size: 62.5%;
  font-family: serif; }

body {
  font-size: 1.8rem;
  line-height: 1.618;
  max-width: 38em;
  margin: auto;
  color: #4a4a4a;
  background-color: #f9f9f9;
  padding: 13px; }

@media (max-width: 684px) {
  body {
    font-size: 1.53rem; } }

@media (max-width: 382px) {
  body {
    font-size: 1.35rem; } }

h1, h2, h3, h4, h5, h6 {
  line-height: 1.1;
  font-family: Verdana, Geneva, sans-serif;
  font-weight: 700;
  overflow-wrap: break-word;
  word-wrap: break-word;
  -ms-word-break: break-all;
  word-break: break-word;
  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
  hyphens: auto; }

h1 {
  font-size: 2.35em; }

h2 {
  font-size: 2.00em; }

h3 {
  font-size: 1.75em; }

h4 {
  font-size: 1.5em; }

h5 {
  font-size: 1.25em; }

h6 {
  font-size: 1em; }

small, sub, sup {
  font-size: 75%; }

hr {
  border-color: #2c8898; }

a {
  text-decoration: none;
  color: #2c8898; }
  a:hover {
    color: #982c61;
    border-bottom: 2px solid #4a4a4a; }

ul {
  padding-left: 1.4em; }

li {
  margin-bottom: 0.4em; }

blockquote {
  font-style: italic;
  margin-left: 1.5em;
  padding-left: 1em;
  border-left: 3px solid #2c8898; }

img {
  max-width: 100%; }

/* Pre and Code */
pre {
  background-color: #f1f1f1;
  display: block;
  padding: 1em;
  overflow-x: auto; }

code {
  font-size: 0.9em;
  padding: 0 0.5em;
  background-color: #f1f1f1;
  white-space: pre-wrap; }

pre > code {
  padding: 0;
  background-color: transparent;
  white-space: pre; }

/* Tables */
table {
  text-align: justify;
  width: 100%;
  border-collapse: collapse; }

td, th {
  padding: 0.5em;
  border-bottom: 1px solid #f1f1f1; }

/* Buttons, forms and input */
input, textarea {
  border: 1px solid #4a4a4a; }
  input:focus, textarea:focus {
    border: 1px solid #2c8898; }

textarea {
  width: 100%; }

.button, button, input[type="submit"], input[type="reset"], input[type="button"] {
  display: inline-block;
  padding: 5px 10px;
  text-align: center;
  text-decoration: none;
  white-space: nowrap;
  background-color: #2c8898;
  color: #f9f9f9;
  border-radius: 1px;
  border: 1px solid #2c8898;
  cursor: pointer;
  box-sizing: border-box; }
  .button[disabled], button[disabled], input[type="submit"][disabled], input[type="reset"][disabled], input[type="button"][disabled] {
    cursor: default;
    opacity: .5; }
  .button:focus, .button:hover, button:focus, button:hover, input[type="submit"]:focus, input[type="submit"]:hover, input[type="reset"]:focus, input[type="reset"]:hover, input[type="button"]:focus, input[type="button"]:hover {
    background-color: #982c61;
    border-color: #982c61;
    color: #f9f9f9;
    outline: 0; }

textarea, select, input[type] {
  color: #4a4a4a;
  padding: 6px 10px;
  /* The 6px vertically centers text on FF, ignored by Webkit */
  margin-bottom: 10px;
  background-color: #f1f1f1;
  border: 1px solid #f1f1f1;
  border-radius: 4px;
  box-shadow: none;
  box-sizing: border-box; }
  textarea:focus, select:focus, input[type]:focus {
    border: 1px solid #2c8898;
    outline: 0; }

input[type="checkbox"]:focus {
  outline: 1px dotted #2c8898; }

label, legend, fieldset {
  display: block;
  margin-bottom: .5rem;
  font-weight: 600; }
body {
    font-family: sans-serif;
}

.error {
    border-color: #a00;
}

.footer {
    clear: both;
    margin: 8em 0 3em 0;
}

@keyframes hithit {
    0% {
        width: 0;
    }
    100% {
        width: 100%;
    }
}

div.progress {
    position: relative;
    width: 100%;
    height: 1em;
    background: #f1f1f1;
}

div.progress div {
    top: 0;
    left: 0;
    content: \'\';
    position: absolute;
    height: 1em;
    width: 100%;
    background: #2c8898;
    animation: hithit 120s linear;
    text-align: center;
}

blockquote.progress {
    display: none;
}



    </style>
    <script type="application/javascript">
        var TIMER;

/**
 * Start a timer, show error after it runs out
 * @param seconds
 */
function startTimer(seconds) {
    TIMER = window.setTimeout(function () {
        document.querySelector(\'div.progress\').style.display = \'none\';
        document.querySelector(\'blockquote.progress\').style.display = \'block\';
    }, seconds * 1000);
}

/**
 * Stop the timer and hide the progress bar
 */
function stopTimer() {
    if(TIMER) window.clearTimeout(TIMER);
    document.querySelector(\'div.progress\').style.display = \'none\';
}
    </script>
</head>
<body>

';
        try {
            $step = isset($_REQUEST['step']) ? (int)$_REQUEST['step'] : 0;
            switch ($step) {
                case 1:
                    $this->step1();
                    break;
                case 2:
                    $this->step2();
                    break;
                case 3:
                    $this->step3();
                    break;
                default:
                    $this->step0();
            }
        } catch (Exception $e) {
            $this->showError($e);
        }
        echo '

<p class="footer"><small>
    Did this script help you? Consider thanking me via <a href="http://donate.dokuwiki.org/downloader">Paypal</a> or <a
        href="https://www.patreon.com/dokuwiki">Patreon</a> ❤
</small></p>

</body>
</html>';
    }


    /**
     * Just shows the intro
     */
    protected function step0()
    {
        $files = glob($this->install_dir . '/*', GLOB_MARK);
        $repl = array(
            '$TAG' => htmlspecialchars($this->tag),
            '$DIR' => htmlspecialchars($this->install_dir),
            '$FILES' => $this->formatDir($files),
        );

        $this->showText('<h1>DokuWiki Downloader</h1>
<p>
    This script will download <strong>DokuWiki $TAG</strong> to your webserver
    and install it in <strong>$DIR</strong>. That directory currently contains the following files:
</p>

<pre>$FILES</pre>

<p>
    You probably want to install into an empty directory. The script may overwrite any existing file!
</p>

<p>
    If this is not the directory you want to install to, move this script to the target directory before continuing.
</p>


<a href="?step=1" class="button" style="float: right">Step 1: Download the TGZ ⮕</a>

', $repl);
    }


    /**
     * Download the Archive
     */
    protected function step1()
    {
        if (!is_writable($this->install_dir)) {
            throw new \RuntimeException(
                $this->install_dir . 'is not writable.' .
                ' You need to make it writable via FTP or your Webhoster\'s admin panel.'
            );
        }

        // download if file isn't there
        if (!file_exists($this->tgz)) {

            $repl = array(
                '$URL' => $this->download,
                '$TGZ' => $this->tgz,
            );
            $this->showText('<h1>Downloading DokuWiki</h1>
<p>
    Downloading from <a href="$URL">$URL</a>.
    The download can take up to two minutes...
</p>

<div class="progress">
    <div></div>
</div>
<blockquote class="progress error">
    Download failed. Try to upload $TGZ yourself.
</blockquote>
<script type="application/javascript">
    startTimer(120);
</script>
', $repl);

            @set_time_limit(125);
            @ignore_user_abort();

            $http = new HTTPClient();
            $http->timeout = 120;
            $data = $http->get($this->download);
            if (!$data) {
                throw  new RuntimeException("Download failed. Try to upload {$this->tgz} yourself.");
            }

            $fp = @fopen($this->tgz, "w");
            if (!$fp) {
                throw new RuntimeException("Failed to save {$this->tgz}. Try to upload it yourself.");
            }
            fwrite($fp, $data);
            fclose($fp);

        }

        // file should be here now
        $repl = array(
            '$TGZ' => htmlspecialchars($this->tgz),
            '$SIZE' => filesize($this->tgz),
        );
        $this->showText('<script type="application/javascript">
    stopTimer();
</script>

<h2>Ready to unpack</h2>

<p>We\'re ready to unpack the archive (<code>$TGZ</code> sized <code>$SIZE</code> bytes).</p>

<a href="?step=2" class="button" style="float: right">Step 2: Extract the TGZ ⮕</a>

', $repl);
    }

    /**
     * Extract the archive
     *
     * @throws \Exception
     */
    protected function step2()
    {
        if (!is_writable($this->install_dir)) {
            throw new \RuntimeException(
                $this->install_dir . 'is not writable.' .
                ' You need to make it writable via FTP or your Webhoster\'s admin panel.'
            );
        }

        if (!file_exists($this->tgz)) {
            throw new \RuntimeException("There is no {$this->tgz}. Did you skip a step?");
        }

        $repl = array('$TGZ' => htmlspecialchars($this->tgz));
        $this->showText('<h1>Extracting Archive</h1>
<p>
    Extracting the archive <code>$TGZ</code>. This can take a few seconds...
</p>

<div class="progress">
    <div></div>
</div>
<blockquote class="progress error">
    The extraction seems to take much longer than anticipated. The server might have killed the script.
    You can try waiting a bit longer or do a traditional installation instead.
</blockquote>
<script type="application/javascript">
    startTimer(120);
</script>
', $repl);


        @set_time_limit(125);
        @ignore_user_abort();

        $tar = new Tar();
        $tar->setCallback(array($this, 'fixPermission'));
        $tar->open($this->tgz);
        $files = $tar->extract($this->install_dir, 1, '/^(_cs|_test)/');

        $repl = array('$COUNT' => count($files));
        $this->showText('<script type="application/javascript">
    stopTimer();
</script>

<p>
    All <code>$COUNT</code> files have been unpacked, please continue to the clean up step.
</p>

<a href="?step=3" class="button" style="float: right">Step 3: Clean Up ⮕</a>

', $repl);
    }

    /**
     * Delete the Installer
     */
    function step3()
    {
        echo '<h1>Setup Done</h1>';

        if (file_exists($this->tgz)) {
            if (@unlink($this->tgz)) {
                echo '<p>DokuWiki archive file has been automatically deleted</p>';
            } else {
                echo '<blockquote class="error">';
                echo "The DokuWiki archive <code>'.htmlsepcialchars($this->tgz).'</code> could not
                      be deleted automatically, you should remove it yourself.";
                echo '</blockquote>';
            }
        }

        if (@unlink(__FILE__)) {
            echo '<p>The DokuWiki Downloader script has been automatically deleted.</p>';
        } else {
            echo '<blockquote class="error">';
            echo "The DokuWiki archive <code>'.htmlsepcialchars(__FILE__).'</code> could not
                  be deleted automatically, you should remove it yourself.";
            echo '</blockquote>';
        }

        $this->showText('
<p>
    All the downloading and unpacking has been done. It\'s time to configure your shiny new wiki.
</p>

<a href="install.php" class="button" style="float: right">Take me to the DokuWiki Setup ⮕</a>

');
    }


    /**
     * Display a text and optionally replace stuff in it
     *
     * @param string $text
     * @param array $repl
     */
    protected function showText($text, $repl = array())
    {
        $text = str_replace(array_keys($repl), array_values($repl), $text);
        echo $text;
        ob_flush();
        flush();
    }

    /**
     * Adjust the file permissions
     *
     * @param FileInfo $file
     */
    public function fixPermission($file)
    {
        if ($file->getIsdir()) {
            $perm = $this->dperm;
        } else {
            $perm = $this->fperm;
        }
        @chmod($this->install_dir . '/' . $file->getPath(), $perm);
    }

    /**
     * format the given files and directories as list
     *
     * @param $files
     * @return string
     */
    protected function formatDir($files)
    {
        $list = array();
        foreach ($files as $file) {
            if (substr($file, -1) === '/') {
                $dir = '/';
            } else {
                $dir = '';
            }
            $list[] = htmlspecialchars(basename($file) . $dir);
        }
        return join("\n", $list);
    }

    /**
     * @param Exception $e
     */
    protected function showError($e)
    {
        echo '<blockquote class="error">';
        echo '<h2>Error</h2>';
        echo '<b>' . htmlspecialchars($e->getMessage()) . '</b>';
        echo '<p>Looks like something went wrong and the script cannot continue. ';
        echo 'Sorry about that. See if you can fix above problem and try again. ';
        echo 'If you can\'t figure it out, maybe ask in the ';
        echo '<a href="https://forum.dokuwiki.org">Forum</a>?</p>';
        echo '</blockquote>';
        echo '<script type="application/javascript">';
        echo 'stopTimer();';
        echo '</script>';
    }
}




/**
 * The archive is unreadable
 */
class ArchiveCorruptedException extends \Exception
{
}



/**
 * Bad or unsupported compression settings requested
 */
class ArchiveIllegalCompressionException extends \Exception
{
}



/**
 * Read/Write Errors
 */
class ArchiveIOException extends \Exception
{
}



/**
 * File meta data problems
 */
class FileInfoException extends \Exception
{
}



abstract class Archive
{

    const COMPRESS_AUTO = -1;
    const COMPRESS_NONE = 0;
    const COMPRESS_GZIP = 1;
    const COMPRESS_BZIP = 2;

    /** @var callable */
    protected $callback;

    /**
     * Set the compression level and type
     *
     * @param int $level Compression level (0 to 9)
     * @param int $type  Type of compression to use (use COMPRESS_* constants)
     * @throws ArchiveIllegalCompressionException
     */
    abstract public function setCompression($level = 9, $type = Archive::COMPRESS_AUTO);

    /**
     * Open an existing archive file for reading
     *
     * @param string $file
     * @throws ArchiveIOException
     */
    abstract public function open($file);

    /**
     * Read the contents of an archive
     *
     * This function lists the files stored in the archive, and returns an indexed array of FileInfo objects
     *
     * The archive is closed afer reading the contents, because rewinding is not possible in bzip2 streams.
     * Reopen the file with open() again if you want to do additional operations
     *
     * @return FileInfo[]
     */
    abstract public function contents();

    /**
     * Extract an existing archive
     *
     * The $strip parameter allows you to strip a certain number of path components from the filenames
     * found in the archive file, similar to the --strip-components feature of GNU tar. This is triggered when
     * an integer is passed as $strip.
     * Alternatively a fixed string prefix may be passed in $strip. If the filename matches this prefix,
     * the prefix will be stripped. It is recommended to give prefixes with a trailing slash.
     *
     * By default this will extract all files found in the archive. You can restrict the output using the $include
     * and $exclude parameter. Both expect a full regular expression (including delimiters and modifiers). If
     * $include is set, only files that match this expression will be extracted. Files that match the $exclude
     * expression will never be extracted. Both parameters can be used in combination. Expressions are matched against
     * stripped filenames as described above.
     *
     * The archive is closed afterwards. Reopen the file with open() again if you want to do additional operations
     *
     * @param string     $outdir  the target directory for extracting
     * @param int|string $strip   either the number of path components or a fixed prefix to strip
     * @param string     $exclude a regular expression of files to exclude
     * @param string     $include a regular expression of files to include
     * @throws ArchiveIOException
     * @return array
     */
    abstract public function extract($outdir, $strip = '', $exclude = '', $include = '');

    /**
     * Create a new archive file
     *
     * If $file is empty, the archive file will be created in memory
     *
     * @param string $file
     */
    abstract public function create($file = '');

    /**
     * Add a file to the current archive using an existing file in the filesystem
     *
     * @param string          $file     path to the original file
     * @param string|FileInfo $fileinfo either the name to us in archive (string) or a FileInfo oject with all meta data, empty to take from original
     * @throws ArchiveIOException
     */
    abstract public function addFile($file, $fileinfo = '');

    /**
     * Add a file to the current archive using the given $data as content
     *
     * @param string|FileInfo $fileinfo either the name to us in archive (string) or a FileInfo oject with all meta data
     * @param string          $data     binary content of the file to add
     * @throws ArchiveIOException
     */
    abstract public function addData($fileinfo, $data);

    /**
     * Close the archive, close all file handles
     *
     * After a call to this function no more data can be added to the archive, for
     * read access no reading is allowed anymore
     */
    abstract public function close();

    /**
     * Returns the created in-memory archive data
     *
     * This implicitly calls close() on the Archive
     */
    abstract public function getArchive();

    /**
     * Save the created in-memory archive data
     *
     * Note: It is more memory effective to specify the filename in the create() function and
     * let the library work on the new file directly.
     *
     * @param string $file
     */
    abstract public function save($file);

    /**
     * Set a callback function to be called whenever a file is added or extracted.
     *
     * The callback is called with a FileInfo object as parameter. You can use this to show progress
     * info during an operation.
     *
     * @param callable $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }
}




/**
 * Class FileInfo
 *
 * stores meta data about a file in an Archive
 *
 * @author  Andreas Gohr <andi@splitbrain.org>
 * @package splitbrain\PHPArchive
 * @license MIT
 */
class FileInfo
{

    protected $isdir = false;
    protected $path = '';
    protected $size = 0;
    protected $csize = 0;
    protected $mtime = 0;
    protected $mode = 0664;
    protected $owner = '';
    protected $group = '';
    protected $uid = 0;
    protected $gid = 0;
    protected $comment = '';

    /**
     * initialize dynamic defaults
     *
     * @param string $path The path of the file, can also be set later through setPath()
     */
    public function __construct($path = '')
    {
        $this->mtime = time();
        $this->setPath($path);
    }

    /**
     * Factory to build FileInfo from existing file or directory
     *
     * @param string $path path to a file on the local file system
     * @param string $as   optional path to use inside the archive
     * @throws FileInfoException
     * @return FileInfo
     */
    public static function fromPath($path, $as = '')
    {
        clearstatcache(false, $path);

        if (!file_exists($path)) {
            throw new FileInfoException("$path does not exist");
        }

        $stat = stat($path);
        $file = new FileInfo();

        $file->setPath($path);
        $file->setIsdir(is_dir($path));
        $file->setMode(fileperms($path));
        $file->setOwner(fileowner($path));
        $file->setGroup(filegroup($path));
        $file->setSize(filesize($path));
        $file->setUid($stat['uid']);
        $file->setGid($stat['gid']);
        $file->setMtime($stat['mtime']);

        if ($as) {
            $file->setPath($as);
        }

        return $file;
    }

    /**
     * @return int the filesize. always 0 for directories
     */
    public function getSize()
    {
        if($this->isdir) return 0;
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getCompressedSize()
    {
        return $this->csize;
    }

    /**
     * @param int $csize
     */
    public function setCompressedSize($csize)
    {
        $this->csize = $csize;
    }

    /**
     * @return int
     */
    public function getMtime()
    {
        return $this->mtime;
    }

    /**
     * @param int $mtime
     */
    public function setMtime($mtime)
    {
        $this->mtime = $mtime;
    }

    /**
     * @return int
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * @param int $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return boolean
     */
    public function getIsdir()
    {
        return $this->isdir;
    }

    /**
     * @param boolean $isdir
     */
    public function setIsdir($isdir)
    {
        // default mode for directories
        if ($isdir && $this->mode === 0664) {
            $this->mode = 0775;
        }
        $this->isdir = $isdir;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $this->cleanPath($path);
    }

    /**
     * Cleans up a path and removes relative parts, also strips leading slashes
     *
     * @param string $path
     * @return string
     */
    protected function cleanPath($path)
    {
        $path    = str_replace('\\', '/', $path);
        $path    = explode('/', $path);
        $newpath = array();
        foreach ($path as $p) {
            if ($p === '' || $p === '.') {
                continue;
            }
            if ($p === '..') {
                array_pop($newpath);
                continue;
            }
            array_push($newpath, $p);
        }
        return trim(implode('/', $newpath), '/');
    }

    /**
     * Strip given prefix or number of path segments from the filename
     *
     * The $strip parameter allows you to strip a certain number of path components from the filenames
     * found in the tar file, similar to the --strip-components feature of GNU tar. This is triggered when
     * an integer is passed as $strip.
     * Alternatively a fixed string prefix may be passed in $strip. If the filename matches this prefix,
     * the prefix will be stripped. It is recommended to give prefixes with a trailing slash.
     *
     * @param  int|string $strip
     */
    public function strip($strip)
    {
        $filename = $this->getPath();
        $striplen = strlen($strip);
        if (is_int($strip)) {
            // if $strip is an integer we strip this many path components
            $parts = explode('/', $filename);
            if (!$this->getIsdir()) {
                $base = array_pop($parts); // keep filename itself
            } else {
                $base = '';
            }
            $filename = join('/', array_slice($parts, $strip));
            if ($base) {
                $filename .= "/$base";
            }
        } else {
            // if strip is a string, we strip a prefix here
            if (substr($filename, 0, $striplen) == $strip) {
                $filename = substr($filename, $striplen);
            }
        }

        $this->setPath($filename);
    }

    /**
     * Does the file match the given include and exclude expressions?
     *
     * Exclude rules take precedence over include rules
     *
     * @param string $include Regular expression of files to include
     * @param string $exclude Regular expression of files to exclude
     * @return bool
     */
    public function match($include = '', $exclude = '')
    {
        $extract = true;
        if ($include && !preg_match($include, $this->getPath())) {
            $extract = false;
        }
        if ($exclude && preg_match($exclude, $this->getPath())) {
            $extract = false;
        }

        return $extract;
    }
}





/**
 * Class Tar
 *
 * Creates or extracts Tar archives. Supports gz and bzip compression
 *
 * Long pathnames (>100 chars) are supported in POSIX ustar and GNU longlink formats.
 *
 * @author  Andreas Gohr <andi@splitbrain.org>
 * @package splitbrain\PHPArchive
 * @license MIT
 */
class Tar extends Archive
{

    protected $file = '';
    protected $comptype = Archive::COMPRESS_AUTO;
    protected $complevel = 9;
    protected $fh;
    protected $memory = '';
    protected $closed = true;
    protected $writeaccess = false;

    /**
     * Sets the compression to use
     *
     * @param int $level Compression level (0 to 9)
     * @param int $type Type of compression to use (use COMPRESS_* constants)
     * @throws ArchiveIllegalCompressionException
     */
    public function setCompression($level = 9, $type = Archive::COMPRESS_AUTO)
    {
        $this->compressioncheck($type);
        if ($level < -1 || $level > 9) {
            throw new ArchiveIllegalCompressionException('Compression level should be between -1 and 9');
        }
        $this->comptype  = $type;
        $this->complevel = $level;
        if($level == 0) $this->comptype = Archive::COMPRESS_NONE;
        if($type == Archive::COMPRESS_NONE) $this->complevel = 0;
    }

    /**
     * Open an existing TAR file for reading
     *
     * @param string $file
     * @throws ArchiveIOException
     * @throws ArchiveIllegalCompressionException
     */
    public function open($file)
    {
        $this->file = $file;

        // update compression to mach file
        if ($this->comptype == Tar::COMPRESS_AUTO) {
            $this->setCompression($this->complevel, $this->filetype($file));
        }

        // open file handles
        if ($this->comptype === Archive::COMPRESS_GZIP) {
            $this->fh = @gzopen($this->file, 'rb');
        } elseif ($this->comptype === Archive::COMPRESS_BZIP) {
            $this->fh = @bzopen($this->file, 'r');
        } else {
            $this->fh = @fopen($this->file, 'rb');
        }

        if (!$this->fh) {
            throw new ArchiveIOException('Could not open file for reading: '.$this->file);
        }
        $this->closed = false;
    }

    /**
     * Read the contents of a TAR archive
     *
     * This function lists the files stored in the archive
     *
     * The archive is closed afer reading the contents, because rewinding is not possible in bzip2 streams.
     * Reopen the file with open() again if you want to do additional operations
     *
     * @throws ArchiveIOException
     * @throws ArchiveCorruptedException
     * @returns FileInfo[]
     */
    public function contents()
    {
        if ($this->closed || !$this->file) {
            throw new ArchiveIOException('Can not read from a closed archive');
        }

        $result = array();
        while ($read = $this->readbytes(512)) {
            $header = $this->parseHeader($read);
            if (!is_array($header)) {
                continue;
            }

            $this->skipbytes(ceil($header['size'] / 512) * 512);
            $result[] = $this->header2fileinfo($header);
        }

        $this->close();
        return $result;
    }

    /**
     * Extract an existing TAR archive
     *
     * The $strip parameter allows you to strip a certain number of path components from the filenames
     * found in the tar file, similar to the --strip-components feature of GNU tar. This is triggered when
     * an integer is passed as $strip.
     * Alternatively a fixed string prefix may be passed in $strip. If the filename matches this prefix,
     * the prefix will be stripped. It is recommended to give prefixes with a trailing slash.
     *
     * By default this will extract all files found in the archive. You can restrict the output using the $include
     * and $exclude parameter. Both expect a full regular expression (including delimiters and modifiers). If
     * $include is set only files that match this expression will be extracted. Files that match the $exclude
     * expression will never be extracted. Both parameters can be used in combination. Expressions are matched against
     * stripped filenames as described above.
     *
     * The archive is closed afer reading the contents, because rewinding is not possible in bzip2 streams.
     * Reopen the file with open() again if you want to do additional operations
     *
     * @param string $outdir the target directory for extracting
     * @param int|string $strip either the number of path components or a fixed prefix to strip
     * @param string $exclude a regular expression of files to exclude
     * @param string $include a regular expression of files to include
     * @throws ArchiveIOException
     * @throws ArchiveCorruptedException
     * @return FileInfo[]
     */
    public function extract($outdir, $strip = '', $exclude = '', $include = '')
    {
        if ($this->closed || !$this->file) {
            throw new ArchiveIOException('Can not read from a closed archive');
        }

        $outdir = rtrim($outdir, '/');
        @mkdir($outdir, 0777, true);
        if (!is_dir($outdir)) {
            throw new ArchiveIOException("Could not create directory '$outdir'");
        }

        $extracted = array();
        while ($dat = $this->readbytes(512)) {
            // read the file header
            $header = $this->parseHeader($dat);
            if (!is_array($header)) {
                continue;
            }
            $fileinfo = $this->header2fileinfo($header);

            // apply strip rules
            $fileinfo->strip($strip);

            // skip unwanted files
            if (!strlen($fileinfo->getPath()) || !$fileinfo->match($include, $exclude)) {
                $this->skipbytes(ceil($header['size'] / 512) * 512);
                continue;
            }

            // create output directory
            $output    = $outdir.'/'.$fileinfo->getPath();
            $directory = ($fileinfo->getIsdir()) ? $output : dirname($output);
            @mkdir($directory, 0777, true);

            // extract data
            if (!$fileinfo->getIsdir()) {
                $fp = @fopen($output, "wb");
                if (!$fp) {
                    throw new ArchiveIOException('Could not open file for writing: '.$output);
                }

                $size = floor($header['size'] / 512);
                for ($i = 0; $i < $size; $i++) {
                    fwrite($fp, $this->readbytes(512), 512);
                }
                if (($header['size'] % 512) != 0) {
                    fwrite($fp, $this->readbytes(512), $header['size'] % 512);
                }

                fclose($fp);
                @touch($output, $fileinfo->getMtime());
                @chmod($output, $fileinfo->getMode());
            } else {
                $this->skipbytes(ceil($header['size'] / 512) * 512); // the size is usually 0 for directories
            }

            if(is_callable($this->callback)) {
                call_user_func($this->callback, $fileinfo);
            }
            $extracted[] = $fileinfo;
        }

        $this->close();
        return $extracted;
    }

    /**
     * Create a new TAR file
     *
     * If $file is empty, the tar file will be created in memory
     *
     * @param string $file
     * @throws ArchiveIOException
     * @throws ArchiveIllegalCompressionException
     */
    public function create($file = '')
    {
        $this->file   = $file;
        $this->memory = '';
        $this->fh     = 0;

        if ($this->file) {
            // determine compression
            if ($this->comptype == Archive::COMPRESS_AUTO) {
                $this->setCompression($this->complevel, $this->filetype($file));
            }

            if ($this->comptype === Archive::COMPRESS_GZIP) {
                $this->fh = @gzopen($this->file, 'wb'.$this->complevel);
            } elseif ($this->comptype === Archive::COMPRESS_BZIP) {
                $this->fh = @bzopen($this->file, 'w');
            } else {
                $this->fh = @fopen($this->file, 'wb');
            }

            if (!$this->fh) {
                throw new ArchiveIOException('Could not open file for writing: '.$this->file);
            }
        }
        $this->writeaccess = true;
        $this->closed      = false;
    }

    /**
     * Add a file to the current TAR archive using an existing file in the filesystem
     *
     * @param string $file path to the original file
     * @param string|FileInfo $fileinfo either the name to us in archive (string) or a FileInfo oject with all meta data, empty to take from original
     * @throws ArchiveCorruptedException when the file changes while reading it, the archive will be corrupt and should be deleted
     * @throws ArchiveIOException there was trouble reading the given file, it was not added
     * @throws FileInfoException trouble reading file info, it was not added
     */
    public function addFile($file, $fileinfo = '')
    {
        if (is_string($fileinfo)) {
            $fileinfo = FileInfo::fromPath($file, $fileinfo);
        }

        if ($this->closed) {
            throw new ArchiveIOException('Archive has been closed, files can no longer be added');
        }

        $fp = @fopen($file, 'rb');
        if (!$fp) {
            throw new ArchiveIOException('Could not open file for reading: '.$file);
        }

        // create file header
        $this->writeFileHeader($fileinfo);

        // write data
        $read = 0;
        while (!feof($fp)) {
            $data = fread($fp, 512);
            $read += strlen($data);
            if ($data === false) {
                break;
            }
            if ($data === '') {
                break;
            }
            $packed = pack("a512", $data);
            $this->writebytes($packed);
        }
        fclose($fp);

        if($read != $fileinfo->getSize()) {
            $this->close();
            throw new ArchiveCorruptedException("The size of $file changed while reading, archive corrupted. read $read expected ".$fileinfo->getSize());
        }

        if(is_callable($this->callback)) {
            call_user_func($this->callback, $fileinfo);
        }
    }

    /**
     * Add a file to the current TAR archive using the given $data as content
     *
     * @param string|FileInfo $fileinfo either the name to us in archive (string) or a FileInfo oject with all meta data
     * @param string          $data     binary content of the file to add
     * @throws ArchiveIOException
     */
    public function addData($fileinfo, $data)
    {
        if (is_string($fileinfo)) {
            $fileinfo = new FileInfo($fileinfo);
        }

        if ($this->closed) {
            throw new ArchiveIOException('Archive has been closed, files can no longer be added');
        }

        $len = strlen($data);
        $fileinfo->setSize($len);
        $this->writeFileHeader($fileinfo);

        for ($s = 0; $s < $len; $s += 512) {
            $this->writebytes(pack("a512", substr($data, $s, 512)));
        }

        if (is_callable($this->callback)) {
            call_user_func($this->callback, $fileinfo);
        }
    }

    /**
     * Add the closing footer to the archive if in write mode, close all file handles
     *
     * After a call to this function no more data can be added to the archive, for
     * read access no reading is allowed anymore
     *
     * "Physically, an archive consists of a series of file entries terminated by an end-of-archive entry, which
     * consists of two 512 blocks of zero bytes"
     *
     * @link http://www.gnu.org/software/tar/manual/html_chapter/tar_8.html#SEC134
     * @throws ArchiveIOException
     */
    public function close()
    {
        if ($this->closed) {
            return;
        } // we did this already

        // write footer
        if ($this->writeaccess) {
            $this->writebytes(pack("a512", ""));
            $this->writebytes(pack("a512", ""));
        }

        // close file handles
        if ($this->file) {
            if ($this->comptype === Archive::COMPRESS_GZIP) {
                gzclose($this->fh);
            } elseif ($this->comptype === Archive::COMPRESS_BZIP) {
                bzclose($this->fh);
            } else {
                fclose($this->fh);
            }

            $this->file = '';
            $this->fh   = 0;
        }

        $this->writeaccess = false;
        $this->closed      = true;
    }

    /**
     * Returns the created in-memory archive data
     *
     * This implicitly calls close() on the Archive
     * @throws ArchiveIOException
     */
    public function getArchive()
    {
        $this->close();

        if ($this->comptype === Archive::COMPRESS_AUTO) {
            $this->comptype = Archive::COMPRESS_NONE;
        }

        if ($this->comptype === Archive::COMPRESS_GZIP) {
            return gzencode($this->memory, $this->complevel);
        }
        if ($this->comptype === Archive::COMPRESS_BZIP) {
            return bzcompress($this->memory);
        }
        return $this->memory;
    }

    /**
     * Save the created in-memory archive data
     *
     * Note: It more memory effective to specify the filename in the create() function and
     * let the library work on the new file directly.
     *
     * @param string $file
     * @throws ArchiveIOException
     * @throws ArchiveIllegalCompressionException
     */
    public function save($file)
    {
        if ($this->comptype === Archive::COMPRESS_AUTO) {
            $this->setCompression($this->complevel, $this->filetype($file));
        }

        if (!@file_put_contents($file, $this->getArchive())) {
            throw new ArchiveIOException('Could not write to file: '.$file);
        }
    }

    /**
     * Read from the open file pointer
     *
     * @param int $length bytes to read
     * @return string
     */
    protected function readbytes($length)
    {
        if ($this->comptype === Archive::COMPRESS_GZIP) {
            return @gzread($this->fh, $length);
        } elseif ($this->comptype === Archive::COMPRESS_BZIP) {
            return @bzread($this->fh, $length);
        } else {
            return @fread($this->fh, $length);
        }
    }

    /**
     * Write to the open filepointer or memory
     *
     * @param string $data
     * @throws ArchiveIOException
     * @return int number of bytes written
     */
    protected function writebytes($data)
    {
        if (!$this->file) {
            $this->memory .= $data;
            $written = strlen($data);
        } elseif ($this->comptype === Archive::COMPRESS_GZIP) {
            $written = @gzwrite($this->fh, $data);
        } elseif ($this->comptype === Archive::COMPRESS_BZIP) {
            $written = @bzwrite($this->fh, $data);
        } else {
            $written = @fwrite($this->fh, $data);
        }
        if ($written === false) {
            throw new ArchiveIOException('Failed to write to archive stream');
        }
        return $written;
    }

    /**
     * Skip forward in the open file pointer
     *
     * This is basically a wrapper around seek() (and a workaround for bzip2)
     *
     * @param int $bytes seek to this position
     */
    protected function skipbytes($bytes)
    {
        if ($this->comptype === Archive::COMPRESS_GZIP) {
            @gzseek($this->fh, $bytes, SEEK_CUR);
        } elseif ($this->comptype === Archive::COMPRESS_BZIP) {
            // there is no seek in bzip2, we simply read on
            // bzread allows to read a max of 8kb at once
            while($bytes) {
                $toread = min(8192, $bytes);
                @bzread($this->fh, $toread);
                $bytes -= $toread;
            }
        } else {
            @fseek($this->fh, $bytes, SEEK_CUR);
        }
    }

    /**
     * Write the given file meta data as header
     *
     * @param FileInfo $fileinfo
     * @throws ArchiveIOException
     */
    protected function writeFileHeader(FileInfo $fileinfo)
    {
        $this->writeRawFileHeader(
            $fileinfo->getPath(),
            $fileinfo->getUid(),
            $fileinfo->getGid(),
            $fileinfo->getMode(),
            $fileinfo->getSize(),
            $fileinfo->getMtime(),
            $fileinfo->getIsdir() ? '5' : '0'
        );
    }

    /**
     * Write a file header to the stream
     *
     * @param string $name
     * @param int $uid
     * @param int $gid
     * @param int $perm
     * @param int $size
     * @param int $mtime
     * @param string $typeflag Set to '5' for directories
     * @throws ArchiveIOException
     */
    protected function writeRawFileHeader($name, $uid, $gid, $perm, $size, $mtime, $typeflag = '')
    {
        // handle filename length restrictions
        $prefix  = '';
        $namelen = strlen($name);
        if ($namelen > 100) {
            $file = basename($name);
            $dir  = dirname($name);
            if (strlen($file) > 100 || strlen($dir) > 155) {
                // we're still too large, let's use GNU longlink
                $this->writeRawFileHeader('././@LongLink', 0, 0, 0, $namelen, 0, 'L');
                for ($s = 0; $s < $namelen; $s += 512) {
                    $this->writebytes(pack("a512", substr($name, $s, 512)));
                }
                $name = substr($name, 0, 100); // cut off name
            } else {
                // we're fine when splitting, use POSIX ustar
                $prefix = $dir;
                $name   = $file;
            }
        }

        // values are needed in octal
        $uid   = sprintf("%6s ", decoct($uid));
        $gid   = sprintf("%6s ", decoct($gid));
        $perm  = sprintf("%6s ", decoct($perm));
        $size  = sprintf("%11s ", decoct($size));
        $mtime = sprintf("%11s", decoct($mtime));

        $data_first = pack("a100a8a8a8a12A12", $name, $perm, $uid, $gid, $size, $mtime);
        $data_last  = pack("a1a100a6a2a32a32a8a8a155a12", $typeflag, '', 'ustar', '', '', '', '', '', $prefix, "");

        for ($i = 0, $chks = 0; $i < 148; $i++) {
            $chks += ord($data_first[$i]);
        }

        for ($i = 156, $chks += 256, $j = 0; $i < 512; $i++, $j++) {
            $chks += ord($data_last[$j]);
        }

        $this->writebytes($data_first);

        $chks = pack("a8", sprintf("%6s ", decoct($chks)));
        $this->writebytes($chks.$data_last);
    }

    /**
     * Decode the given tar file header
     *
     * @param string $block a 512 byte block containing the header data
     * @return array|false returns false when this was a null block
     * @throws ArchiveCorruptedException
     */
    protected function parseHeader($block)
    {
        if (!$block || strlen($block) != 512) {
            throw new ArchiveCorruptedException('Unexpected length of header');
        }

        // null byte blocks are ignored
        if(trim($block) === '') return false;

        for ($i = 0, $chks = 0; $i < 148; $i++) {
            $chks += ord($block[$i]);
        }

        for ($i = 156, $chks += 256; $i < 512; $i++) {
            $chks += ord($block[$i]);
        }

        $header = @unpack(
            "a100filename/a8perm/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix",
            $block
        );
        if (!$header) {
            throw new ArchiveCorruptedException('Failed to parse header');
        }

        $return['checksum'] = OctDec(trim($header['checksum']));
        if ($return['checksum'] != $chks) {
            throw new ArchiveCorruptedException('Header does not match it\'s checksum');
        }

        $return['filename'] = trim($header['filename']);
        $return['perm']     = OctDec(trim($header['perm']));
        $return['uid']      = OctDec(trim($header['uid']));
        $return['gid']      = OctDec(trim($header['gid']));
        $return['size']     = OctDec(trim($header['size']));
        $return['mtime']    = OctDec(trim($header['mtime']));
        $return['typeflag'] = $header['typeflag'];
        $return['link']     = trim($header['link']);
        $return['uname']    = trim($header['uname']);
        $return['gname']    = trim($header['gname']);

        // Handle ustar Posix compliant path prefixes
        if (trim($header['prefix'])) {
            $return['filename'] = trim($header['prefix']).'/'.$return['filename'];
        }

        // Handle Long-Link entries from GNU Tar
        if ($return['typeflag'] == 'L') {
            // following data block(s) is the filename
            $filename = trim($this->readbytes(ceil($return['size'] / 512) * 512));
            // next block is the real header
            $block  = $this->readbytes(512);
            $return = $this->parseHeader($block);
            // overwrite the filename
            $return['filename'] = $filename;
        }

        return $return;
    }

    /**
     * Creates a FileInfo object from the given parsed header
     *
     * @param $header
     * @return FileInfo
     */
    protected function header2fileinfo($header)
    {
        $fileinfo = new FileInfo();
        $fileinfo->setPath($header['filename']);
        $fileinfo->setMode($header['perm']);
        $fileinfo->setUid($header['uid']);
        $fileinfo->setGid($header['gid']);
        $fileinfo->setSize($header['size']);
        $fileinfo->setMtime($header['mtime']);
        $fileinfo->setOwner($header['uname']);
        $fileinfo->setGroup($header['gname']);
        $fileinfo->setIsdir((bool) $header['typeflag']);

        return $fileinfo;
    }

    /**
     * Checks if the given compression type is available and throws an exception if not
     *
     * @param $comptype
     * @throws ArchiveIllegalCompressionException
     */
    protected function compressioncheck($comptype)
    {
        if ($comptype === Archive::COMPRESS_GZIP && !function_exists('gzopen')) {
            throw new ArchiveIllegalCompressionException('No gzip support available');
        }

        if ($comptype === Archive::COMPRESS_BZIP && !function_exists('bzopen')) {
            throw new ArchiveIllegalCompressionException('No bzip2 support available');
        }
    }

    /**
     * Guesses the wanted compression from the given file
     *
     * Uses magic bytes for existing files, the file extension otherwise
     *
     * You don't need to call this yourself. It's used when you pass Archive::COMPRESS_AUTO somewhere
     *
     * @param string $file
     * @return int
     */
    public function filetype($file)
    {
        // for existing files, try to read the magic bytes
        if(file_exists($file) && is_readable($file) && filesize($file) > 5) {
            $fh = @fopen($file, 'rb');
            if(!$fh) return false;
            $magic = fread($fh, 5);
            fclose($fh);

            if(strpos($magic, "\x42\x5a") === 0) return Archive::COMPRESS_BZIP;
            if(strpos($magic, "\x1f\x8b") === 0) return Archive::COMPRESS_GZIP;
        }

        // otherwise rely on file name
        $file = strtolower($file);
        if (substr($file, -3) == '.gz' || substr($file, -4) == '.tgz') {
            return Archive::COMPRESS_GZIP;
        } elseif (substr($file, -4) == '.bz2' || substr($file, -4) == '.tbz') {
            return Archive::COMPRESS_BZIP;
        }

        return Archive::COMPRESS_NONE;
    }

}

/**
 * HTTP Client
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Goetz <cpuidle@gmx.de>
 */


define('HTTP_NL',"\r\n");

/**
 * Class HTTPClientException
 */
class HTTPClientException extends Exception { }

/**
 * This class implements a basic HTTP client
 *
 * It supports POST and GET, Proxy usage, basic authentication,
 * handles cookies and referers. It is based upon the httpclient
 * function from the VideoDB project.
 *
 * @link   http://www.splitbrain.org/go/videodb
 * @author Andreas Goetz <cpuidle@gmx.de>
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Tobias Sarnowski <sarnowski@new-thoughts.org>
 */
class HTTPClient {
    //set these if you like
    public $agent;         // User agent
    public $http;          // HTTP version defaults to 1.0
    public $timeout;       // read timeout (seconds)
    public $cookies;
    public $referer;
    public $max_redirect;
    public $max_bodysize;
    public $max_bodysize_abort = true;  // if set, abort if the response body is bigger than max_bodysize
    public $header_regexp; // if set this RE must match against the headers, else abort
    public $headers;
    public $debug;
    public $start = 0.0; // for timings
    public $keep_alive = true; // keep alive rocks

    // don't set these, read on error
    public $error;
    public $redirect_count;

    // read these after a successful request
    public $status;
    public $resp_body;
    public $resp_headers;

    // set these to do basic authentication
    public $user;
    public $pass;

    // set these if you need to use a proxy
    public $proxy_host;
    public $proxy_port;
    public $proxy_user;
    public $proxy_pass;
    public $proxy_ssl; //boolean set to true if your proxy needs SSL
    public $proxy_except; // regexp of URLs to exclude from proxy

    // list of kept alive connections
    protected static $connections = array();

    // what we use as boundary on multipart/form-data posts
    protected $boundary = '---DokuWikiHTTPClient--4523452351';

    /**
     * Constructor.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function __construct(){
        $this->agent        = 'Mozilla/4.0 (compatible; DokuWiki HTTP Client; '.PHP_OS.')';
        $this->timeout      = 15;
        $this->cookies      = array();
        $this->referer      = '';
        $this->max_redirect = 3;
        $this->redirect_count = 0;
        $this->status       = 0;
        $this->headers      = array();
        $this->http         = '1.0';
        $this->debug        = false;
        $this->max_bodysize = 0;
        $this->header_regexp= '';
        if(extension_loaded('zlib')) $this->headers['Accept-encoding'] = 'gzip';
        $this->headers['Accept'] = 'text/xml,application/xml,application/xhtml+xml,'.
                                   'text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
        $this->headers['Accept-Language'] = 'en-us';
    }


    /**
     * Simple function to do a GET request
     *
     * Returns the wanted page or false on an error;
     *
     * @param  string $url       The URL to fetch
     * @param  bool   $sloppy304 Return body on 304 not modified
     * @return false|string  response body, false on error
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function get($url,$sloppy304=false){
        if(!$this->sendRequest($url)) return false;
        if($this->status == 304 && $sloppy304) return $this->resp_body;
        if($this->status < 200 || $this->status > 206) return false;
        return $this->resp_body;
    }

    /**
     * Simple function to do a GET request with given parameters
     *
     * Returns the wanted page or false on an error.
     *
     * This is a convenience wrapper around get(). The given parameters
     * will be correctly encoded and added to the given base URL.
     *
     * @param  string $url       The URL to fetch
     * @param  array  $data      Associative array of parameters
     * @param  bool   $sloppy304 Return body on 304 not modified
     * @return false|string  response body, false on error
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function dget($url,$data,$sloppy304=false){
        if(strpos($url,'?')){
            $url .= '&';
        }else{
            $url .= '?';
        }
        $url .= $this->_postEncode($data);
        return $this->get($url,$sloppy304);
    }

    /**
     * Simple function to do a POST request
     *
     * Returns the resulting page or false on an error;
     *
     * @param  string $url       The URL to fetch
     * @param  array  $data      Associative array of parameters
     * @return false|string  response body, false on error
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function post($url,$data){
        if(!$this->sendRequest($url,$data,'POST')) return false;
        if($this->status < 200 || $this->status > 206) return false;
        return $this->resp_body;
    }

    /**
     * Send an HTTP request
     *
     * This method handles the whole HTTP communication. It respects set proxy settings,
     * builds the request headers, follows redirects and parses the response.
     *
     * Post data should be passed as associative array. When passed as string it will be
     * sent as is. You will need to setup your own Content-Type header then.
     *
     * @param  string $url    - the complete URL
     * @param  mixed  $data   - the post data either as array or raw data
     * @param  string $method - HTTP Method usually GET or POST.
     * @return bool - true on success
     *
     * @author Andreas Goetz <cpuidle@gmx.de>
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function sendRequest($url,$data='',$method='GET'){
        $this->start  = $this->_time();
        $this->error  = '';
        $this->status = 0;
        $this->status = 0;
        $this->resp_body = '';
        $this->resp_headers = array();

        // don't accept gzip if truncated bodies might occur
        if($this->max_bodysize &&
           !$this->max_bodysize_abort &&
           $this->headers['Accept-encoding'] == 'gzip'){
            unset($this->headers['Accept-encoding']);
        }

        // parse URL into bits
        $uri = parse_url($url);
        $server = $uri['host'];
        $path   = $uri['path'];
        if(empty($path)) $path = '/';
        if(!empty($uri['query'])) $path .= '?'.$uri['query'];
        if(!empty($uri['port'])) $port = $uri['port'];
        if(isset($uri['user'])) $this->user = $uri['user'];
        if(isset($uri['pass'])) $this->pass = $uri['pass'];

        // proxy setup
        if($this->proxy_host && (!$this->proxy_except || !preg_match('/'.$this->proxy_except.'/i',$url)) ){
            $request_url = $url;
            $server      = $this->proxy_host;
            $port        = $this->proxy_port;
            if (empty($port)) $port = 8080;
            $use_tls     = $this->proxy_ssl;
        }else{
            $request_url = $path;
            if (!isset($port)) $port = ($uri['scheme'] == 'https') ? 443 : 80;
            $use_tls     = ($uri['scheme'] == 'https');
        }

        // add SSL stream prefix if needed - needs SSL support in PHP
        if($use_tls) {
            if(!in_array('ssl', stream_get_transports())) {
                $this->status = -200;
                $this->error = 'This PHP version does not support SSL - cannot connect to server';
            }
            $server = 'ssl://'.$server;
        }

        // prepare headers
        $headers               = $this->headers;
        $headers['Host']       = $uri['host'];
        if(!empty($uri['port'])) $headers['Host'].= ':'.$uri['port'];
        $headers['User-Agent'] = $this->agent;
        $headers['Referer']    = $this->referer;

        if($method == 'POST'){
            if(is_array($data)){
                if (empty($headers['Content-Type'])) {
                    $headers['Content-Type'] = null;
                }
                switch ($headers['Content-Type']) {
                case 'multipart/form-data':
                    $headers['Content-Type']   = 'multipart/form-data; boundary=' . $this->boundary;
                    $data = $this->_postMultipartEncode($data);
                    break;
                default:
                    $headers['Content-Type']   = 'application/x-www-form-urlencoded';
                    $data = $this->_postEncode($data);
                }
            }
        }elseif($method == 'GET'){
            $data = ''; //no data allowed on GET requests
        }

        $contentlength = strlen($data);
        if($contentlength)  {
            $headers['Content-Length'] = $contentlength;
        }

        if($this->user) {
            $headers['Authorization'] = 'Basic '.base64_encode($this->user.':'.$this->pass);
        }
        if($this->proxy_user) {
            $headers['Proxy-Authorization'] = 'Basic '.base64_encode($this->proxy_user.':'.$this->proxy_pass);
        }

        // already connected?
        $connectionId = $this->_uniqueConnectionId($server,$port);
        $this->_debug('connection pool', self::$connections);
        $socket = null;
        if (isset(self::$connections[$connectionId])) {
            $this->_debug('reusing connection', $connectionId);
            $socket = self::$connections[$connectionId];
        }
        if (is_null($socket) || feof($socket)) {
            $this->_debug('opening connection', $connectionId);
            // open socket
            $socket = @fsockopen($server,$port,$errno, $errstr, $this->timeout);
            if (!$socket){
                $this->status = -100;
                $this->error = "Could not connect to $server:$port\n$errstr ($errno)";
                return false;
            }

            // try establish a CONNECT tunnel for SSL
            try {
                if($this->_ssltunnel($socket, $request_url)){
                    // no keep alive for tunnels
                    $this->keep_alive = false;
                    // tunnel is authed already
                    if(isset($headers['Proxy-Authentication'])) unset($headers['Proxy-Authentication']);
                }
            } catch (HTTPClientException $e) {
                $this->status = $e->getCode();
                $this->error = $e->getMessage();
                fclose($socket);
                return false;
            }

            // keep alive?
            if ($this->keep_alive) {
                self::$connections[$connectionId] = $socket;
            } else {
                unset(self::$connections[$connectionId]);
            }
        }

        if ($this->keep_alive && !$this->proxy_host) {
            // RFC 2068, section 19.7.1: A client MUST NOT send the Keep-Alive
            // connection token to a proxy server. We still do keep the connection the
            // proxy alive (well except for CONNECT tunnels)
            $headers['Connection'] = 'Keep-Alive';
        } else {
            $headers['Connection'] = 'Close';
        }

        try {
            //set non-blocking
            stream_set_blocking($socket, 0);

            // build request
            $request  = "$method $request_url HTTP/".$this->http.HTTP_NL;
            $request .= $this->_buildHeaders($headers);
            $request .= $this->_getCookies();
            $request .= HTTP_NL;
            $request .= $data;

            $this->_debug('request',$request);
            $this->_sendData($socket, $request, 'request');

            // read headers from socket
            $r_headers = '';
            do{
                $r_line = $this->_readLine($socket, 'headers');
                $r_headers .= $r_line;
            }while($r_line != "\r\n" && $r_line != "\n");

            $this->_debug('response headers',$r_headers);

            // check if expected body size exceeds allowance
            if($this->max_bodysize && preg_match('/\r?\nContent-Length:\s*(\d+)\r?\n/i',$r_headers,$match)){
                if($match[1] > $this->max_bodysize){
                    if ($this->max_bodysize_abort)
                        throw new HTTPClientException('Reported content length exceeds allowed response size');
                    else
                        $this->error = 'Reported content length exceeds allowed response size';
                }
            }

            // get Status
            if (!preg_match('/^HTTP\/(\d\.\d)\s*(\d+).*?\n/', $r_headers, $m))
                throw new HTTPClientException('Server returned bad answer '.$r_headers);

            $this->status = $m[2];

            // handle headers and cookies
            $this->resp_headers = $this->_parseHeaders($r_headers);
            if(isset($this->resp_headers['set-cookie'])){
                foreach ((array) $this->resp_headers['set-cookie'] as $cookie){
                    list($cookie)   = explode(';',$cookie,2);
                    list($key,$val) = explode('=',$cookie,2);
                    $key = trim($key);
                    if($val == 'deleted'){
                        if(isset($this->cookies[$key])){
                            unset($this->cookies[$key]);
                        }
                    }elseif($key){
                        $this->cookies[$key] = $val;
                    }
                }
            }

            $this->_debug('Object headers',$this->resp_headers);

            // check server status code to follow redirect
            if($this->status == 301 || $this->status == 302 ){
                if (empty($this->resp_headers['location'])){
                    throw new HTTPClientException('Redirect but no Location Header found');
                }elseif($this->redirect_count == $this->max_redirect){
                    throw new HTTPClientException('Maximum number of redirects exceeded');
                }else{
                    // close the connection because we don't handle content retrieval here
                    // that's the easiest way to clean up the connection
                    fclose($socket);
                    unset(self::$connections[$connectionId]);

                    $this->redirect_count++;
                    $this->referer = $url;
                    // handle non-RFC-compliant relative redirects
                    if (!preg_match('/^http/i', $this->resp_headers['location'])){
                        if($this->resp_headers['location'][0] != '/'){
                            $this->resp_headers['location'] = $uri['scheme'].'://'.$uri['host'].':'.$uri['port'].
                                                            dirname($uri['path']).'/'.$this->resp_headers['location'];
                        }else{
                            $this->resp_headers['location'] = $uri['scheme'].'://'.$uri['host'].':'.$uri['port'].
                                                            $this->resp_headers['location'];
                        }
                    }
                    // perform redirected request, always via GET (required by RFC)
                    return $this->sendRequest($this->resp_headers['location'],array(),'GET');
                }
            }

            // check if headers are as expected
            if($this->header_regexp && !preg_match($this->header_regexp,$r_headers))
                throw new HTTPClientException('The received headers did not match the given regexp');

            //read body (with chunked encoding if needed)
            $r_body    = '';
            if(
                (
                    isset($this->resp_headers['transfer-encoding']) &&
                    $this->resp_headers['transfer-encoding'] == 'chunked'
                ) || (
                    isset($this->resp_headers['transfer-coding']) &&
                    $this->resp_headers['transfer-coding'] == 'chunked'
                )
            ) {
                $abort = false;
                do {
                    $chunk_size = '';
                    while (preg_match('/^[a-zA-Z0-9]?$/',$byte=$this->_readData($socket,1,'chunk'))){
                        // read chunksize until \r
                        $chunk_size .= $byte;
                        if (strlen($chunk_size) > 128) // set an abritrary limit on the size of chunks
                            throw new HTTPClientException('Allowed response size exceeded');
                    }
                    $this->_readLine($socket, 'chunk');     // readtrailing \n
                    $chunk_size = hexdec($chunk_size);

                    if($this->max_bodysize && $chunk_size+strlen($r_body) > $this->max_bodysize){
                        if ($this->max_bodysize_abort)
                            throw new HTTPClientException('Allowed response size exceeded');
                        $this->error = 'Allowed response size exceeded';
                        $chunk_size = $this->max_bodysize - strlen($r_body);
                        $abort = true;
                    }

                    if ($chunk_size > 0) {
                        $r_body .= $this->_readData($socket, $chunk_size, 'chunk');
                        $this->_readData($socket, 2, 'chunk'); // read trailing \r\n
                    }
                } while ($chunk_size && !$abort);
            }elseif(isset($this->resp_headers['content-length']) && !isset($this->resp_headers['transfer-encoding'])){
                /* RFC 2616
                 * If a message is received with both a Transfer-Encoding header field and a Content-Length
                 * header field, the latter MUST be ignored.
                 */

                // read up to the content-length or max_bodysize
                // for keep alive we need to read the whole message to clean up the socket for the next read
                if(
                    !$this->keep_alive &&
                    $this->max_bodysize &&
                    $this->max_bodysize < $this->resp_headers['content-length']
                ) {
                    $length = $this->max_bodysize;
                }else{
                    $length = $this->resp_headers['content-length'];
                }

                $r_body = $this->_readData($socket, $length, 'response (content-length limited)', true);
            }elseif( !isset($this->resp_headers['transfer-encoding']) && $this->max_bodysize && !$this->keep_alive){
                $r_body = $this->_readData($socket, $this->max_bodysize, 'response (content-length limited)', true);
            } elseif ((int)$this->status === 204) {
                // request has no content
            } else{
                // read entire socket
                while (!feof($socket)) {
                    $r_body .= $this->_readData($socket, 4096, 'response (unlimited)', true);
                }
            }

            // recheck body size, we might had to read the whole body, so we abort late or trim here
            if($this->max_bodysize){
                if(strlen($r_body) > $this->max_bodysize){
                    if ($this->max_bodysize_abort) {
                        throw new HTTPClientException('Allowed response size exceeded');
                    } else {
                        $this->error = 'Allowed response size exceeded';
                    }
                }
            }

        } catch (HTTPClientException $err) {
            $this->error = $err->getMessage();
            if ($err->getCode())
                $this->status = $err->getCode();
            unset(self::$connections[$connectionId]);
            fclose($socket);
            return false;
        }

        if (!$this->keep_alive ||
                (isset($this->resp_headers['connection']) && $this->resp_headers['connection'] == 'Close')) {
            // close socket
            fclose($socket);
            unset(self::$connections[$connectionId]);
        }

        // decode gzip if needed
        if(isset($this->resp_headers['content-encoding']) &&
           $this->resp_headers['content-encoding'] == 'gzip' &&
           strlen($r_body) > 10 && substr($r_body,0,3)=="\x1f\x8b\x08"){
            $this->resp_body = @gzinflate(substr($r_body, 10));
            if($this->resp_body === false){
                $this->error = 'Failed to decompress gzip encoded content';
                $this->resp_body = $r_body;
            }
        }else{
            $this->resp_body = $r_body;
        }

        $this->_debug('response body',$this->resp_body);
        $this->redirect_count = 0;
        return true;
    }

    /**
     * Tries to establish a CONNECT tunnel via Proxy
     *
     * Protocol, Servername and Port will be stripped from the request URL when a successful CONNECT happened
     *
     * @param resource &$socket
     * @param string   &$requesturl
     * @throws HTTPClientException when a tunnel is needed but could not be established
     * @return bool true if a tunnel was established
     */
    protected function _ssltunnel(&$socket, &$requesturl){
        if(!$this->proxy_host) return false;
        $requestinfo = parse_url($requesturl);
        if($requestinfo['scheme'] != 'https') return false;
        if(!$requestinfo['port']) $requestinfo['port'] = 443;

        // build request
        $request  = "CONNECT {$requestinfo['host']}:{$requestinfo['port']} HTTP/1.0".HTTP_NL;
        $request .= "Host: {$requestinfo['host']}".HTTP_NL;
        if($this->proxy_user) {
            $request .= 'Proxy-Authorization: Basic '.base64_encode($this->proxy_user.':'.$this->proxy_pass).HTTP_NL;
        }
        $request .= HTTP_NL;

        $this->_debug('SSL Tunnel CONNECT',$request);
        $this->_sendData($socket, $request, 'SSL Tunnel CONNECT');

        // read headers from socket
        $r_headers = '';
        do{
            $r_line = $this->_readLine($socket, 'headers');
            $r_headers .= $r_line;
        }while($r_line != "\r\n" && $r_line != "\n");

        $this->_debug('SSL Tunnel Response',$r_headers);
        if(preg_match('/^HTTP\/1\.[01] 200/i',$r_headers)){
            // set correct peer name for verification (enabled since PHP 5.6)
            stream_context_set_option($socket, 'ssl', 'peer_name', $requestinfo['host']);

            // SSLv3 is broken, use only TLS connections.
            // @link https://bugs.php.net/69195
            if (PHP_VERSION_ID >= 50600 && PHP_VERSION_ID <= 50606) {
                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            } else {
                // actually means neither SSLv2 nor SSLv3
                $cryptoMethod = STREAM_CRYPTO_METHOD_SSLv23_CLIENT;
            }

            if (@stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
                $requesturl = $requestinfo['path'].
                  (!empty($requestinfo['query'])?'?'.$requestinfo['query']:'');
                return true;
            }

            throw new HTTPClientException(
                'Failed to set up crypto for secure connection to '.$requestinfo['host'], -151
            );
        }

        throw new HTTPClientException('Failed to establish secure proxy connection', -150);
    }

    /**
     * Safely write data to a socket
     *
     * @param  resource $socket     An open socket handle
     * @param  string   $data       The data to write
     * @param  string   $message    Description of what is being read
     * @throws HTTPClientException
     *
     * @author Tom N Harris <tnharris@whoopdedo.org>
     */
    protected function _sendData($socket, $data, $message) {
        // send request
        $towrite = strlen($data);
        $written = 0;
        while($written < $towrite){
            // check timeout
            $time_used = $this->_time() - $this->start;
            if($time_used > $this->timeout)
                throw new HTTPClientException(sprintf('Timeout while sending %s (%.3fs)',$message, $time_used), -100);
            if(feof($socket))
                throw new HTTPClientException("Socket disconnected while writing $message");

            // select parameters
            $sel_r = null;
            $sel_w = array($socket);
            $sel_e = null;
            // wait for stream ready or timeout (1sec)
            if(@stream_select($sel_r,$sel_w,$sel_e,1) === false){
                 usleep(1000);
                 continue;
            }

            // write to stream
            $nbytes = fwrite($socket, substr($data,$written,4096));
            if($nbytes === false)
                throw new HTTPClientException("Failed writing to socket while sending $message", -100);
            $written += $nbytes;
        }
    }

    /**
     * Safely read data from a socket
     *
     * Reads up to a given number of bytes or throws an exception if the
     * response times out or ends prematurely.
     *
     * @param  resource $socket     An open socket handle in non-blocking mode
     * @param  int      $nbytes     Number of bytes to read
     * @param  string   $message    Description of what is being read
     * @param  bool     $ignore_eof End-of-file is not an error if this is set
     * @throws HTTPClientException
     * @return string
     *
     * @author Tom N Harris <tnharris@whoopdedo.org>
     */
    protected function _readData($socket, $nbytes, $message, $ignore_eof = false) {
        $r_data = '';
        // Does not return immediately so timeout and eof can be checked
        if ($nbytes < 0) $nbytes = 0;
        $to_read = $nbytes;
        do {
            $time_used = $this->_time() - $this->start;
            if ($time_used > $this->timeout)
                throw new HTTPClientException(
                        sprintf('Timeout while reading %s after %d bytes (%.3fs)', $message,
                                strlen($r_data), $time_used), -100);
            if(feof($socket)) {
                if(!$ignore_eof)
                    throw new HTTPClientException("Premature End of File (socket) while reading $message");
                break;
            }

            if ($to_read > 0) {
                // select parameters
                $sel_r = array($socket);
                $sel_w = null;
                $sel_e = null;
                // wait for stream ready or timeout (1sec)
                if(@stream_select($sel_r,$sel_w,$sel_e,1) === false){
                     usleep(1000);
                     continue;
                }

                $bytes = fread($socket, $to_read);
                if($bytes === false)
                    throw new HTTPClientException("Failed reading from socket while reading $message", -100);
                $r_data .= $bytes;
                $to_read -= strlen($bytes);
            }
        } while ($to_read > 0 && strlen($r_data) < $nbytes);
        return $r_data;
    }

    /**
     * Safely read a \n-terminated line from a socket
     *
     * Always returns a complete line, including the terminating \n.
     *
     * @param  resource $socket     An open socket handle in non-blocking mode
     * @param  string   $message    Description of what is being read
     * @throws HTTPClientException
     * @return string
     *
     * @author Tom N Harris <tnharris@whoopdedo.org>
     */
    protected function _readLine($socket, $message) {
        $r_data = '';
        do {
            $time_used = $this->_time() - $this->start;
            if ($time_used > $this->timeout)
                throw new HTTPClientException(
                        sprintf('Timeout while reading %s (%.3fs) >%s<', $message, $time_used, $r_data),
                        -100);
            if(feof($socket))
                throw new HTTPClientException("Premature End of File (socket) while reading $message");

            // select parameters
            $sel_r = array($socket);
            $sel_w = null;
            $sel_e = null;
            // wait for stream ready or timeout (1sec)
            if(@stream_select($sel_r,$sel_w,$sel_e,1) === false){
                 usleep(1000);
                 continue;
            }

            $r_data = fgets($socket, 1024);
        } while (!preg_match('/\n$/',$r_data));
        return $r_data;
    }

    /**
     * print debug info
     *
     * Uses _debug_text or _debug_html depending on the SAPI name
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param string $info
     * @param mixed  $var
     */
    protected function _debug($info,$var=null){
        if(!$this->debug) return;
        if(php_sapi_name() == 'cli'){
            $this->_debug_text($info, $var);
        }else{
            $this->_debug_html($info, $var);
        }
    }

    /**
     * print debug info as HTML
     *
     * @param string $info
     * @param mixed  $var
     */
    protected function _debug_html($info, $var=null){
        print '<b>'.$info.'</b> '.($this->_time() - $this->start).'s<br />';
        if(!is_null($var)){
            ob_start();
            print_r($var);
            $content = htmlspecialchars(ob_get_contents());
            ob_end_clean();
            print '<pre>'.$content.'</pre>';
        }
    }

    /**
     * prints debug info as plain text
     *
     * @param string $info
     * @param mixed  $var
     */
    protected function _debug_text($info, $var=null){
        print '*'.$info.'* '.($this->_time() - $this->start)."s\n";
        if(!is_null($var)) print_r($var);
        print "\n-----------------------------------------------\n";
    }

    /**
     * Return current timestamp in microsecond resolution
     *
     * @return float
     */
    protected static function _time(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * convert given header string to Header array
     *
     * All Keys are lowercased.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param string $string
     * @return array
     */
    protected function _parseHeaders($string){
        $headers = array();
        $lines = explode("\n",$string);
        array_shift($lines); //skip first line (status)
        foreach($lines as $line){
            @list($key, $val) = explode(':',$line,2);
            $key = trim($key);
            $val = trim($val);
            $key = strtolower($key);
            if(!$key) continue;
            if(isset($headers[$key])){
                if(is_array($headers[$key])){
                    $headers[$key][] = $val;
                }else{
                    $headers[$key] = array($headers[$key],$val);
                }
            }else{
                $headers[$key] = $val;
            }
        }
        return $headers;
    }

    /**
     * convert given header array to header string
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param array $headers
     * @return string
     */
    protected function _buildHeaders($headers){
        $string = '';
        foreach($headers as $key => $value){
            if($value === '') continue;
            $string .= $key.': '.$value.HTTP_NL;
        }
        return $string;
    }

    /**
     * get cookies as http header string
     *
     * @author Andreas Goetz <cpuidle@gmx.de>
     *
     * @return string
     */
    protected function _getCookies(){
        $headers = '';
        foreach ($this->cookies as $key => $val){
            $headers .= "$key=$val; ";
        }
        $headers = substr($headers, 0, -2);
        if ($headers) $headers = "Cookie: $headers".HTTP_NL;
        return $headers;
    }

    /**
     * Encode data for posting
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param array $data
     * @return string
     */
    protected function _postEncode($data){
        return http_build_query($data,'','&');
    }

    /**
     * Encode data for posting using multipart encoding
     *
     * @fixme use of urlencode might be wrong here
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param array $data
     * @return string
     */
    protected function _postMultipartEncode($data){
        $boundary = '--'.$this->boundary;
        $out = '';
        foreach($data as $key => $val){
            $out .= $boundary.HTTP_NL;
            if(!is_array($val)){
                $out .= 'Content-Disposition: form-data; name="'.urlencode($key).'"'.HTTP_NL;
                $out .= HTTP_NL; // end of headers
                $out .= $val;
                $out .= HTTP_NL;
            }else{
                $out .= 'Content-Disposition: form-data; name="'.urlencode($key).'"';
                if($val['filename']) $out .= '; filename="'.urlencode($val['filename']).'"';
                $out .= HTTP_NL;
                if($val['mimetype']) $out .= 'Content-Type: '.$val['mimetype'].HTTP_NL;
                $out .= HTTP_NL; // end of headers
                $out .= $val['body'];
                $out .= HTTP_NL;
            }
        }
        $out .= "$boundary--".HTTP_NL;
        return $out;
    }

    /**
     * Generates a unique identifier for a connection.
     *
     * @param  string $server
     * @param  string $port
     * @return string unique identifier
     */
    protected function _uniqueConnectionId($server, $port) {
        return "$server:$port";
    }
}


/**
 * Adds DokuWiki specific configs to the HTTP client
 *
 * @author Andreas Goetz <cpuidle@gmx.de>
 */
class DokuHTTPClient extends HTTPClient {

    /**
     * Constructor.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function __construct(){
        global $conf;

        // call parent constructor
        parent::__construct();

        // set some values from the config
        $this->proxy_host   = $conf['proxy']['host'];
        $this->proxy_port   = $conf['proxy']['port'];
        $this->proxy_user   = $conf['proxy']['user'];
        $this->proxy_pass   = conf_decodeString($conf['proxy']['pass']);
        $this->proxy_ssl    = $conf['proxy']['ssl'];
        $this->proxy_except = $conf['proxy']['except'];

        // allow enabling debugging via URL parameter (if debugging allowed)
        if($conf['allowdebug']) {
            if(
                isset($_REQUEST['httpdebug']) ||
                (
                    isset($_SERVER['HTTP_REFERER']) &&
                    strpos($_SERVER['HTTP_REFERER'], 'httpdebug') !== false
                )
            ) {
                $this->debug = true;
            }
        }
    }


    /**
     * Wraps an event around the parent function
     *
     * @triggers HTTPCLIENT_REQUEST_SEND
     * @author   Andreas Gohr <andi@splitbrain.org>
     */
    /**
     * @param string $url
     * @param string|array $data the post data either as array or raw data
     * @param string $method
     * @return bool
     */
    public function sendRequest($url,$data='',$method='GET'){
        $httpdata = array('url'    => $url,
                          'data'   => $data,
                          'method' => $method);
        $evt = new Doku_Event('HTTPCLIENT_REQUEST_SEND',$httpdata);
        if($evt->advise_before()){
            $url    = $httpdata['url'];
            $data   = $httpdata['data'];
            $method = $httpdata['method'];
        }
        $evt->advise_after();
        unset($evt);
        return parent::sendRequest($url,$data,$method);
    }

}

//Setup VIM: ex: et ts=4 :


$gui = new GUI();
$gui->run();
//-->
