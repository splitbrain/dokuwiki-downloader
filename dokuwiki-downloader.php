<?php
/**
 * This single script can be uploaded to you webhost and then be called from
 * the browser. It will download and extract DokuWiki for you
 */

$VERSION = '2009-02-14'; // the DokuWiki version to be downloaded

// these are *very* relaxed permissions by default. They make sure you will
// be able to delete or update your wiki through FTP.
$DPERM   = 0777;
$FPERM   = 0666;

// --- LIBS -----------------------------------------------------------------
// Below are two libraries, ripped from the DokuWiki sources and slightly
// modified. They are used for downloading and extracting.
//
// The main code comes at the end of this file, search for MAIN
// --------------------------------------------------------------------------

/**
 * TAR format class - Creates TAR archives
 *
 * This class is part or the MaxgComp suite and originally named
 * MaxgTar class.
 *
 * Modified for Dokuwiki
 *
 * @license GPL
 * @link    http://docs.maxg.info
 * @author  Bouchon <tarlib@bouchon.org> (Maxg)
 * @author  Christopher Smith <chris@jalakai.co.uk>
 */
define('COMPRESS_GZIP',1);
define('COMPRESS_BZIP',2);
define('COMPRESS_AUTO',3);
define('COMPRESS_NONE',0);
define('TARLIB_VERSION','1.2');
define('FULL_ARCHIVE',-1);
define('ARCHIVE_DYNAMIC',0);
define('ARCHIVE_RENAMECOMP',5);
define('COMPRESS_DETECT',-1);

class TarLib
{
  var $_comptype;
  var $_compzlevel;
  var $_fp;
  var $_memdat;
  var $_nomf;
  var $_result;
  var $_initerror;

  /**
   * constructor, initialize the class
   *
   * The constructor initialize the variables and prepare the class for the
   * archive, and return the object created. Note that you can use multiple
   * instances of the MaxgTar class, if you call this function another time and
   * store the object in an other variable.
   *
   * In fact, MaxgTar accepts the following arguments (all are optional) :
   *
   * filename can be either a file name (absolute or relative). In this
   * case, it can be used both for reading and writing. You can also open
   * remote archive if you add a protocole name at the beginning of the file
   * (ie https://host.dom/archive.tar.gz), but for reading only and if the
   * directive allow_url_fopen is enabled in PHP.INI (this can be checked with
   * TarInfo()). If you pass a file that doesn't exist, the script
   * will try to create it. If the archive already exists and contains files,
   * you can use Add() to append files.But by default this parameter
   * is ARCHIVE_DYNAMIC (write only) so the archive is created in memory and
   * can be sent to a file [writeArchive()] or to the client
   * [sendClient()]
   *
   * compression_type should be a constant that represents a type of
   * compression, or its integer value. The different values are described in
   * the constants.
   *
   * compression_level is an integer between 1 and 9 (by default) an
   * represent the GZIP or BZIP compression level.  1 produce fast compression,
   * and 9 produce smaller files. See the RFC 1952 for more infos.
   */
  function tarlib($p_filen = ARCHIVE_DYNAMIC , $p_comptype = COMPRESS_AUTO, $p_complevel = 9)
  {
    $this->_initerror = 0;
    $this->_nomf = $p_filen; $flag=0;
    if($p_comptype && $p_comptype % 5 == 0){$p_comptype /= ARCHIVE_RENAMECOMP; $flag=1;}

    if($p_complevel > 0 && $p_complevel <= 9) $this->_compzlevel = $p_complevel;
    else $p_complevel = 9;

    if($p_comptype == COMPRESS_DETECT)
    {
      if(strtolower(substr($p_filen,-3)) == '.gz') $p_comptype = COMPRESS_GZIP;
      elseif(strtolower(substr($p_filen,-4)) == '.bz2') $p_comptype = COMPRESS_BZIP;
      else $p_comptype = COMPRESS_NONE;
    }

    switch($p_comptype)
    {
      case COMPRESS_GZIP:
        if(!extension_loaded('zlib')) $this->_initerror = -1;
        $this->_comptype = COMPRESS_GZIP;
      break;

      case COMPRESS_BZIP:
        if(!extension_loaded('bz2')) $this->_inierror = -2;
        $this->_comptype = COMPRESS_BZIP;
      break;

      case COMPRESS_AUTO:
        if(extension_loaded('zlib'))
          $this->_comptype = COMPRESS_GZIP;
        elseif(extension_loaded('bz2'))
          $this->_comptype = COMPRESS_BZIP;
        else
          $this->_comptype = COMPRESS_NONE;
      break;

      default:
        $this->_comptype = COMPRESS_NONE;
    }

    if($this->_init_error < 0) $this->_comptype = COMPRESS_NONE;

    if($flag) $this->_nomf.= '.'.$this->getCompression(1);
    $this->_result = true;
  }

  /**
   * Recycle a TAR object.
   *
   * This function does exactly the same as TarLib (constructor), except it
   * returns a status code.
   */
  function setArchive($p_name='', $p_comp = COMPRESS_AUTO, $p_level=9)
  {
    $this->_CompTar();
    $this->TarLib($p_name, $p_comp, $p_level);
    return $this->_result;
  }

  /**
   * Get the compression used to generate the archive
   *
   * This is a very useful function when you're using dynamical archives.
   * Besides, if you let the script chose which compression to use, you'll have
   * a problem when you'll want to send it to the client if you don't know
   * which compression was used.
   *
   * There are two ways to call this function : if you call it without argument
   * or with FALSE, it will return the compression constants, explained on the
   * MaxgTar Constants.  If you call it with GetExtension on TRUE it will
   * return the extension without starting dot (ie "tar" or "tar.bz2" or
   * "tar.gz")
   *
   * NOTE: This can be done with the flag ARCHIVE_RENAMECOMP, see the
   * MaxgTar Constants
   */
  function getCompression($ext = false)
  {
    $exts = Array('tar','tar.gz','tar.bz2');
    if($ext) return $exts[$this->_comptype];
    return $this->_comptype;
  }

