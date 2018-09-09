# DokuWiki Downloader

This script will download and install the latest stable version of DokuWiki
into the same directory as this script.

This is the most simplest and fastest way to install DokuWiki on hosted web
space.

## Usage

A detailed description with screenshots (for an older version of this script) is available in a 
[blog post](http://www.splitbrain.org/blog/2008-12/24-setup_dokuwiki_on_free_hosting_in_less_than_15_minutes).

1. download the [dokuwiki-downloader.php](https://github.com/splitbrain/dokuwiki-downloader/raw/master/dokuwiki-downloader.php) script to your computer
2. upload the script to the directory on your server where you want to install DokuWiki using FTP, SCP or your hoster's webinterface
3. Visit `http://example.com/dokuwiki/dokuwiki-downloader.php` (Adjust the address to match your domain and where you placed the script)
4. follow the on-screen instructions

## Development

Development happens in the [devel](https://github.com/splitbrain/dokuwiki-downloader/tree/devel) branch. The `dokuwiki-downloader.php` is built from multiple source files with the `build.php` script.
