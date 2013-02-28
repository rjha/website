#!/bin/bash

set -e

if [ "$UID" -eq "0" ]
then
    echo "Do not run this script as root."
    exit 100 ;
fi

#cleanup
#rm --force website-bundle* 

#generate
# website-bundle.js
# website-bundle.css

`dirname $0`/cat.php

#minify bundle files

java -jar yuicompressor-2.4.7.jar   website-bundle.js -o website-bundle.min.js  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0
java -jar yuicompressor-2.4.7.jar   website-bundle.css -o website-bundle.min.css  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0


#copy

cp ./website-bundle.min.js ../web/js/.
cp ./website-bundle.js ../web/js/.

cp ./website-bundle.css ../web/css/.
cp ./website-bundle.min.css ../web/css/.

#cleanup
rm website-bundle*