  /**
   * Change the compression mode.
   *
   * This function will change the compression methode to read or write
   * the archive. See the MaxgTar Constants to see which constants you can use.
   * It may look strange, but it returns the GZIP compression level.
   */
  function setCompression($p_comp = COMPRESS_AUTO)
  {
    $this->setArchive($this->_nomf, $p_comp, $this->_compzlevel);
    return $this->_compzlevel;
  }

  /**
   * Returns the compressed dynamic archive.
   *
   * When you're working with dynamic archives, use this function to grab
   * the final compressed archive in a string ready to be put in a SQL table or
   * in a file.
   */
  function getDynamicArchive()
  {
    return $this->_encode($this->_memdat);
  }

  /**
   * Write a dynamical archive into a file
   *
   * This function attempts to write a dynamicaly-genrated archive into
   * TargetFile on the webserver.  It returns a TarErrorStr() status
   * code.
   *
   * To know the extension to add to the file if you're using AUTO_DETECT
   * compression, you can use getCompression().
   */
  function writeArchive($p_archive) {
    if(!$this->_memdat) return -7;
    $fp = @fopen($p_archive, 'wb');
    if(!$fp) return -6;

    fwrite($fp, $this->_memdat);
    fclose($fp);

    return true;
  }

  /**
   * Send a TAR archive to the client browser.
   *
   * This function will send an archive to the client, and return a status
   * code, but can behave differently depending on the arguments you give. All
   * arguments are optional.
   *
   * ClientName is used to specify the archive name to give to the browser. If
   * you don't give one, it will send the constructor filename or return an
   * error code in case of dynamical archive.
   *
   * FileName is optional and used to send a specific archive. Leave it blank
   * to send dynamical archives or the current working archive.
   *
   * If SendHeaders is enabled (by default), the library will send the HTTP
   * headers itself before it sends the contents. This headers are :
   * Content-Type, Content-Disposition, Content-Length and Accept-Range.
   *
   * Please note that this function DOES NOT stops the script so don't forget
   * to exit() to avoid your script sending other data and corrupt the archive.
   * Another note : for AUTO_DETECT dynamical archives you can know the
   * extension to add to the name with getCompression()
   */
  function sendClient($name = '', $archive = '', $headers = true)
  {
    if(!$name && !$this->_nomf) return -9;
    if(!$archive && !$this->_memdat) return -10;
    if(!$name) $name = basename($this->_nomf);

    if($archive){ if(!file_exists($archive)) return -11; }
    else $decoded = $this->getDynamicArchive();

    if($headers)
    {
      header('Content-Type: application/x-gtar');
      header('Content-Disposition: attachment; filename='.basename($name));
      header('Accept-Ranges: bytes');
      header('Content-Length: '.($archive ? filesize($archive) : strlen($decoded)));
    }

    if($archive)
    {
      $fp = @fopen($archive,'rb');
      if(!$fp) return -4;

      while(!foef($fp)) echo fread($fp,2048);
    }
    else
    {
      echo $decoded;
    }

    return true;
  }

  /**
   * Extract part or totality of the archive.
   *
   * This function can extract files from an archive, and returns then a
   * status codes that can be converted with TarErrorStr() into a
   * human readable message.
   *
   * Only the first argument is required, What and it can be either the
   * constant FULL_ARCHIVE or an indexed array containing each file you want to
   * extract.
   *
   * To contains the target folder to extract the archive. It is optional and
   * the default value is '.' which means the current folder. If the target
   * folder doesn't exist, the script attempts to create it and give it
   * permissions 0777 by default.
   *
   * RemovePath is very usefull when you want to extract files from a subfoler
   * in the archive to a root folder. For instance, if you have a file in the
   * archive called some/sub/folder/test.txt and you want to extract it to the
   * script folder, you can call Extract with To = '.' and RemovePath =
   * 'some/sub/folder/'
   *
   * FileMode is optional and its default value is 0755. It is in fact the UNIX
   * permission in octal mode (prefixed with a 0) that will be given on each
   * extracted file.
   */
  function Extract($p_what = FULL_ARCHIVE, $p_to = '.', $p_remdir='', $p_mode = 0755)
  {
    if(!$this->_OpenRead()) return -4;
//  if(!@is_dir($p_to)) if(!@mkdir($p_to, 0777)) return -8;   --CS
    if(!@is_dir($p_to)) if(!$this->_dirApp($p_to)) return -8;   //--CS (route through correct dir fn)

    $ok = $this->_extractList($p_to, $p_what, $p_remdir, $p_mode);
    $this->_CompTar();

    return $ok;
  }

  /**
   * Create a new package with the given files
   *
   * This function will attempt to create a new archive with global headers
   * then add the given files into.  If the archive is a real file, the
   * contents are written directly into the file, if it is a dynamic archive
   * contents are only stored in memory. This function should not be used to
   * add files to an existing archive, you should use Add() instead.
   *
   * The FileList supports actually three différents modes :
   *
   * - You can pass a string containing filenames separated by pipes '|'.
   *   In this case the file are read from the webserver filesystem and the
   *   root folder is the folder where the script using the MaxgTar is called.
   *
   * - You can also give a unidimensional indexed array containing the
   *   filenames. The behaviour for the content reading is the same that a
   *   '|'ed string.
   *
   * - The more useful usage is to pass bidimentional arrays, where the
   *   first element contains the filename and the second contains the file
   *   contents. You can even add empty folders to the package if the filename
   *   has a leading '/'. Once again, have a look at the exemples to understand
   *   better.
   *
   * Note you can also give arrays with both dynamic contents and static files.
   *
   * The optional parameter RemovePath can be used to delete a part of the tree
   * of the filename you're adding, for instance if you're adding in the root
   * of a package a file that is stored somewhere in the server tree.
   *
   * On the contrary the parameter AddPath can be used to add a prefix folder
   * to the file you store. Note also that the RemovePath is applied before the
   * AddPath is added, so it HAS a sense to use both parameters together.
   */
  function Create($p_filelist,$p_add='',$p_rem='')
  {
    if(!$fl = $this->_fetchFilelist($p_filelist)) return -5;
    if(!$this->_OpenWrite()) return -6;

    $ok = $this->_addFileList($fl,$p_add,$p_rem);

    if($ok) $this->_writeFooter();
    else{ $this->_CompTar(); @unlink($this->_nomf); }

    return $ok;
  }

