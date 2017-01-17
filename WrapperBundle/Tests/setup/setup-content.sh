#!/usr/bin/env bash

EZ_VERSION=$1
EZ_APP_DIR=$2

php vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/console --env=behat kaliop:migration:migrate -n -u --path=WrapperBundle/Tests/data/001_Content.yml
