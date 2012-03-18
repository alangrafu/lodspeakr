#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
FILE=README
cd $DIR/../..
grep -E "^version" $FILE |head -1 |sed -e 's/version: //'