  /**
   * Add files to an existing package.
   *
   * This function does exactly the same than Create() exept it
   * will append the given files at the end of the archive.  Please not the is
   * safe to call Add() on a newly created archive whereas the
   * contrary will fail !
   *
   * This function returns a status code, you can use TarErrorStr() on
   * it to get the human-readable description of the error.
   */
  function Add($p_filelist, $p_add = '', $p_rem = '') { if (($this->_nomf
!= ARCHIVE_DYNAMIC && @is_file($this->_nomf)) || ($this->_nomf ==
ARCHIVE_DYNAMIC && !$this->_memdat)) return $this->Create($p_filelist,
$p_add, $p_rem);

    if(!$fl = $this->_fetchFilelist($p_filelist)) return -5;
    return $this->_append($fl, $p_add, $p_rem);
  }

  /**
   * Read the contents of a TAR archive
   *
   * This function attempts to get the list of the files stored in the
   * archive, and return either an error code or an indexed array of
   * associative array containing for each file the following informations :
   *
   * checksum    Tar Checksum of the file
   * filename    The full name of the stored file (up to 100 c.)
   * mode        UNIX permissions in DECIMAL, not octal
   * uid         The Owner ID
   * gid         The Group ID
   * size        Uncompressed filesize
   * mtime       Timestamp of last modification
   * typeflag    Empty for files, set for folders
   * link        For the links, did you guess it ?
   * uname       Owner name
   * gname       Group name
   */
  function ListContents()
  {
    if(!$this->_nomf) return -3;
    if(!$this->_OpenRead()) return -4;

    $result = Array();

    while ($dat = $this->_read(512))
    {
      $dat = $this->_readHeader($dat);
      if(!is_array($dat)) continue;

      $this->_seek(ceil($dat['size']/512)*512,1);
      $result[] = $dat;
    }

    return  $result;
  }

  /**
   * Convert a status code into a human readable message
   *
   * Some MaxgTar functions like Create(), Add() ... return numerical
   * status code.  You can pass them to this function to grab their english
   * equivalent.
   */
  function TarErrorStr($i)
  {
    $ecodes = Array(
         1 => true,
         0 => "Undocumented error",
        -1 => "Can't use COMPRESS_GZIP compression : ZLIB extensions are not loaded !",
        -2 => "Can't use COMPRESS_BZIP compression : BZ2 extensions are not loaded !",
        -3 => "You must set a archive file to read the contents !",
        -4 => "Can't open the archive file for read !",
        -5 => "Invalide file list !",
        -6 => "Can't open the archive in write mode !",
        -7 => "There is no ARCHIVE_DYNAMIC to write !",
        -8 => "Can't create the directory to extract files !",
        -9 => "Please pass a archive name to send if you made created an ARCHIVE_DYNAMIC !",
       -10 => "You didn't pass an archive filename and there is no stored ARCHIVE_DYNAMIC !",
       -11 => "Given archive doesn't exist !"
    );

    return isset($ecodes[$i]) ? $ecodes[$i] : $ecodes[0];
  }

  /**
   * Display informations about the MaxgTar Class.
   *
   * This function will display vaious informations about the server
   * MaxgTar is running on.
   *
   * The optional parameter DispHeaders is used to generate a full page with
   * HTML headers (TRUE by default) or just the table with the informations
   * (FALSE).  Note that the HTML page generated is verified compatible XHTML
   * 1.0, but not HTML 4.0 compatible.
   */
  function TarInfo($headers = true)
  {
    if($headers)
    {
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
  <title>MaxgComp TAR</title>
  <style type="text/css">
   body{margin: 20px;}
   body,td{font-size:10pt;font-family: arial;}
  </style>
  <meta name="Author" content="The Maxg Network, http://maxg.info" />
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body bgcolor="#EFEFEF">
<?php
    }
?>
<table border="0" align="center" width="500" cellspacing="4" cellpadding="5" style="border:1px dotted black;">
<tr>
  <td align="center" bgcolor="#DFDFEF" colspan="3" style="font-size:15pt;font-color:#330000;border:1px solid black;">MaxgComp TAR</td>
</tr>
<tr>
  <td colspan="2" bgcolor="#EFEFFE" style="border:1px solid black;">This software was created by the Maxg Network, <a href="http://maxg.info" target="_blank" style="text-decoration:none;color:#333366;">http://maxg.info</a>
   <br />It is distributed under the GNU <a href="http://www.gnu.org/copyleft/lesser.html" target="_blank" style="text-decoration:none;color:#333366;">Lesser General Public License</a>
   <br />You can find the documentation of this class <a href="http://docs.maxg.info" target="_blank" style="text-decoration:none;color:#333366;">here</a></td>
   <td width="60" bgcolor="#EFEFFE" style="border:1px solid black;" align="center"><img src="http://img.maxg.info/menu/tar.gif" border="0" alt="MaxgComp TAR" /></td>
</tr>
<tr>
  <td width="50%" align="center" style="border:1px solid black;" bgcolor="#DFDFEF">MaxgComp TAR version</td>
  <td colspan="2" align="center" bgcolor="#EFEFFE" style="border:1px solid black;"><?=TARLIB_VERSION?></td>
</tr>
<tr>
  <td width="50%" align="center" style="border:1px solid black;" bgcolor="#DFDFEF">ZLIB extensions</td>
  <td colspan="2" align="center" bgcolor="#EFEFFE" style="border:1px solid black;"><?=(extension_loaded('zlib') ? '<b>Yes</b>' : '<i>No</i>')?></td>
</tr>
<tr>
  <td width="50%" align="center" style="border:1px solid black;" bgcolor="#DFDFEF">BZ2 extensions</td>
  <td colspan="2" align="center" bgcolor="#EFEFFE" style="border:1px solid black;"><?=(extension_loaded('bz2') ? '<b>Yes</b>' : '<i>No</i>')?></td>
</tr>
<tr>
  <td width="50%" align="center" style="border:1px solid black;" bgcolor="#DFDFEF">Allow URL fopen</td>
  <td colspan="2" align="center" bgcolor="#EFEFFE" style="border:1px solid black;"><?=(ini_get('allow_url_fopen') ? '<b>Yes</b>' : '<i>No</i>')?></td>
</tr>
<tr>
  <td width="50%" align="center" style="border:1px solid black;" bgcolor="#DFDFEF">Time limit</td>
  <td colspan="2" align="center" bgcolor="#EFEFFE" style="border:1px solid black;"><?=ini_get('max_execution_time')?></td>
</tr>
<tr>
  <td width="50%" align="center" style="border:1px solid black;" bgcolor="#DFDFEF">PHP Version</td>
  <td colspan="2" align="center" bgcolor="#EFEFFE" style="border:1px solid black;"><?=phpversion()?></td>
</tr>
<tr>
  <td colspan="3" align="center" bgcolor="#EFEFFE" style="border:1px solid black;">
    <i>Special thanks to &laquo; Vincent Blavet &raquo; for his PEAR::Archive_Tar class</i>
  </td>
</tr>
</table>
<?php
    if($headers) echo '</body></html>';
  }

