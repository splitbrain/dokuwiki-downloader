<?php

use splitbrain\PHPArchive\FileInfo;
use splitbrain\PHPArchive\Tar;

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
        echo '__HEADER__';
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
        echo '__FOOTER__';
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

        $this->showText('__STEP0__', $repl);
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
            $this->showText('__STEP1-INTRO__', $repl);

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
        $this->showText('__STEP1-DONE__', $repl);
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
        $this->showText('__STEP2-INTRO__', $repl);


        @set_time_limit(125);
        @ignore_user_abort();

        $tar = new Tar();
        $tar->setCallback(array($this, 'fixPermission'));
        $tar->open($this->tgz);
        $files = $tar->extract($this->install_dir, 1, '/^(_cs|_test)/');

        $repl = array('$COUNT' => count($files));
        $this->showText('__STEP2-DONE__', $repl);
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

        $this->showText('__STEP3__');
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
    }
}