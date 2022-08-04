#!/bin/sh
set -eu

if [ -z ${REDIS_HOST+x} ]; then
    echo "REDIS_HOST not set"
    exit 1
fi

echo "Configuring Redis as session handler"
{
    echo 'session.save_handler = redis'
    echo "session.save_path = \"tcp://${REDIS_HOST}:${REDIS_HOST_PORT:=6379}\""
    echo "redis.session.locking_enabled = 1"
    echo "redis.session.lock_retries = -1"
    # redis.session.lock_wait_time is specified in microseconds.
    # Wait 10ms before retrying the lock rather than the default 2ms.
    echo "redis.session.lock_wait_time = 10000"
} > /usr/local/etc/php/conf.d/redis-session.ini

exec "$@"
