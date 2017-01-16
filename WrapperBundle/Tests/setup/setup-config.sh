#!/usr/bin/env bash

EZ_VERSION=$1
EZ_APP_DIR=$2
EZ_KERNEL=$3

# Set up configuration files:
# eZ5 config files
cp vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/config/parameters.yml.dist vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/config/parameters.yml
cat WrapperBundle/Tests/config/ezpublish/config_behat_${EZ_VERSION}.yml >> vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/config/config_behat.yml

# Load the wrapper bundle in the Sf kernel
sed -i 's/$bundles = array(/$bundles = array(new Kaliop\\eZObjectWrapperBundle\\KaliopeZObjectWrapperBundle(),/' vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/${EZ_KERNEL}.php
# And the Migration bundle
# we load netgen it after the Kernel bundles... hopefully OneupFlysystemBundle will stay there :-)
sed -i 's/OneupFlysystemBundle(),\?/OneupFlysystemBundle(), new Kaliop\\eZMigrationBundle\\EzMigrationBundle(),/' vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/${EZ_KERNEL}.php
# For eZPlatform, load the xmltext bundle as well
if [ "$EZ_VERSION" = "ezplatform" ]; then
    # we load it after the Kernel bundles...
    sed -i 's/AppBundle(),\?/AppBundle(), new EzSystems\\EzPlatformXmlTextFieldTypeBundle\\EzSystemsEzPlatformXmlTextFieldTypeBundle (),/' vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/${EZ_KERNEL}.php
fi
# Fix the eZ5 autoload configuration for the unexpected directory layout
sed -i "s#'/../vendor/autoload.php'#'/../../../../vendor/autoload.php'#" vendor/ezsystems/${EZ_VERSION}/${EZ_APP_DIR}/autoload.php

# Generate legacy autoloads
if [ "$EZ_VERSION" != "ezplatform" ]; then cat WrapperBundle/Tests/config/ezpublish-legacy/config.php > vendor/ezsystems/ezpublish-legacy/config.php; fi
if [ "$EZ_VERSION" != "ezplatform" ]; then cd vendor/ezsystems/ezpublish-legacy && php bin/php/ezpgenerateautoloads.php && cd ../../..; fi

# Fix the phpunit configuration if needed
if [ "$EZ_VERSION" = "ezplatform" ]; then sed -i 's/"vendor\/ezsystems\/ezpublish-community\/ezpublish"/"vendor\/ezsystems\/ezplatform\/app"/' phpunit.xml; fi
