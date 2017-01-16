#!/usr/bin/env bash

EZ_VERSION=$1

# We do not rely on the requirements set in composer.json, but install a different eZ version depending on the test matrix
# For the moment, to install eZPlatform, a set of DEV packages have to be allowed; really ugly sed expression to alter composer.json follows
# TODO is this still needed?
if [ "$EZ_VERSION" = "ezplatform" ]; then sed -i 's/"license": "GPL-2.0",/"license": "GPL-2.0", "minimum-stability": "dev", "prefer-stable": true,/' composer.json; fi