  function _seek($p_flen, $tell=0)
  {
    if($this->_nomf === ARCHIVE_DYNAMIC)
      $this->_memdat=substr($this->_memdat,0,($tell ? strlen($this->_memdat) : 0) + $p_flen);
    elseif($this->_comptype == COMPRESS_GZIP)
      @gzseek($this->_fp, ($tell ? @gztell($this->_fp) : 0)+$p_flen);
    elseif($this->_comptype == COMPRESS_BZIP)
      @fseek($this->_fp, ($tell ? @ftell($this->_fp) : 0)+$p_flen);
    else
      @fseek($this->_fp, ($tell ? @ftell($this->_fp) : 0)+$p_flen);
  }

  function _OpenRead()
  {
    if($this->_comptype == COMPRESS_GZIP)
      $this->_fp = @gzopen($this->_nomf, 'rb');
    elseif($this->_comptype == COMPRESS_BZIP)
      $this->_fp = @bzopen($this->_nomf, 'rb');
    else
      $this->_fp = @fopen($this->_nomf, 'rb');

    return ($this->_fp ? true : false);
  }

  function _OpenWrite($add = 'w')
  {
    if($this->_nomf === ARCHIVE_DYNAMIC) return true;

    if($this->_comptype == COMPRESS_GZIP)
      $this->_fp = @gzopen($this->_nomf, $add.'b'.$this->_compzlevel);
    elseif($this->_comptype == COMPRESS_BZIP)
      $this->_fp = @bzopen($this->_nomf, $add.'b');
    else
      $this->_fp = @fopen($this->_nomf, $add.'b');

    return ($this->_fp ? true : false);
  }

  function _CompTar()
  {
    if($this->_nomf === ARCHIVE_DYNAMIC || !$this->_fp) return;

    if($this->_comptype == COMPRESS_GZIP) @gzclose($this->_fp);
    elseif($this->_comptype == COMPRESS_BZIP) @bzclose($this->_fp);
    else @fclose($this->_fp);
  }

  function _read($p_len)
  {
    if($this->_comptype == COMPRESS_GZIP)
      return @gzread($this->_fp,$p_len);
    elseif($this->_comptype == COMPRESS_BZIP)
      return @bzread($this->_fp,$p_len);
    else
      return @fread($this->_fp,$p_len);
  }

  function _write($p_data)
  {
    if($this->_nomf === ARCHIVE_DYNAMIC) $this->_memdat .= $p_data;
    elseif($this->_comptype == COMPRESS_GZIP)
      return @gzwrite($this->_fp,$p_data);

    elseif($this->_comptype == COMPRESS_BZIP)
      return @bzwrite($this->_fp,$p_data);

    else
      return @fwrite($this->_fp,$p_data);
  }

  function _encode($p_dat)
  {
    if($this->_comptype == COMPRESS_GZIP)
      return gzencode($p_dat, $this->_compzlevel);
    elseif($this->_comptype == COMPRESS_BZIP)
      return bzcompress($p_dat, $this->_compzlevel);
    else return $p_dat;
  }

  function _readHeader($p_dat)
  {
    if (!$p_dat || strlen($p_dat) != 512) return false;

    for ($i=0, $chks=0; $i<148; $i++)
      $chks += ord($p_dat[$i]);

    for ($i=156,$chks+=256; $i<512; $i++)
      $chks += ord($p_dat[$i]);

    $headers = @unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", $p_dat);
    if(!$headers) return false;

    $return['checksum'] = OctDec(trim($headers['checksum']));
    if ($return['checksum'] != $chks) return false;

    $return['filename'] = trim($headers['filename']);
    $return['mode'] = OctDec(trim($headers['mode']));
    $return['uid'] = OctDec(trim($headers['uid']));
    $return['gid'] = OctDec(trim($headers['gid']));
    $return['size'] = OctDec(trim($headers['size']));
    $return['mtime'] = OctDec(trim($headers['mtime']));
    $return['typeflag'] = $headers['typeflag'];
    $return['link'] = trim($headers['link']);
    $return['uname'] = trim($headers['uname']);
    $return['gname'] = trim($headers['gname']);

    return $return;
  }

  function _fetchFilelist($p_filelist)
  {
    if(!$p_filelist || (is_array($p_filelist) && !@count($p_filelist))) return false;

    if(is_string($p_filelist))
    {
        $p_filelist = explode('|',$p_filelist);
        if(!is_array($p_filelist)) $p_filelist = Array($p_filelist);
    }

    return $p_filelist;
  }

