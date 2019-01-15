#!/bin/bash

# Variables
BUILD_DIR=$(pwd)
GITHUB_API_URL="https://api.github.com/repos/$CIRCLE_PROJECT_USERNAME/$CIRCLE_PROJECT_REPONAME"

# Check if we are NOT on the master branch and this is a PR
if [[ ${CIRCLE_BRANCH} != "master" && -z ${CIRCLE_PULL_REQUEST+x} ]];
then
	echo "$CIRCLE_BRANCH -- $CIRCLE_PULL_REQUEST\n\n"
	echo -e "\Lighthouse performance test will only run if not on the master branch when making a pull request"
	exit 0
fi

# Stash PR number
PR_NUMBER=${CI_PULL_REQUEST##*/}
echo -e "\nProcessing pull request #$PR_NUMBER"

# Compare against `dev` (integration), as it has all the approved and merged PRs
LIVE_SITE_URL="https://dev-$TERMINUS_SITE.pantheonsite.io"
MULTIDEV_SITE_URL="https://$TERMINUS_ENV-$TERMINUS_SITE.pantheonsite.io"

# Make artifacts directory
CIRCLE_ARTIFACTS='artifacts'
CIRCLE_ARTIFACTS_DIR='/tmp/artifacts'
mkdir -p $CIRCLE_ARTIFACTS_DIR

# Used to create a file name based on a page's label
slugify () {
    echo "$1" | iconv -t ascii//TRANSLIT | sed -r s/[~\^]+//g | sed -r s/[^a-zA-Z0-9]+/_/g | sed -r s/^_+\|_+$//g | tr A-Z a-z
}

# Branches to compare. If pushing to a PR, it will be compared against master
BRANCHES=()
URLS=()
if [[ ${CIRCLE_BRANCH} != "master" ]]; then
	BRANCHES+=($TERMINUS_ENV)
	URLS+=($MULTIDEV_SITE_URL)
fi
BRANCHES+=("master")
URLS+=($LIVE_SITE_URL)


# Track the performance metrics for all pages in `key_pages.json`
KEY_PAGES=$(cat key_pages.json)
PAGE_LABELS=(`echo $KEY_PAGES | jq '.[].label'`)
PAGE_URLS=(`echo $KEY_PAGES | jq '.[].url'`)
declare -A RESULTS=()
for ((b=0;b<${#BRANCHES[@]};++b)); do
	BRANCH=${BRANCHES[b]}
	LIGHTHOUSE_URL=${URLS[b]}

	# Ping the Pantheon environment to wake it from sleep and prime the cache
	echo -e "\nPinging the ${BRANCH} environment to wake it from sleep..."
	curl -s -I "$LIGHTHOUSE_URL" >/dev/null

	for ((i=0;i<${#PAGE_URLS[@]};++i)); do
		PAGE_NAME=${PAGE_LABELS[i]//\"}
		PAGE_SLUG=`slugify ${PAGE_NAME}`
		PAGE_URL=${PAGE_URLS[i]//\"}

		LIGHTHOUSE_RESULTS_DIR="lighthouse_results/$BRANCH/$PAGE_SLUG"
		LIGHTHOUSE_REPORT_NAME="$LIGHTHOUSE_RESULTS_DIR/lighthouse.json"
		LIGHTHOUSE_JSON_REPORT="$LIGHTHOUSE_RESULTS_DIR/lighthouse.report.json"
		LIGHTHOUSE_HTML_REPORT="$LIGHTHOUSE_RESULTS_DIR/lighthouse.report.html"
		LIGHTHOUSE_RESULTS_JSON="$LIGHTHOUSE_RESULTS_DIR/lighthouse.results.json"

		TEST_URL="$LIGHTHOUSE_URL/$PAGE_URL"

		echo -e "\nTesting $PAGE_NAME on $BRANCH branch (${TEST_URL})"

		# Delete the Lighthouse results directory so we don't keep old results around
		if [ -d "$LIGHTHOUSE_RESULTS_DIR" ]; then
		  rm -rf $LIGHTHOUSE_RESULTS_DIR
		fi

		# Create the Lighthouse results directory if it doesn't exist or has been deleted
		mkdir -p $LIGHTHOUSE_RESULTS_DIR

		# Stash Circle Artifacts URL
		CIRCLE_ARTIFACTS_URL="$CIRCLE_BUILD_URL/artifacts/$CIRCLE_NODE_INDEX/$CIRCLE_ARTIFACTS"

		# Run the Lighthouse test
		lighthouse --perf --save-artifacts --output json --output html --output-path ${LIGHTHOUSE_REPORT_NAME} --chrome-flags="--headless --disable-gpu --no-sandbox" ${TEST_URL}

		# Check for HTML report file
		if [ ! -f $LIGHTHOUSE_HTML_REPORT ]; then
			echo -e "\nLighthouse HTML report file $LIGHTHOUSE_HTML_REPORT not found!"
			exit 1
		fi

		# Check for JSON report file
		if [ ! -f $LIGHTHOUSE_JSON_REPORT ]; then
			echo -e "\nLighthouse JSON report file $LIGHTHOUSE_JSON_REPORT not found!"
			exit 1
		fi

		# Keep track of the results
		# @todo: Lighthouse on CI is running an older version. This will fail with newer formats of the JSON report
		LIGHTHOUSE_SCORE=$(cat $LIGHTHOUSE_JSON_REPORT | jq ' .score | tonumber | floor')
		RESULTS["$BRANCH-$PAGE_SLUG-SCORE"]=$LIGHTHOUSE_SCORE
		RESULTS["$BRANCH-$PAGE_SLUG-HTML_REPORT"]="$CIRCLE_ARTIFACTS_URL/$LIGHTHOUSE_HTML_REPORT"
		RESULTS["$BRANCH-$PAGE_SLUG-JSON_REPORT"]="$CIRCLE_ARTIFACTS_URL/$LIGHTHOUSE_JSON_REPORT"

		echo -e "\nLighthouse score for $PAGE_NAME in $BRANCH branch is $LIGHTHOUSE_SCORE"

		# Rsync files to CIRCLE_ARTIFACTS_DIR
		echo -e "\nRsyincing lighthouse_results files to $CIRCLE_ARTIFACTS_DIR..."
		rsync -rlvz lighthouse_results $CIRCLE_ARTIFACTS_DIR
	done
done

# If pushing right to the master branch, that's it
if [[ ${CIRCLE_BRANCH} == "master" ]]; then
	exit 0
fi


# Process the results and report back to the PR
printf '%s\n' "${RESULTS[@]}"

# Table header
THEAD=""
THEADL1="| Page/Branch "
THEADL2="| --- "
for ((b=0;b<${#BRANCHES[@]};++b)); do
	BRANCH=${BRANCHES[b]}
	THEADL1+="| ${BRANCH} "
	THEADL2+="| ---: "
done
THEADL1+="| Result |"
THEADL2+="| :---: |"
THEAD="${THEADL1}\n${THEADL2}\n"

PASSES=1
THRESHOLD=5
# Table body
TBODY="";
for ((i=0;i<${#PAGE_URLS[@]};++i)); do
	PAGE_NAME=${PAGE_LABELS[i]//\"}
	PAGE_SLUG=`slugify ${PAGE_NAME}`
	TBODY+="|${PAGE_NAME}";

	for ((b=0;b<${#BRANCHES[@]};++b)); do
		BRANCH=${BRANCHES[b]}
		SCORE=${RESULTS[$BRANCH-$PAGE_SLUG-SCORE]}
		REPORT_URL=${RESULTS[$BRANCH-$PAGE_SLUG-HTML_REPORT]}
		TBODY+="|[${SCORE}](${REPORT_URL})";
		if [[ ${BRANCH} == "master" ]]; then
			MASTER_SCORE=$SCORE
		else
			PR_SCORE=$SCORE
		fi
	done

	ACCEPTABLE_SCORE=$((MASTER_SCORE-THRESHOLD))
	if [ $PR_SCORE -lt $ACCEPTABLE_SCORE ]; then
		PASSES=0
		TBODY+="|✘|"
	else
		TBODY+="|✔|"
	fi

	TBODY+="\n"
done



echo -e "\nPosting Lighthouse results back to $LIGHTHOUSE_BRANCH "
if [ $PASSES -eq 1 ]; then
	PR_MESSAGE="Congrats! Frontend performance tests passed!\n\n"
else
	PR_MESSAGE="⚠ Frontend performance tests failed!\n\n"
fi
PR_MESSAGE+="Full Lighthouse performance results:\n\n${THEAD}${TBODY}"
curl -s -i -u "$GIT_USERNAME:$GITHUB_TOKEN" -d "{\"body\": \"$PR_MESSAGE\"}" $GITHUB_API_URL/issues/$PR_NUMBER/comments

if [ $PASSES -eq 1 ]; then
	exit 0
else
	exit 1
fi
