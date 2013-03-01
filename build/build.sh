#!/bin/bash

set -e

if [ "$UID" -eq "0" ]
then
    echo "Do not run this script as root."
    exit 100 ;
fi


#generate bundles

`dirname $0`/cat.php

#minify bundle files

java -jar yuicompressor-2.4.7.jar   wb-bundle.js -o wb-bundle.min.js  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0
java -jar yuicompressor-2.4.7.jar   wb-bundle.css -o wb-bundle.min.css  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0


#copy

cp ./wb-bundle.min.js ../web/js/.
cp ./wb-bundle.js ../web/js/.

cp ./wb-bundle.css ../web/css/.
cp ./wb-bundle.min.css ../web/css/.

#cleanup
rm wb-bundle*
