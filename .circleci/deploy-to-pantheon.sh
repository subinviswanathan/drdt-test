#!/bin/bash

set -ex

TERMINUS_DOES_MULTIDEV_EXIST()
{
    # Return 0 if on master since dev always exists
    if [[ ${CIRCLE_BRANCH} == "master" ]]
    then
        return 0;
    fi
    
    # Stash list of Pantheon multidev environments
    PANTHEON_MULTIDEV_LIST="$(terminus multidev:list -n ${TERMINUS_SITE} --format=list --field=Name)"

    while read -r multiDev; do
        if [[ "${multiDev}" == "$1" ]]
        then
            return 0;
        fi
    done <<< "$PANTHEON_MULTIDEV_LIST"

    return 1;
}

if [[ (${CIRCLE_BRANCH} != "master" && -z ${CIRCLE_PULL_REQUEST+x}) || (${CIRCLE_BRANCH} == "master" && -n ${CIRCLE_PULL_REQUEST+x}) ]];
then
    echo -e "CircleCI will only deploy to Pantheon if on the master branch or creating a pull requests.\n"
    exit 0;
fi

if ! TERMINUS_DOES_MULTIDEV_EXIST ${TERMINUS_ENV}
then
    terminus env:wake -n "$TERMINUS_SITE.dev"
    terminus build:env:create -n "$TERMINUS_SITE.dev" "$TERMINUS_ENV" --clone-content --yes
    action="Created new"
else
    terminus build:env:push -n "$TERMINUS_SITE.$TERMINUS_ENV" --yes
    action="Updated"
fi

terminus secrets:set -n "$TERMINUS_SITE.$TERMINUS_ENV" token "$GITHUB_TOKEN" --file='github-secrets.json' --clear --skip-if-empty

# Add a PR message every time code gets deployed
site_url="https://$TERMINUS_ENV-$TERMINUS_SITE.pantheonsite.io"
# @todo: Add Pantheon Dashboard URL
body="${action} staging environment for ${TERMINUS_ENV}."
if [ -n "$site_url" ] ; then
    body+="Here are some useful links:\n\n[WordPress Admin](${site_url}/wp/wp-admin)\n\n"

    key_pages=$(cat key_pages.json)
    page_labels=(`echo $key_pages | jq '.[].label'`)
    page_urls=(`echo $key_pages | jq '.[].url'`)
    for ((i=0;i<${#page_urls[@]};++i)); do
        body+="[${page_labels[i]//\"}](${site_url}/${page_urls[i]//\"})\n"
    done
fi

PR_NUMBER=${CI_PULL_REQUEST##*/}
GITHUB_API_URL="https://api.github.com/repos/$CIRCLE_PROJECT_USERNAME/$CIRCLE_PROJECT_REPONAME"
curl -s -i -u "$GIT_USERNAME:$GITHUB_TOKEN" -d "{\"body\": \"$body\"}" $GITHUB_API_URL/issues/$PR_NUMBER/comments

printf "$body\n"


# Cleanup old multidevs
terminus build:env:delete:pr -n "$TERMINUS_SITE" --yes
