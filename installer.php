<?php
/**
 * PHP-MSF快速安装程序
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

/**
 * 输出到控制台
 *
 * @param string $messages 输出的到控制台数据
 */
function writeln($messages)
{
    $msgStr = (string)$messages;
    echo sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $msgStr);
}

/**
 * 在同一行输出到控制台
 *
 * @param string $messages 输出的到控制台数据
 */
function writeLine($messages)
{
    $msgStr = (string)$messages;
    echo sprintf("[%s] %s", date('Y-m-d H:i:s'), $msgStr);
}

// 是否安装swoole
if (!defined('SWOOLE_VERSION')) {
    writeln("Swoole Extension Not Found");
    exit(255);
}

// 是否安装redis
if (!class_exists(\Redis::class)) {
    writeln("phpredis Extension Not Found");
    exit(255);
}

// 是否安装yac
if (!defined('YAC_VERSION')) {
    writeln("Yac Extension Not Found");
    exit(255);
}

// 检查PHP
if (version_compare(PHP_VERSION, '7.0.0') < 0) {
    writeln("PHP Version Is Too Lower, Please Install >= 7.0.0");
    exit(255);
}

// 检查Swoole
if (version_compare(SWOOLE_VERSION, '1.9.15') < 0) {
    writeln("Swoole Version Is Too Lower, Please Install >= 1.9.15");
    exit(255);
}

// 配置应用
$tmp                   = '/tmp/';
$gitBinary             = trim(shell_exec('which git'));
$phpBinPath            = trim(shell_exec('which php'));
$composerBin           = trim(shell_exec('which composer'));
$defaultSystemName     = 'demo';
$defaultApplicationDir = '/home/worker/data/www/';
$defaultPort           = 80;
// 检查php
if (empty($phpBinPath)) {
    writeln("no php command");
    exit(255);
}

// 检查composer
if (empty($composerBin)) {
    writeln("no php composer command");
    exit(255);
}

// 检查Git
if (empty($gitBinary)) {
    writeln("no git command");
    exit(255);
}

// 克隆模板项目
$gitRepo   = 'https://github.com/pinguo/php-msf-demo';
writeln("git clone {$gitRepo}");
shell_exec("$gitBinary clone $gitRepo {$tmp}php-msf-demo");

// 读取用户输入配置
writeLine("Input application directory ($defaultApplicationDir): ");
$applicationDir        = trim(fgets(STDIN));
writeLine("Input application name ($defaultSystemName): ");
$systemName            = trim(fgets(STDIN));
writeLine("Input application env (will write to " . $_ENV['HOME'] . "/.bashrc): ");
$applicationEnv        = trim(fgets(STDIN));
writeLine("Input application http port ($defaultPort): ");
$port                  = trim(fgets(STDIN));

// 创建目录结构
if (empty($applicationDir)) {
    $applicationDir = $defaultApplicationDir;
}

if (empty($systemName)) {
    $systemName = $defaultSystemName;
}

if (empty($applicationEnv)) {
    $applicationEnv = 'docker';
}

if (empty($port)) {
    $port = $defaultPort;
}

!is_dir($applicationDir) && @mkdir($applicationDir, 0755, true);
!is_dir($applicationDir . '/app/Console') && @mkdir($applicationDir . '/app/Console', 0755, true);
!is_dir($applicationDir . '/app/Controllers') && @mkdir($applicationDir . '/app/Controllers', 0755, true);
!is_dir($applicationDir . '/app/Lib') && @mkdir($applicationDir . '/app/Lib', 0755, true);
!is_dir($applicationDir . '/app/Models') && @mkdir($applicationDir . '/app/Models', 0755, true);
!is_dir($applicationDir . '/app/Route') && @mkdir($applicationDir . '/app/Route', 0755, true);
!is_dir($applicationDir . '/app/Tasks') && @mkdir($applicationDir . '/app/Tasks', 0755, true);
!is_dir($applicationDir . '/app/Views') && @mkdir($applicationDir . '/app/Views', 0755, true);
!is_dir($applicationDir . '/config/' . $applicationEnv) && @mkdir($applicationDir . '/config/' . $applicationEnv, 0755, true);
!is_dir($applicationDir . '/test/') && @mkdir($applicationDir . '/test/', 0755, true);
!is_dir($applicationDir . '/www/') && @mkdir($applicationDir . '/www/', 0755, true);
writeln('Create application directory structure success');

