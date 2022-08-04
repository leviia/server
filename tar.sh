#!/bin/bash

NEXTCLOUD_LOCAL_TAR=nextcloud.tar.bz2

rm -rf nextcloud
mkdir -p nextcloud

tar --create --bzip2 --file="nextcloud/$NEXTCLOUD_LOCAL_TAR" \
    --exclude="./.devcontainer" \
    --exclude="./.git" \
    --exclude="./autotest*" \
    --exclude="./babel.config.js" \
    --exclude="./build" \
    --exclude="./build.sh" \
    --exclude="./CHANGELOG.md" \
    --exclude="./CODE_OF_CONDUCT.md" \
    --exclude="./composer.lock" \
    --exclude="./composer.json" \
    --exclude="./config/config.php" \
    --exclude="./contribute" \
    --exclude="./COPYING-README" \
    --exclude="./data" \
    --exclude="./data-autotest" \
    --exclude="./Dockerfile" \
    --exclude="./Dockerfile.test" \
    --exclude="./entrypoint.sh" \
    --exclude="./Makefile" \
    --exclude="./nextcloud.tar.bz2" \
    --exclude="./nextcloud" \
    --exclude="./node_modules" \
    --exclude="./package.json" \
    --exclude="./package-lock.json" \
    --exclude="./psalm.xml" \
    --exclude="./psalm-ocp.xml" \
    --exclude="./README.md" \
    --exclude="./SECURITY.md" \
    --exclude="./tar.sh" \
    --exclude="./tests" \
    --exclude="./test.sh" \
    --exclude="./vendor-bin" \
    --exclude="./webpack.common.js" \
    --exclude="./webpack.dev.js" \
    --exclude="./webpack.modules.js" \
    --exclude="./webpack.prod.js" \
    --directory=. \
    .
