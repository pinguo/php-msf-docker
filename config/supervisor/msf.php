<?php
/**
 * 品果微服务框架服务启动脚本
 *
 * @author xudianyang<120343758@qq.com>
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */
// 服务部署路径
$wwwPath = '/home/worker/data/www/';

// 服务列表
$servers = [];
$msfServers = [];

// 遍历服务部署路径,检查已部署服务是否支持MSF框架
if ($dh = opendir($wwwPath)) {
    while (($file = readdir($dh)) !== false) {
        if (strpos($file, '_rollback') !== false) {
            continue;
        }
        if (!empty($servers) && !in_array($file, $servers)) {
            continue;
        }

        if (is_dir($wwwPath . $file) && file_exists($wwwPath . $file . '/server.php')){
            chmod($wwwPath . $file . '/server.php', 0755);
            $name = $file;
            if ($name != 'msf-' && strpos($name, 'msf-') === 0) {
                $name = substr($name, strlen('msf-'));
            }
            $msfServers[$name] = $wwwPath . $file . '/server.php';
        }
    }
    closedir($dh);
}

// supervisor配置模板
$conf = <<<eot
[program:msf-___NAME___]
command=___BIN___ 
process_name=msf-___NAME___
numprocs=1
directory=/home/worker/php/
umask=022
priority=999
autostart=true
autorestart=true
startsecs=10
startretries=2
exitcodes=0,2
stopsignal=TERM
stopwaitsecs=10
user=worker
redirect_stderr=true
stdout_logfile=/home/worker/data/msf-___NAME___/server.log
redirect_stdout=true
stderr_logfile=/home/worker/data/msf-___NAME___/error.log
eot;

// 重写配置
if (!empty($msfServers)) {
    $newConfContent = '';
    foreach ($msfServers as $name => $bin) {
        // 创建日志目录
        if (!is_dir('/home/worker/data/msf-' . $name)) {
            mkdir('/home/worker/data/msf-' . $name, 0777, true);
        }
        $tmpConf = str_replace('___BIN___', $bin, $conf);
        $tmpConf = str_replace('___NAME___', $name, $tmpConf);
        $newConfContent .= $tmpConf . "\n";
    }

    file_put_contents('/home/worker/supervisor/conf.d/msf.conf', $newConfContent);
}