#!/usr/bin/env bash
#
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

root_htaccess="root.htaccess"
parent_htaccess="../.htaccess"
settings_file="settings.inc.php"

metadb="db.sqlite"
baseUrl="http://localhost/my/data"
home=`basename \`pwd\`` # 'lodspeakr', the directory from git clone
ns=$baseUrl
endpoint="http://localhost/sparql"
everything_ok="n"

if [ -e "$parent_htaccess" ]; then
  echo
  echo "`dirname \`pwd\``/.htaccess already exists."
  echo "Please remove it to continue the installation."
  exit 1
fi

back_one=`cd .. 2>/dev/null && pwd`
parent=`basename $back_one`

utils/create_db.sh $metadb



_baseurl=
_basens=
_sparqlendpoint=
_chown=
_chmod=
_component=

options=$@

# An array with all the arguments
arguments=($options)

# Loop index
index=0

for argument in $options
  do
    # Incrementing index
    index=`expr $index + 1`

    # The conditions
    case $argument in
      base-url=*) val=${argument#*=};
                  opt=${argument%=$val};
                  _baseurl="${val}" ;;
      base-namespace=*) val=${argument#*=};
                  opt=${argument%=$val};
                  _basens="${val}" ;;
      sparql-endpoint=*) val=${argument#*=};
                  opt=${argument%=$val};
                  _sparqlendpoint="${val}" ;;
      chown=*) val=${argument#*=};
                  opt=${argument%=$val};
                  _chown="${val}" ;;
      chmod=*) val=${argument#*=};
                  opt=${argument%=$val};
                  _chmod="${val}" ;;
    esac
  done


if [ ! -z "$_baseurl"  ] && [ ! -z "$_basens"  ] && [ ! -z "$_sparqlendpoint"  ]; then
  baseUrl="`echo $_baseurl | sed 's/\/$//'`/"
  ns=$_basens
  endpoint=$_sparqlendpoint
  everything_ok="y"
fi


while [ "$everything_ok" != "y" ]; do
  echo
  echo "== Basic Information =="
  echo
  echo "LODSPeaKr needs to know three (3) URIs to minimally configure itself:"
  echo 
  echo    "(1/3) At what URL will `pwd |sed -e 's/lodspeakr$//'` be available? (e.g. http://localhost/$parent/)"
  echo    "**Note** DO NOT include 'lodspeakr/' in the URL"
  echo -n "(default '$baseUrl'): "
  read -u 1 aux_baseUrl
  echo 
  if [ "$aux_baseUrl" != "" ]; then
    baseUrl=$aux_baseUrl
  fi
  baseUrl="`echo $baseUrl | sed 's/\/$//'`/" # remove any ending slash and append one.
  
  #Suggest baseUrl+"/sparql" for default endpoint
  if [ "$baseUrl" != "" ]; then
    endpoint=$baseUrl"sparql"
  fi
  ns=$baseUrl
  echo    "(2/3) What local namespace you will use?"
  echo -n "(default '$ns'): "
  read -u 1 aux_ns
  echo ""
  if [ "$aux_ns" != "" ]; then
    aux_ns="`echo $aux_ns | sed 's/\/$//'`/" # remove any ending slash and append one.
    ns=$aux_ns
  fi
  
  echo    "(3/3) What is the URL of your SPARQL endpoint?"
  echo -n "(default $endpoint): "
  read -u 1 aux_endpoint
  echo ""
  if [ "$aux_endpoint" != "" ]; then
    endpoint=$aux_endpoint
  fi

  echo "== Configuration =="
  echo
  echo "Ok, so I have the following configuration:"
  echo
  echo "Base URL is                        $baseUrl"
  echo "The local namespace is             $ns"
  echo "Your SPARQL endpoint is located at $endpoint"
  echo
  echo "lodspeakr is installed at          $DIR"

  echo
  echo -n "Complete installation? (y/n)? "
  read -u 1 everything_ok
done

external=""
extra=""
if [[ "$baseUrl" =~ ^"$ns" ]]; then
  external="false"
else
  external="\$conf['ns']['local']"
  extra="\$conf['ns']['base']   = '$baseUrl';"
fi

if [ -e "$settings_file" ]; then
  ts=`date +%s`
  settings_backup="$settings_file.$ts"
  echo "Making a backup of existing settings at $settings_backup"
  mv $settings_file $settings_backup
fi
  
LODSPEAKR_HOME=`pwd`/
  
content="<?php

\$conf['endpoint']['local'] = '$endpoint';
\$conf['home'] = '$LODSPEAKR_HOME';
\$conf['basedir'] = '$baseUrl';
\$conf['debug'] = false;

\$conf['ns']['local']   = '$ns';
$extra

\$conf['mirror_external_uris'] = $external;

//Variables in $lodspk can be used to store user info.
//For examples, 'title' will be used in the header
//(you can forget about all conventions and use your own as well)
\$lodspk['title'] = 'LODSPeaKr';
?>" 
echo "$content" > $settings_file
chmod 644 $settings_file
echo "Created new configuration file: $settings_file"
echo ""
echo "WARNING: Copying $root_htaccess as .htaccess in parent directory"
echo ""

echo "<IfModule mod_rewrite.c>" > $parent_htaccess
echo "RewriteEngine on" >> $parent_htaccess
echo >> $parent_htaccess
newBase=`echo $baseUrl|sed -e "s|https\{0,1\}://[^\/]*||g"`
echo "RewriteBase $newBase" >> $parent_htaccess
cat $root_htaccess >> $parent_htaccess
echo "RewriteRule ^(.+)\$ $home/index.php?q=\$1 [L]" >> $parent_htaccess
echo "</IfModule>" >> $parent_htaccess
mkdir -p cache

if [ ! -z "$_components" ]; then
  cp -r doc/examples/originalComponents components
  mkdir -p components/uris
else
  ln -s "$_components"
done

bold=`tput bold`
normal=`tput sgr0`
wwwUser=`ps aux|egrep "apache|httpd|www" |egrep -v "grep|root"|awk '{print $1}'|uniq|tail -1`  

if [ ! -z "$_chmod" ]; then
  echo
  echo "WARNING: Automatically changing permissions of cache, meta, components and settings.inc.php to $_chmod"
  echo
  chmod -R $_chmod  $DIR/cache $DIR/meta $DIR/components $DIR/settings.inc.php 
elif [ ! -z "$_chown" ]; then
  echo
  echo "WARNING: Automatically changing ownership of cache, meta, components and settings.inc.php to $_chown"
  echo
  chown -R $_chown $DIR/cache $DIR/meta $DIR/components $DIR/settings.inc.php
else
  echo
  echo "                                      *** ATTENTION ***"
  echo
  echo "LODSPeaKr needs the web server to have write permissions for $home/cache/ $home/meta/ $home/components and $home/settings.inc.php."
  echo
  echo
  echo "Common ways of doing this:"
  if [ "$wwwUser" != "" ]; then
    echo " ${bold}chmod -R g+w $home/cache $home/meta $home/settings.inc.php${normal}; sudo chgrp -R $wwwUser $home/cache $home/meta $home/settings.inc.php${normal} "
    echo "OR"
    echo " ${bold}chmod -R 777 $home/cache $home/meta $home/settings.inc.php${normal} (highly discouraged but useful to test when everything fails. It shouldn't be used in production sites)"
  else
    echo " ${bold}chown -R www-data $home/cache $home/meta${normal} $home/components $home/settings.inc.php (find the name of the apache user in your system)"
    echo " ${bold}chown -R www-apache $home/cache $home/meta${normal} $home/components $home/settings.inc.php (find the name of the apache user in your system)"
    echo " ${bold}chown -R apache $home/cache $home/meta${normal} $home/components $home/settings.inc.php (find the name of the apache user in your system)"
    echo " ${bold}chmod -R g+w $home/cache $home/meta${normal} $home/components $home/settings.inc.php (if you have a group in common with the apache user)"
    echo " ${bold}chmod -R 777 $home/cache $home/meta${normal} $home/components $home/settings.inc.php (highly discouraged but useful to test when everything fails. It shouldn't be used in production sites)"
  fi
  echo
  echo "Please give the server write permissions. Otherwise, LODSPeaKr will NOT WORK."
  echo
  echo "See https://github.com/alangrafu/lodspeakr/wiki/Installation for further information"
fi
  echo
  echo "--------------------------------------------------------------------------------------------------------"
  echo "You can now visit ${bold}$baseUrl${normal} to navigate through your data."
  echo "--------------------------------------------------------------------------------------------------------"
  echo