  function _addFileList($p_fl, $p_addir, $p_remdir)
  {
    foreach($p_fl as $file)
    {
      if(($file == $this->_nomf && $this->_nomf != ARCHIVE_DYNAMIC) || !$file || (!file_exists($file) && !is_array($file)))
        continue;

      if (!$this->_addFile($file, $p_addir, $p_remdir))
        continue;

      if (@is_dir($file))
      {
        $d = @opendir($file);

        if(!$d) continue;
        readdir($d); readdir($d);

        while($f = readdir($d))
        {
          if($file != ".") $tmplist[0] = "$file/$f";
          else $tmplist[0] = $d;

          $this->_addFileList($tmplist, $p_addir, $p_remdir);
        }

        closedir($d); unset($tmplist,$f);
      }
    }
    return true;
  }

  function _addFile($p_fn, $p_addir = '', $p_remdir = '')
  {
    if(is_array($p_fn)) list($p_fn, $data) = $p_fn;
    $sname = $p_fn;

    if($p_remdir)
    {
        if(substr($p_remdir,-1) != '/') $p_remdir .= "/";

        if(substr($sname, 0, strlen($p_remdir)) == $p_remdir)
          $sname = substr($sname, strlen($p_remdir));
    }

    if($p_addir) $sname = $p_addir.(substr($p_addir,-1) == '/' ? '' : "/").$sname;

    if(strlen($sname) > 99) return;

    if(@is_dir($p_fn))
    {
      if(!$this->_writeFileHeader($p_fn, $sname)) return false;
    }
    else
    {
     if(!$data)
     {
      $fp = fopen($p_fn, 'rb');
      if(!$fp) return false;
     }

     if(!$this->_writeFileHeader($p_fn, $sname, ($data ? strlen($data) : false))) return false;

     if(!$data)
     {
      while(!feof($fp))
      {
        $packed = pack("a512", fread($fp,512));
        $this->_write($packed);
      }
      fclose($fp);
     }
     else
     {
      for($s = 0; $s < strlen($data); $s += 512)
        $this->_write(pack("a512",substr($data,$s,512)));
     }
    }

    return true;
  }

  function _writeFileHeader($p_file, $p_sname, $p_data=false)
  {
   if(!$p_data)
   {
    if (!$p_sname) $p_sname = $p_file;
    $p_sname = $this->_pathTrans($p_sname);

    $h_info = stat($p_file);
    $h[0] = sprintf("%6s ", DecOct($h_info[4]));
    $h[] = sprintf("%6s ", DecOct($h_info[5]));
    $h[] = sprintf("%6s ", DecOct(fileperms($p_file)));
    clearstatcache();
    $h[] = sprintf("%11s ", DecOct(filesize($p_file)));
    $h[] = sprintf("%11s", DecOct(filemtime($p_file)));

    $dir = @is_dir($p_file) ? '5' : '';
   }
   else
   {
    $dir = '';
    $p_data = sprintf("%11s ", DecOct($p_data));
    $time = sprintf("%11s ", DecOct(time()));
    $h = Array("     0 ","     0 "," 40777 ",$p_data,$time);
   }

    $data_first = pack("a100a8a8a8a12A12", $p_sname, $h[2], $h[0], $h[1], $h[3], $h[4]);
    $data_last = pack("a1a100a6a2a32a32a8a8a155a12", $dir, '', '', '', '', '', '', '', '', "");

     for ($i=0,$chks=0; $i<148; $i++)
       $chks += ord($data_first[$i]);

     for ($i=156, $chks+=256, $j=0; $i<512; $i++, $j++)
       $chks += ord($data_last[$j]);

     $this->_write($data_first);

     $chks = pack("a8",sprintf("%6s ", DecOct($chks)));
     $this->_write($chks.$data_last);

     return true;
  }

  function _append($p_filelist, $p_addir="", $p_remdir="")
  {
    if(!$this->_fp) if(!$this->_OpenWrite('a')) return -6;

    if($this->_nomf == ARCHIVE_DYNAMIC)
    {
      $s = strlen($this->_memdat);
      $this->_memdat = substr($this->_memdat,0,-512);
    }
    else
    {
      $s = filesize($this->_nomf);
      $this->_seek($s-512);
    }

    $ok = $this->_addFileList($p_filelist, $p_addir, $p_remdir);
    $this->_writeFooter();

    return $ok;
  }

  function _pathTrans($p_dir)
  {
    if ($p_dir)
    {
      $subf = explode("/", $p_dir); $r='';

      for ($i=count($subf)-1; $i>=0; $i--)
      {
        if ($subf[$i] == ".") {}
        else if ($subf[$i] == "..") $i--;
        else if (!$subf[$i] && $i!=count($subf)-1 && $i) {}
        else $r = $subf[$i].($i!=(count($subf)-1) ? "/".$r : "");
      }
    }
    return $r;
  }

  function _writeFooter()
  {
    $this->_write(pack("a512", ""));
  }