// 复制模板文件
copy("{$tmp}php-msf-demo/server.php", $applicationDir . '/server.php');
copy("{$tmp}php-msf-demo/console.php", $applicationDir . '/console.php');
copy("{$tmp}php-msf-demo/composer.json", $applicationDir . '/composer.json');
copy("{$tmp}php-msf-demo/checkstyle.sh", $applicationDir . '/checkstyle.sh');
copy("{$tmp}php-msf-demo/build.sh", $applicationDir . '/build.sh');
copy("{$tmp}php-msf-demo/www/index.html", $applicationDir . '/www/index.html');
copy("{$tmp}php-msf-demo/test/README.md", $applicationDir . '/test/README.md');
copy("{$tmp}php-msf-demo/config/server.php", $applicationDir . '/config/server.php');
copy("{$tmp}php-msf-demo/config/log.php", $applicationDir . '/config/log.php');
copy("{$tmp}php-msf-demo/config/http.php", $applicationDir . '/config/http.php');
copy("{$tmp}php-msf-demo/config/fileHeader.php", $applicationDir . '/config/fileHeader.php');
copy("{$tmp}php-msf-demo/config/check.php", $applicationDir . '/config/check.php');
copy("{$tmp}php-msf-demo/app/AppServer.php", $applicationDir . '/app/AppServer.php');
copy("{$tmp}php-msf-demo/app/Controllers/Welcome.php", $applicationDir . '/app/Controllers/Welcome.php');
$conf = <<<eot
<?php
\$config = [];
return \$config;

eot;
file_put_contents($applicationDir . '/config/' . $applicationEnv . '/params.php', $conf);
writeln('Copy application template file success');

// 替换配置
chdir($applicationDir);
shell_exec("sed -i 's/demo/{$systemName}/g' server.php console.php");
shell_exec("sed -i 's#/home/worker/php/bin/php#$phpBinPath#g' server.php console.php");
shell_exec("sed -i 's/pinguo\/php-msf-demo/{$systemName}/g' composer.json");
shell_exec("sed -i 's/export\ MSF_ENV=\w\+//g' " . $_ENV['HOME'] . '/.bashrc');
shell_exec("echo export MSF_ENV=" . $applicationEnv . '>>' .  $_ENV['HOME'] . '/.bashrc');
shell_exec("sed -i 's/=\ 8000/=\ {$port}/g' ./config/http.php");
shell_exec("sed -i 's/localhost:8000/localhost:{$port}/g' ./config/http.php");
writeln('Replace application config success');

// 清理
shell_exec("rm -rf " . $tmp . "php-msf-demo");
writeln('Clear tmp data success');

// 添加权限
shell_exec("chmod +x server.php console.php");

// composer install
shell_exec("composer install -vvv");
writeln('Run composer install success');

// 启动服务
writeln("Congratulations, all are installed successfully!");
writeln("You can, visit http://127.0.0.1:" . $port . '/Welcome for test');

$ascii     = <<<eot
      _______                               ____
________  / /_  ____        ____ ___  _____/ __/
___/ __ \/ __ \/ __ \______/ __ `__ \/ ___/ /_
__/ /_/ / / / / /_/ /_____/ / / / / (__  ) __/
_/ .___/_/ /_/ .___/     /_/ /_/ /_/____/_/
/_/         /_/         Camera360 Open Source TM
eot;
echo $ascii, "\n";
writeln('Swoole  Version: ' . SWOOLE_VERSION);
writeln('PHP     Version: ' . PHP_VERSION);
writeln('Application ENV: ' . $applicationEnv);
writeln("Listen     Addr: " . '0.0.0.0');
writeln("Listen     Port: " . $port);
shell_exec("MSF_ENV=" . $applicationEnv . " ./server.php");