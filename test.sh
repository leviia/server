#!/bin/sh

docker kill leviia-container
docker rm leviia-container

./tar.sh

docker build -f Dockerfile.test --tag leviia/leviia:test .
docker run --name leviia-container -d -p 82:80 leviia/leviia:test
#docker exec -ti leviia-container /bin/bash