  function _extractList($p_to, $p_files, $p_remdir, $p_mode = 0755)
  {
    if (!$p_to || ($p_to[0]!="/"&&substr($p_to,0,3)!="../"&&substr($p_to,1,2)!=":\\")) /*" // <- PHP Coder bug */
      $p_to = "./$p_to";

    if ($p_remdir && substr($p_remdir,-1)!='/') $p_remdir .= '/';
    $p_remdirs = strlen($p_remdir);
    while($dat = $this->_read(512))
    {
      $headers = $this->_readHeader($dat);
      if(!$headers['filename']) continue;

      if($p_files == -1 || $p_files[0] == -1) $extract = true;
      else
      {
        $extract = false;

        foreach($p_files as $f)
        {
          if(substr($f,-1) == "/") {
            if((strlen($headers['filename']) > strlen($f)) && (substr($headers['filename'],0,strlen($f))==$f)) {
              $extract = true; break;
            }
          }
          elseif($f == $headers['filename']) {
            $extract = true; break;
          }
        }
      }

      if ($extract)
      {
        $det[] = $headers;
        if ($p_remdir && substr($headers['filename'],0,$p_remdirs)==$p_remdir)
          $headers['filename'] = substr($headers['filename'],$p_remdirs);

        if($headers['filename'].'/' == $p_remdir && $headers['typeflag']=='5') continue;

        if ($p_to != "./" && $p_to != "/")
        {
          while($p_to{-1}=="/") $p_to = substr($p_to,0,-1);

          if($headers['filename']{0} == "/")
            $headers['filename'] = $p_to.$headers['filename'];
          else
            $headers['filename'] = $p_to."/".$headers['filename'];
        }

        $ok = $this->_dirApp($headers['typeflag']=="5" ? $headers['filename'] : dirname($headers['filename']));
        if($ok < 0) return $ok;

        if (!$headers['typeflag'])
        {
          if (!$fp = @fopen($headers['filename'], "wb")) return -6;
          $n = floor($headers['size']/512);

          for ($i=0; $i<$n; $i++) fwrite($fp, $this->_read(512),512);
          if (($headers['size'] % 512) != 0) fwrite($fp, $this->_read(512), $headers['size'] % 512);

          fclose($fp);
          touch($headers['filename'], $headers['mtime']);
          chmod($headers['filename'], $p_mode);
        }
       else
       {
         $this->_seek(ceil($headers['size']/512)*512,1);
       }
      }else $this->_seek(ceil($headers['size']/512)*512,1);
    }
    return $det;
  }

  function _dirApp($target) {
    global $DPERM;
    if (@is_dir($target)||empty($target)) return 1;
    if ($this->_dirApp(substr($target,0,strrpos($target,'/')))){
        $ret = @mkdir($target,0777);
        if($ret){
            chmod($target, $DPERM);
            say(preg_replace('!^.*?(//)!','',$target));
        }
        return $ret;
    }
    return 0;
  }
}

define('HTTP_NL',"\r\n");


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
 */
class HTTPClient {
    //set these if you like
    var $agent;         // User agent
    var $http;          // HTTP version defaults to 1.0
    var $timeout;       // read timeout (seconds)
    var $cookies;
    var $referer;
    var $max_redirect;
    var $max_bodysize;
    var $max_bodysize_abort = true;  // if set, abort if the response body is bigger than max_bodysize
    var $header_regexp; // if set this RE must match against the headers, else abort
    var $headers;
    var $debug;
    var $start = 0; // for timings

    // don't set these, read on error
    var $error;
    var $redirect_count;
    var $read_bytes = 0;

    // read these after a successful request
    var $resp_status;
    var $resp_body;
    var $resp_headers;

    // set these to do basic authentication
    var $user;
    var $pass;

    // set these if you need to use a proxy
    var $proxy_host;
    var $proxy_port;
    var $proxy_user;
    var $proxy_pass;
    var $proxy_ssl; //boolean set to true if your proxy needs SSL

