#!/bin/bash

echo "Please enter version X.Y.Z.n (leave empty if only dev)"
read SERVER_VERSION

if [ -z "$SERVER_VERSION" ]; then
    echo "Result version will be build: leviia/leviia:dev"
else
    echo "Result version will be build: leviia/leviia:dev-$SERVER_VERSION"
fi

echo "Press enter to proceed"
read TEMP

echo "tar.sh running"
./tar.sh

echo "docker build running"
if [ -z "$SERVER_VERSION" ]; then
    docker build --tag leviia/leviia:dev --no-cache .
    docker push leviia/leviia:dev
else
    docker build --tag leviia/leviia:dev-$SERVER_VERSION --no-cache .
    docker push leviia/leviia:dev-$SERVER_VERSION
fi
