# Webhook.standalone

[![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/edmur/webhooks.standalone.svg)](https://hub.docker.com/r/edmur/webhooks.standalone)
[![GitHub last commit](https://img.shields.io/github/last-commit/rumd3x/webhooks.standalone.svg)](https://github.com/rumd3x/webhooks.standalone/commits/master)

This is a standalone dockerized version of `fredsted/webhook.site`. 

## Usage

First create a volume for persistent data on container restart (optional)

```
docker volume create webhooks-data
```

Start the container

```
docker run -d \
--name webhooks \
--restart always \
-p 80:80 \
-v webhooks-data:/var/www/html/storage \
edmur/webhooks.standalone
```
