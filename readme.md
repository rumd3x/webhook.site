# [Webhook.standalone](https://webhook.site)

[![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/edmur/webhook.standalone.svg)](https://hub.docker.com/r/edmur/webhook.standalone)
[![GitHub last commit](https://img.shields.io/github/last-commit/rumd3x/webhook.standalone.svg)](https://github.com/rumd3x/webhook.standalone/commits/master)

This is a dockerized standalone version of `fredsted/webhook.site`. 

## Usage

First create a volume for persistent data (optional)

```
docker volume create webhooks-data
```

Start the container

```
docker run -d --name webhooks --restart always \
-p 80:80 -v webhooks-data:/var/www/html/storage \
edmur/webhooks.standalone
```
