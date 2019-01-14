#!/usr/bin/env bash

# Validate only the following paths by default:
FILES=(
	./web/wp-content/themes/bumblebee/*
)

# And ignore these files (Not used when files are specified
# as arguments):
IGNORE=()

# Override FILES and reset IGNORE when CLI arguments are present
if [[ ! -z "$*" ]]; then
	FILES=($*)
	IGNORE=
fi


# Configure PHPCS
./vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs

PHPCS_ARGS=(
	--standard=WordPress
	--extensions=php
)

for i in ${IGNORE[@]}; do
	PHPCS_ARGS+=("--ignore=$i")
done

PHPCS_ARGS+=("-n")
PHPCS_ARGS+=("-s")
PHPCS_ARGS+=(${FILES[@]})
