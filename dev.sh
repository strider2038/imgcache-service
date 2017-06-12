#!/bin/bash

echo "Cleaning old images..."
echo "========================================================================="
docker stop $(docker ps -a -q)
docker rm $(docker ps -a -q)

./build.sh

echo "Building development image..."
echo "========================================================================="

docker build \
    --file ./docker/Dockerfile.dev \
    --tag strider2038:imgcache-service-dev \
    ./docker

echo "Starting container..."
echo "========================================================================="

docker run \
    -p 80:80 -p 9002:9001 \
    --detach \
    --name imgcache \
    --stop-signal SIGKILL \
    --volume $PWD:/services/imgcache \
    strider2038:imgcache-service-dev