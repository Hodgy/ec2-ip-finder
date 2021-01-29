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

box validate || exit 1
box compile  || exit 1

if [ ! -f "./eif.phar" ] || [ ! -x "./eif.phar" ]; then
  (>&2 echo "Something went wrong when building eif.phar")
  (>&2 echo "Aborting.")
  exit 1
fi
