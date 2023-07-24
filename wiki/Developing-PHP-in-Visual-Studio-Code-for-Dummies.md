This article describes how to set up your tooling if you want a free and lightweight option for developing PHP projects on Windows (tested for PHP 8).

## Install PHP

1. Download PHP from http://windows.php.net/download/. I'm using the `VC18 x64 Non Thread Safe` version
1. Extract it to `c:\php`
1. Copy contents of `php.ini-production` to `php.ini`
1. Edit `php.ini` and uncomment `extension_dir = “ext”`
1. Uncomment some basic extensions

```plain
extension=bz2
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=exif      ; Must be after mbstring as it depends on it
extension=openssl
```

## Install XDebug

1. Go to `c:\php`
1. `SHIFT`+`Right click` in the folder -> `Open PowerShell window here`
1. Run `./php -i` (alternatively, in case you receive `'php' not recognized`, run `./php -r "phpinfo();"` and press Enter)
    * *In powershell you can use `.\php -i | clip.exe` to sent the output directly to clipboard.*
1. Copy the output
1. Paste it here https://xdebug.org/wizard.php and follow the instructions which basically say:
1. Click download
1. Extract it to `C:\php\ext`
1. Open `c:\php\php.ini` and append the XDebug section to the end of the file (if it's already there, adjust it accordingly):

   ```ini
   [XDebug] ; for XDebug v 2.x.x
   zend_extension = php_xdebug-3.1.3-8.0-vs16-nts-x86_64.dll ; Use the name of the DLL you copied to ext folder
   xdebug.default_enable = 1
   xdebug.scream = 1
   xdebug.coverage_enable = 1
   xdebug.profiler_enable = 0
   xdebug.profiler_enable_trigger = 1
   xdebug.profiler_output_dir = "C:\php\profiles"
   xdebug.remote_enable = 1
   xdebug.remote_autostart  = 1
   xdebug.remote_host=127.0.0.1
   xdebug.remote_port = 9003
   ```

   ```ini
   [XDebug]; for XDebug v3.x.x https://stackoverflow.com/questions/43783482/visual-studio-code-php-debugging-not-working/70351090#70351090
   xdebug.mode = coverage ; or debug
   xdebug.start_with_request = yes
   zend_extension = "C:\php\ext\php_xdebug-3.2.2-8.2-vs16-x86_64.dll" ; Use the name of the DLL you copied to ext folder
   xdebug.stopOnEntry = true
   xdebug.profiler_enable = off
   xdebug.profiler_enable_trigger = Off
   xdebug.profiler_output_name = cachegrind.out.%t.%p
   xdebug.output_dir ="c:\php\tmp"
   xdebug.show_local_vars=0
   xdebug.remote_handler = "dbgp"
   xdebug.client_host = "127.0.0.1"
   xdebug.log = "C:\php\tmp\xdebug.txt"
   xdebug.client_port = 9003
   xdebug.remote_cookie_expire_time = 36000
   ```

1. Add `c:\php` to your PATH environment variable
1. Add key: `XDEBUG_CONFIG` with value: `idekey=VSCODE` to your System Variables

> See the differences for multiple XDebug configurations https://stackoverflow.com/a/70351090/3529135.

## Install Visual Studio Code

1. Download and install VS Code https://code.visualstudio.com/
1. Learn `CTRL`+`SHIFT`+`P` to run any command (aka Command Palette)
1. Use the Command Palette shortcut and search for "Inst Ext"
1. Install Composer into your OS from [https://getcomposer.org/doc/00-intro.md#installation-windows](https://getcomposer.org/doc/00-intro.md#installation-windows)
   _ You might experience error during installation, validate the `php_xdebug-XYZ.dll` file name from error is the same as in the `C:\php\ext` dir. Change name or config appropriately.
   . Install the following extensions:
   _ [Composer](https://marketplace.visualstudio.com/items?itemName=ikappas.composer)
   _ You can also download it separately: https://getcomposer.org/download/
   _ You must set composer.executablePath user setting as:
   `"composer.executablePath": "C:\\ProgramData\\ComposerSetup\\bin\\composer.bat"`
   _ You will be unable to use this plugin unless you configure this setting before first use.
   _ Validate correct settings via `Composer: Validate`
   _ [PHPUnit](https://marketplace.visualstudio.com/items?itemName=emallin.phpunit)
   _ Can be installed also via Composer by running `composer global require phpunit/phpunit`
   _ [PHP IntelliSense](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-intellisense)
   _ [PHP Debug](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug) \* [XML Tools](https://marketplace.visualstudio.com/items?itemName=DotJoshJohnson.xml)
1. Use the Command Palette to "Open User Settings (JSON)"
1. Configure PHP-related settings

   ```json
   {
     "php.executablePath": "C:\\php\\php.exe",
     "php.validate.executablePath": "C:\\php\\php.exe",
     "php.validate.enable": true,
     "php.validate.run": "onType",
     "phpunit.execPath": "C:\\Users\\<yourusername>\\AppData\\Roaming\\Composer\\vendor\\bin\\phpunit.bat",
     "phpunit.args": []
   }
   ```

## Done

You're ready to start coding in PHP!

[**Part 2: Continue reading about debugging, unit testing, autoloading, bootstrapping...**](https://github.com/kontent-ai/delivery-sdk-php/blob/master/wiki/Doing-PHP-stuff-on-Windows-with-VS-Code.md)

## More resources

More detailed resources that helped me assemble this tutorial:

- [Woah! I switched to Windows and it’s awesome for PHP development](https://www.newmediacampaigns.com/blog/woah-i-switched-to-windows-and-its-awesome-for-php-development)
- [Configuring Visual Studio Code for PHP development](https://blogs.msdn.microsoft.com/nicktrog/2016/02/11/configuring-visual-studio-code-for-php-development/)
- [PHP Programming with VS Code](https://code.visualstudio.com/docs/languages/php)
- [VS Code PHP Unit Extension Documentation](https://github.com/elonmallin/vscode-phpunit)
- [VS Code PHP Debug Extension Documentation](https://github.com/felixfbecker/vscode-php-debug)
