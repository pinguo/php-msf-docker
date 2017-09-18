#!/bin/bash

RABBIT_DIR=/home/worker/rabbit-server

$RABBIT_DIR/sbin/rabbitmq-server -detached &

#printf "waiting for server start success...\n"
#sleep 300
#
#printf "grant privileges...\n"
#$RABBIT_DIR/sbin/rabbitmqctl add_user worker worker
#$RABBIT_DIR/sbin/rabbitmqctl set_user_tags worker administrator
#$RABBIT_DIR/sbin/rabbitmqctl set_permissions worker ".*" ".*" ".*"
#
#$RABBIT_DIR/sbin/rabbitmqctl add_user monitor mongitor
#$RABBIT_DIR/sbin/rabbitmqctl set_user_tags monitor monitoring
#$RABBIT_DIR/sbin/rabbitmqctl set_permissions monitor "" "" ".*"
