# Docker for php-msf

# Registry

- 公网地址: `docker pull registry.cn-hangzhou.aliyuncs.com/pinguo-ops/php-msf-docker:latest`
- 阿里云经典内网: `docker pull registry-internal.cn-hangzhou.aliyuncs.com/pinguo-ops/php-msf-docker:latest`
- 阿里云VPC网络： `docker pull registry-vpc.cn-hangzhou.aliyuncs.com/pinguo-ops/php-msf-docker:latest`
- DockerHub(国外): `docker pull pinguoops/php-msf-docker`
- Full镜像(包含MongoDB和RabbitMQ): `docker pull registry.cn-hangzhou.aliyuncs.com/pinguo-ops/php-msf-docker:full`

# 镜像内容

## latest

- CentOS 6.9
- Nginx 1.6.2
- PHP 7.1.9
- Swoole 1.9.19
- Yac 2.0.2
- Xdebug 2.5.5
- Composer
- ImageMagick 7.0.7
- Redis 2.8.17
- Jq
- Apache ab
- Git 2.14.1
- NodeJS 6.x
- Python 2.7.13
- Supervisor
- ApiDoc
- Nodemon

## full 额外增加

- MongoDB 3.4.9
- RabbitMQ 3.6.12
- php-amqp 1.9.1

# Docker用户名密码

```
username: worker
password: worker
```