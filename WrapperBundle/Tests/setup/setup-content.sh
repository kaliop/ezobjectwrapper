#!/usr/bin/env bash

php vendor/ezsystems/ezpublish-community/ezpublish/console --env=behat kaliop:migration:migrate -n --path=WrapperBundle/Tests/data/001_Content.yml
