#!/bin/bash

ln -sf /usr/share/zoneinfo/Asia/Chongqing /etc/localtime

set|grep '_.*=' >/home/worker/.ssh/environment

MODULES="php supervisor redis-6379 redis-6380 redis-6381 redis-7379 redis-7380 redis-7381 memcached mongodb rabbitmq"
for i in $MODULES
do
mkdir -p /home/worker/data/$i/log
mkdir -p /home/worker/data/$i/run
done
mkdir -p /home/worker/data/nginx/logs
mkdir -p /home/worker/data/www/runtime/xhprof

# chown
chown worker.worker /home/worker
chown worker.worker /home/worker/data
chown worker.worker /home/worker/data/www
dotfile=`cd /home/worker && find . -maxdepth 1 -name '*' |sed -e 's#^.$##' -e 's#^.\/##' -e 's#^data$##'`
datadir=`cd /home/worker/data && find . -maxdepth 1 -name '*' |sed -e 's#^.$##' -e 's#^.\/##' -e 's#^www$##'`
cd /home/worker && chown -R  worker.worker $dotfile
cd /home/worker/data && chown -R  worker.worker $datadir

chown root.worker /home/worker/nginx/sbin/nginx
chmod u+s /home/worker/nginx/sbin/nginx

chmod 700 /home/worker/.ssh
chmod 600 /home/worker/.ssh/authorized_keys

echo '/etc/init.d/sshd start'
/etc/init.d/sshd start

if [ -f /home/worker/bin/init.sh ]; then
    echo '/home/worker/bin/init.sh'
    chmod a+x /home/worker/bin/init.sh
    su worker -c '/home/worker/bin/init.sh'
fi

echo 'supervisord -c /home/worker/supervisor/supervisord.conf'
supervisord -c /home/worker/supervisor/supervisord.conf
