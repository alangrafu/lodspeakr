#!/bin/bash
#
# Script to borrow essential bits from another lodspeakr.
# Desired when lodspeakr essentials are maintained in a project-specfic repository.
# Contributed by Tim Lebo when trying to apply lodspeakr for DataFAQs.

if [ $# -lt 1 ]; then
   echo "usage: `basename $0` other-lodspeakr-directory"
   exit 1 
fi

if [ ! -d $otherDir ]; then
   echo "$otherDir does not exist."
   exit 1
fi

if [[ ! -e settings.inc.php && -e $otherDir/settings.inc.php ]]; then
   echo ln -s $otherDir/settings.inc.phpl settings.inc.php
   ln -s $otherDir/settings.inc.phpl settings.inc.php
else
   echo "WARNING: not linking settings.inc.phpl because it exists."
fi

if [[ ! -e .htaccess && -e $otherDir/.htaccess ]]; then
   echo ln -s $otherDir/models models
   ln -s $otherDir/models models
else
   echo "WARNING: not linking models/ because it exists."
fi

if [[ ! -e models && -e $otherDir/models ]]; then
   echo ln -s $otherDir/models models
   ln -s $otherDir/models models
else
   echo "WARNING: not linking models/ because it exists."
fi

if [[ ! -e views && -e $otherDir/views ]]; then
   echo ln -s $otherDir/views views
   ln -s $otherDir/views views
else
   echo "WARNING: not linking views/ because it exists."
fi
