#!/usr/bin/env bash

root_htaccess="root.htaccess"
parent_htaccess="../.htaccess"
settings_file="settings.inc.php"

basedir="http://localhost/my/data/"
lodspeakrdir="lodspeakr"
ns=$basedir
endpoint="http://localhost/sparql?"
everything_ok="n"


if [ -e "$parent_htaccess" ]
then
  echo ".htaccess file found in parent directory."
  echo "Please remove it to continue the installation."
  exit
fi

while [ "$everything_ok" != "y" ]
do
  echo "==Basic Information=="
  echo "lodspeakr needs to gather some basic information first to configure the installation properly"
  echo -n "Type the base url for your data, including a final / (default '$basedir'): "
  read -u 1 aux_basedir
  echo ""
  if [ "$aux_basedir" != "" ] 
  then
  	basedir=$aux_basedir
  fi


  ns=$basedir
  echo -n "Type the local namespace you will use (default '$ns'): "
  read -u 1 aux_ns
  echo ""
  if [ "$aux_ns" != "" ] 
  then
  	ns=$aux_ns
  fi

  
  echo -n "What is the URL of your SPARQL endpoint? (default $endpoint): "
  read -u 1 aux_endpoint
  echo ""
  if [ "$aux_endpoint" != "" ] 
  then
  	endpoint=$aux_endpoint
  fi

  echo "==Configuration=="
  echo "Ok, so I have the following configuration:"
  echo "Base URL is $basedir"
  echo "lodspeakr is installed at $basedir$lodspeakrdir"
  echo "The local namespace is $ns"
  echo "Your SPARQL endpoint is located at $endpoint"

  
  echo -n "Is everything ok (y/n)?"
  read -u 1 everything_ok
  
done

 if [ -e "$settings_file" ]
  then
  	ts=`date +%s`
  	settings_backup="$settings_file.$ts"
  	echo "Making a backup of existing settings at $settings_backup"
    mv $settings_file $settings_backup
  fi
  
#Getting home  
LODSPEAKR_HOME=`pwd`

  content="<?

\$conf['endpoint']['host'] = '$endpoint';
\$conf['basedir'] = '$basedir';
\$conf['home'] = '$LODSPEAKR_HOME/';

#If you want to add/overrid a namespace, add it here
\$conf['ns']['local']   = '$ns';

?>" 
echo "$content" > $settings_file
chmod 644 $settings_file
echo "New configuration file created"
echo ""
echo "WARNING: Copying $root_htaccess as .htacess in parent directory"
echo ""
cp $root_htaccess $parent_htaccess

#Creating symlinks for turtle and ntriples
cd views
ln -s default.view.rdf default.view.nt
ln -s default.view.rdf default.view.ttl
ln -s owl:Class.view.html rdfs:Class.view.html

cd ../models
ln -s default.model.rdf default.model.nt
ln -s default.model.rdf default.model.ttl
ln -s owl:Class.model.html rdfs:Class.view.html

cd ..

mkdir cache

echo ""
echo "***ATTENTION***"
echo "Remember to give permissions to the server to write in cache/ and meta/ dirs"
echo "Otherwise LODSPeaKr won't work"