    /**
     * Constructor.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function HTTPClient(){
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
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function get($url,$sloppy304=false){
        if(!$this->sendRequest($url)) return false;
        if($this->status == 304 && $sloppy304) return $this->resp_body;
        if($this->status < 200 || $this->status > 206) return false;
        return $this->resp_body;
    }

    /**
     * Simple function to do a POST request
     *
     * Returns the resulting page or false on an error;
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function post($url,$data){
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
     * @author Andreas Goetz <cpuidle@gmx.de>
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function sendRequest($url,$data='',$method='GET'){
        $this->start  = $this->_time();
        $this->error  = '';
        $this->status = 0;

        // parse URL into bits
        $uri = parse_url($url);
        $server = $uri['host'];
        $path   = $uri['path'];
        if(empty($path)) $path = '/';
        if(!empty($uri['query'])) $path .= '?'.$uri['query'];
        $port = $uri['port'];
        if($uri['user']) $this->user = $uri['user'];
        if($uri['pass']) $this->pass = $uri['pass'];

        // proxy setup
        if($this->proxy_host){
            $request_url = $url;
            $server      = $this->proxy_host;
            $port        = $this->proxy_port;
            if (empty($port)) $port = 8080;
        }else{
            $request_url = $path;
            $server      = $server;
            if (empty($port)) $port = ($uri['scheme'] == 'https') ? 443 : 80;
        }

        // add SSL stream prefix if needed - needs SSL support in PHP
        if($port == 443 || $this->proxy_ssl) $server = 'ssl://'.$server;

        // prepare headers
        $headers               = $this->headers;
        $headers['Host']       = $uri['host'];
        $headers['User-Agent'] = $this->agent;
        $headers['Referer']    = $this->referer;
        $headers['Connection'] = 'Close';
        if($method == 'POST'){
            if(is_array($data)){
                $headers['Content-Type']   = 'application/x-www-form-urlencoded';
                $data = $this->_postEncode($data);
            }
            $headers['Content-Length'] = strlen($data);
            $rmethod = 'POST';
        }elseif($method == 'GET'){
            $data = ''; //no data allowed on GET requests
        }
        if($this->user) {
            $headers['Authorization'] = 'Basic '.base64_encode($this->user.':'.$this->pass);
        }
        if($this->proxy_user) {
            $headers['Proxy-Authorization'] = 'Basic '.base64_encode($this->proxy_user.':'.$this->proxy_pass);
        }

        // stop time
        $start = time();

        // open socket
        $socket = @fsockopen($server,$port,$errno, $errstr, $this->timeout);
        if (!$socket){
            $resp->status = '-100';
            $this->error = "Could not connect to $server:$port\n$errstr ($errno)";
            return false;
        }
        //set non blocking
        stream_set_blocking($socket,0);

        // build request
        $request  = "$method $request_url HTTP/".$this->http.HTTP_NL;
        $request .= $this->_buildHeaders($headers);
        $request .= $this->_getCookies();
        $request .= HTTP_NL;
        $request .= $data;

        $this->_debug('request',$request);

        // send request
        $towrite = strlen($request);
        $written = 0;
        while($written < $towrite){
            $ret = fwrite($socket, substr($request,$written));
            if($ret === false){
                $this->status = -100;
                $this->error = 'Failed writing to socket';
                return false;
            }
            $written += $ret;
        }


        // read headers from socket
        $r_headers = '';
        do{
            if(time()-$start > $this->timeout){
                $this->status = -100;
                $this->error = sprintf('Timeout while reading headers (%.3fs)',$this->_time() - $this->start);
                return false;
            }
            if(feof($socket)){
                $this->error = 'Premature End of File (socket)';
                return false;
            }
            $r_headers .= fgets($socket,1024);
        }while(!preg_match('/\r?\n\r?\n$/',$r_headers));

        $this->_debug('response headers',$r_headers);

        // check if expected body size exceeds allowance
        if($this->max_bodysize && preg_match('/\r?\nContent-Length:\s*(\d+)\r?\n/i',$r_headers,$match)){
            if($match[1] > $this->max_bodysize){
                $this->error = 'Reported content length exceeds allowed response size';
                if ($this->max_bodysize_abort)
                    return false;
            }
        }

        // get Status
        if (!preg_match('/^HTTP\/(\d\.\d)\s*(\d+).*?\n/', $r_headers, $m)) {
            $this->error = 'Server returned bad answer';
            return false;
        }
        $this->status = $m[2];

        // handle headers and cookies
        $this->resp_headers = $this->_parseHeaders($r_headers);
        if(isset($this->resp_headers['set-cookie'])){
            foreach ((array) $this->resp_headers['set-cookie'] as $c){
                list($key, $value, $foo) = split('=', $cookie);
                $this->cookies[$key] = $value;
            }
        }

        $this->_debug('Object headers',$this->resp_headers);

        // check server status code to follow redirect
        if($this->status == 301 || $this->status == 302 ){
            if (empty($this->resp_headers['location'])){
                $this->error = 'Redirect but no Location Header found';
                return false;
            }elseif($this->redirect_count == $this->max_redirect){
                $this->error = 'Maximum number of redirects exceeded';
                return false;
            }else{
                $this->redirect_count++;
                $this->referer = $url;
                if (!preg_match('/^http/i', $this->resp_headers['location'])){
                    $this->resp_headers['location'] = $uri['scheme'].'://'.$uri['host'].
                                                      $this->resp_headers['location'];
                }
                // perform redirected request, always via GET (required by RFC)
                return $this->sendRequest($this->resp_headers['location'],array(),'GET');
            }
        }

        // check if headers are as expected
        if($this->header_regexp && !preg_match($this->header_regexp,$r_headers)){
            $this->error = 'The received headers did not match the given regexp';
            return false;
        }

        $lastsay = 0;

        //read body (with chunked encoding if needed)
        $r_body    = '';
        if(preg_match('/transfer\-(en)?coding:\s*chunked\r\n/i',$r_header)){
            do {
                unset($chunk_size);
                do {
                    if(feof($socket)){
                        $this->error = 'Premature End of File (socket)';
                        return false;
                    }
                    if(time()-$start > $this->timeout){
                        $this->status = -100;
                        $this->error = sprintf('Timeout while reading chunk (%.3fs)',$this->_time() - $this->start);
                        return false;
                    }
                    $byte = fread($socket,1);
                    $chunk_size .= $byte;
                } while (preg_match('/[a-zA-Z0-9]/',$byte)); // read chunksize including \r

                $byte = fread($socket,1);     // readtrailing \n
                $chunk_size = hexdec($chunk_size);
                $this_chunk = fread($socket,$chunk_size);
                $r_body    .= $this_chunk;
                if ($chunk_size) $byte = fread($socket,2); // read trailing \r\n

                if($this->max_bodysize && strlen($r_body) > $this->max_bodysize){
                    $this->error = 'Allowed response size exceeded';
                    if ($this->max_bodysize_abort)
                        return false;
                    else
                        break;
                }

                $this->_status(strlen($r_body));
            } while ($chunk_size);
        }else{
            // read entire socket
            while (!feof($socket)) {
                if(time()-$start > $this->timeout){
                    $this->status = -100;
                    $this->error = sprintf('Timeout while reading response (%.3fs)',$this->_time() - $this->start);
                    return false;
                }
                $r_body .= fread($socket,4096);
                $r_size = strlen($r_body);
                if($this->max_bodysize && $r_size > $this->max_bodysize){
                    $this->error = 'Allowed response size exceeded';
                    if ($this->max_bodysize_abort)
                        return false;
                    else
                        break;
                }
                if($this->resp_headers['content-length'] && !$this->resp_headers['transfer-encoding'] &&
                   $this->resp_headers['content-length'] == $r_size){
                    // we read the content-length, finish here
                    break;
                }
                $this->_status(strlen($r_body));
            }
        }

        // close socket
        $status = socket_get_status($socket);
        fclose($socket);

        say(strlen($r_body).' bytes read');

        // decode gzip if needed
        if($this->resp_headers['content-encoding'] == 'gzip'){
            $this->resp_body = gzinflate(substr($r_body, 10));
        }else{
            $this->resp_body = $r_body;
        }

        $this->_debug('response body',$this->resp_body);
        $this->redirect_count = 0;
        return true;
    }

    function _status($bytes){
        if($this->read_bytes < ($bytes - 1024*250)){
            $this->read_bytes = $bytes;
            say($bytes.' bytes read');
        }
    }

    /**
     * print debug info
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function _debug($info,$var=null){
        if(!$this->debug) return;
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
     * Return current timestamp in microsecond resolution
     */
    function _time(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * convert given header string to Header array
     *
     * All Keys are lowercased.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function _parseHeaders($string){
        $headers = array();
        $lines = explode("\n",$string);
        foreach($lines as $line){
            list($key,$val) = explode(':',$line,2);
            $key = strtolower(trim($key));
            $val = trim($val);
            if(empty($val)) continue;
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
     */
    function _buildHeaders($headers){
        $string = '';
        foreach($headers as $key => $value){
            if(empty($value)) continue;
            $string .= $key.': '.$value.HTTP_NL;
        }
        return $string;
    }

    /**
     * get cookies as http header string
     *
     * @author Andreas Goetz <cpuidle@gmx.de>
     */
    function _getCookies(){
        foreach ($this->cookies as $key => $val){
            if ($headers) $headers .= '; ';
            $headers .= $key.'='.$val;
        }

        if ($headers) $headers = "Cookie: $headers".HTTP_NL;
        return $headers;
    }

    /**
     * Encode data for posting
     *
     * @todo handle mixed encoding for file upoads
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    function _postEncode($data){
        foreach($data as $key => $val){
            if($url) $url .= '&';
            $url .= urlencode($key).'='.urlencode($val);
        }
        return $url;
    }
}

// --- FUNCTIONS-------------------------------------------------------------
// Some smaller functions for this installer
// --------------------------------------------------------------------------

function html_header(){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>DokuWiki Downloader</title>
    <style type="text/css">
        div#main {
            background-color:#FFFFFF;
            color:#000000;
            font-family:"Lucida Grande",Verdana,Lucida,Helvetica,Arial,sans-serif;
            font-size:80%;
            margin: 2em auto;
            width: 55em;
        }
    </style>
</head>
<body>
<div id="main">
<h1>DokuWiki Downloader</h1>
<img src="http://www.dokuwiki.org/_media/wiki:dokuwiki-128.png" width="128" height="128" align="right" />
<?php
}

function html_footer(){ ?>
</div>
</body>
</html>
<?php
exit();
}

function say($msg){
    echo $msg.'<br />';
    flush();
    ob_flush();
}

function step1(){
    global $INSTALL_DIR;
    global $VERSION;

    if(!isset($_GET['go'])){
        say("<p>This script will download <strong>DokuWiki $VERSION</strong> to your webserver
             and install it in <strong>$INSTALL_DIR</strong>. If this is not the directory
             you want to install to, move this script to the target directory</p>
             <form method=\"get\" action=\"\">
                 <input type=\"hidden\" name=\"go\" value=\"1\" />
                 <input type=\"submit\" value=\"Okay, proceed to download\" />
             </form>
            ");
        html_footer();
    }

    if(!is_writable($INSTALL_DIR)){
        say("<strong>$INSTALL_DIR</strong> is not writable. You need to make it writable via
             FTP or your Webhoster's admin panel.");
        html_footer();
    }
}

function step2(){
    global $DOWNLOAD;
    global $INSTALL_DIR;
    global $TGZ;

    say('Downloading <a href="'.$DOWNLOAD.'">DokuWiki archive</a>...');
    @set_time_limit(120);
    @ignore_user_abort();

    $http = new HTTPClient();
    $http->timeout = 120;
    $data = $http->get($DOWNLOAD);
    if(!$data){
        say($http->error);
        say("Download failed. Try to upload the .tgz yourself.");
        html_footer();
    }


    $fp = @fopen($TGZ,"w");
    if(!$fp){
        say("Failed to save $TGZ. Try to upload it yourself.");
        html_footer();
    }
    fwrite($fp,$data);
    fclose($fp);
    unset($data);

    say("<p>Download completed successfully. Please continue to next step</p>
         <form method=\"get\" action=\"\">
             <input type=\"hidden\" name=\"go\" value=\"3\" />
             <input type=\"submit\" value=\"Okay, proceed to file extraction\" />
         </form>");
    html_footer();
}

function step3(){
    global $INSTALL_DIR;
    global $TGZ;
    global $VERSION;
    global $FPERM;

    say('Extracting the archive...');
    @set_time_limit(120);
    @ignore_user_abort();

    $tar = new tarlib($TGZ);
    if($tar->_initerror < 0){
        say($tar->TarErrorStr($ok));
        say('Extraction failed');
        html_footer();
    }
    $ok = $tar->Extract(FULL_ARCHIVE,"$INSTALL_DIR/",'dokuwiki-'.$VERSION.'/',$FPERM);
    if($ok < 1){
        say($tar->TarErrorStr($ok));
        say('Extraction failed');
        html_footer();
    }

    say("<p>Extraction completed successfully. Please continue to next step</p>
         <form method=\"get\" action=\"\">
             <input type=\"hidden\" name=\"go\" value=\"4\" />
             <input type=\"submit\" value=\"Okay, proceed to last step\" />
         </form>");
    html_footer();
}

function step4(){
    global $TGZ;
    if(@unlink($TGZ)){
        say("DokuWiki archive file deleted.");
    }else{
        say("The DokuWiki .tgz archive could not be deleted automatically,
             you should remove it yourself.");
    }
    if(@unlink(__FILE__)){
        say("DokuWiki Download Script deleted.");
    }else{
        say("This download script could not be deleted automatically,
             you should remove it yourself.");
    }

    say("<p>You're done. DokuWiki was successfully downloaded and extracted on your
         server. The next step is to run the default installer to do some initial
         setup.</p>
        <form method=\"get\" action=\"install.php\">
             <input type=\"submit\" value=\"Okay, take me to the installer.\" />
         </form>
        ");
    html_footer();
}


// --- MAIN -----------------------------------------------------------------
// Here the work is done ;-)
// --------------------------------------------------------------------------

// initialize
$DOWNLOAD    = 'http://www.splitbrain.org/_media/projects/dokuwiki/dokuwiki-'.$VERSION.'.tgz';
$INSTALL_DIR = dirname(__FILE__);
$TGZ         = "$INSTALL_DIR/dokuwiki-$VERSION.tgz";

if(defined('E_DEPRECATED')){ // since php 5.3
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}else{
    error_reporting(E_ALL ^ E_NOTICE);
}


header('Content-type: text/html; charset=utf-8');
html_header();

step1();
if(!file_exists($TGZ)) step2();
if(!file_exists($INSTALL_DIR.'/doku.php')) step3();
step4();


//Setup VIM: ex: et ts=4 enc=utf-8 :
