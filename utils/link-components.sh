#!/bin/bash
#
# Script to borrow essential bits ;rom another lodspeakr instance on local machine.
# Desired when lodspeakr essentials are maintained in a project-specfic repository.
# Contributed by Tim Lebo when trying to apply lodspeakr for DataFAQs.
#
# Usage:
#  bash-3.2$ pwd
#  /Applications/XAMPP/htdocs/hello/lodspeakr
#
#  bash-3.2$ sudo rm -rf .htaccess settings.inc.php models views
#
#  bash-3.2$ sudo /Users/lebo/projects/lodspeakr/github/lodspeakr/utils/link-components.sh /Users/lebo/projects/DataFAQs/github/DataFAQs/ui/configure-epoch/lodspeakr
#  ln -s /Users/lebo/projects/DataFAQs/github/DataFAQs/ui/configure-epoch/lodspeakr/settings.inc.php settings.inc.php
#  ln -s /Users/lebo/projects/DataFAQs/github/DataFAQs/ui/configure-epoch/lodspeakr/.htaccess .htaccess
#  ln -s /Users/lebo/projects/DataFAQs/github/DataFAQs/ui/configure-epoch/lodspeakr/models models
#  ln -s /Users/lebo/projects/DataFAQs/github/DataFAQs/ui/configure-epoch/lodspeakr/views views
#
#  bash-3.2$ sudo /Users/lebo/projects/lodspeakr/github/lodspeakr/utils/link-components.sh /Users/lebo/projects/DataFAQs/github/DataFAQs/ui/configure-epoch/lodspeakr
#  WARNING: not linking settings.inc.php because it exists.
#  WARNING: not linking .htaccess because it exists.
#  WARNING: not linking models because it exists.
#  WARNING: not linking views because it exists.

essentials='settings.inc.php .htaccess models views'
if [ $# -lt 1 ]; then
   echo "usage: `basename $0` other-lodspeakr-directory"
   echo "  soft links the following from other-lodspeakr-director to current directory: $essentials"
   exit 1 
fi

otherDir="$1"
if [[ ! -d $otherDir && "$otherDir" != "/" ]]; then
   echo "$otherDir does not exist."
   exit 1
fi

for essential in $essentials; do
   if [[ ! -e $essential && -e $otherDir/$essential ]]; then
      echo ln -s $otherDir/$essential $essential
      ln -s $otherDir/$essential
   else
      echo "WARNING: not linking $essential because it exists."
   fi
done
