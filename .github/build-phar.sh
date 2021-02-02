#!/bin/sh

# -e  Exit immediately if a command exits with a non-zero status.
# -u  Treat unset variables as an error when substituting.
set -eu

if [ "$(command -v box 2>/dev/null || true)" = "" ]; then
  (>&2 printf "To use this script you need to install humbug/box: %s \\n" \
    "https://github.com/humbug/box")
  (>&2 echo "Aborting.")
  exit 1
fi

VERSION=$(git describe --tags --exact-match HEAD 2>/dev/null || git log --pretty="%H" -n1 HEAD)
sed -i "s/@package_version@/$VERSION/g" "src/Ec2IpFinder.php"

RELEASE_DATE=$(date +"%Y-%m-%d %T")
sed -i "s/@release_date@/$VERSION/g" "src/Ec2IpFinder.php"

box validate || exit 1
box compile  || exit 1

if [ ! -f "./eif.phar" ] || [ ! -x "./eif.phar" ]; then
  (>&2 echo "Something went wrong when building eif.phar")
  (>&2 echo "Aborting.")
  exit 1
fi
