#!/usr/bin/env bash

#!/bin/bash

# Automatically fix the fixable violations found in PHP code using phpcbf.

. validate-common.sh

./vendor/bin/phpcbf ${PHPCS_ARGS[@]}