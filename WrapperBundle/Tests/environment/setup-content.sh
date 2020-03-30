#!/usr/bin/env bash

if [ "${EZ_VERSION}" = "ezplatform3" ]; then
    APP_DIR=vendor/ezsystems/ezplatform
elif [ "${EZ_VERSION}" = "ezplatform2" ]; then
    APP_DIR=vendor/ezsystems/ezplatform
elif [ "${EZ_VERSION}" = "ezplatform" ]; then
    APP_DIR=vendor/ezsystems/ezplatform
elif [ "${EZ_VERSION}" = "ezpublish-community" ]; then
    APP_DIR=vendor/ezsystems/ezpublish-community
else
    echo "Unsupported eZ version: ${EZ_VERSION}"
    exit 1
fi

php ${APP_DIR}/console --env=behat kaliop:migration:migrate -n -u --path=WrapperBundle/Tests/data/001_Content.yml
