#!/usr/bin/env bash
#
#

root_htaccess="root.htaccess"
parent_htaccess="../.htaccess"
settings_file="settings.inc.php"

basedir="http://localhost/my/data/"
lodspeakrdir="lodspeakr"
ns=$basedir
endpoint="http://localhost/sparql?"
everything_ok="n"

if [ -e "$parent_htaccess" ]; then
  echo
  echo "`dirname \`pwd\``/.htaccess already exists."
  echo "Please remove it to continue the installation."
  exit
fi

back_one=`cd .. 2>/dev/null && pwd`
while [ "$everything_ok" != "y" ]; do
  echo
  echo "== Basic Information =="
  echo
  echo "LODSPeaKr needs to know three (3) URIs to minimally configure itself:"
  echo 
  echo    "(1/3) At what URL will `pwd` be available? (e.g. http://localhost/`basename $back_one`/`basename \`pwd\``/)"
  echo -n "(default '$basedir'): "
  read -u 1 aux_basedir
  echo 
  aux_basedir="`echo $aux_basedir | sed 's/\/$//'`/" # remove any ending slash and append one.
  if [ "$aux_basedir" != "/" ]; then
    basedir=$aux_basedir
  fi


  ns=$basedir
  echo    "(2/3) Type the local namespace you will use "
  echo -n "(default '$ns'): "
  read -u 1 aux_ns
  echo ""
  if [ "$aux_ns" != "" ] 
  then
   ns=$aux_ns
  fi

  external=""
  extra=""
  if [[ "$basedir" =~ ^"$ns" ]]; then
   external="false"
  else
   external="true"
   extra="\$conf['ns']['base']   = '$basedir';"
  fi

  
  
  echo    "(3/3) What is the URL of your SPARQL endpoint?"
  echo -n "(default $endpoint): "
  read -u 1 aux_endpoint
  echo ""
  if [ "$aux_endpoint" != "" ] 
  then
   endpoint=$aux_endpoint
  fi

  echo "== Configuration =="
  echo
  echo "Ok, so I have the following configuration:"
  echo "Base URL is                        $basedir"
  echo "lodspeakr is installed at          $basedir$lodspeakrdir"
  echo "The local namespace is             $ns"
  echo "Your SPARQL endpoint is located at $endpoint"

  echo -n "Is everything ok (y/n)?"
  read -u 1 everything_ok
  
done

 if [ -e "$settings_file" ]; then
   ts=`date +%s`
   settings_backup="$settings_file.$ts"
   echo "Making a backup of existing settings at $settings_backup"
    mv $settings_file $settings_backup
  fi
  
LODSPEAKR_HOME=`pwd`/
  
  content="<?

\$conf['endpoint']['local'] = '$endpoint';
\$conf['home'] = '$LODSPEAKR_HOME';
\$conf['basedir'] = '$basedir';
\$conf['debug'] = false;
\$conf['mirror_external_uris'] = $external;

/*ATTENTION: By default this application is available to
 * be exported and copied (its configuration)
 * by others. If you do not want that, 
 * turn the next option as false
 */ 
\$conf['export'] = true;

#If you want to add/overrid a namespace, add it here
\$conf['ns']['local']   = '$ns';
$extra
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
ln -s rdfs:Resource.view.rdf rdfs:Resource.view.nt
ln -s rdfs:Resource.view.rdf rdfs:Resource.view.ttl
ln -s rdfs:Resource.view.rdf rdfs:Resource.view.json

cd ../models
ln -s rdfs:Resource.model.rdf rdfs:Resource.model.nt
ln -s rdfs:Resource.model.rdf rdfs:Resource.model.ttl
ln -s rdfs:Resource.model.rdf rdfs:Resource.model.json

cd ..

mkdir cache

echo
echo "*** ATTENTION ***"
echo
echo "LODSPeaKr needs the web server to have write permissions for lodspeakr/cache/ and lodspeakr/meta/."
echo "Please give the server write permissions (e.g. chmod 777 lodspeakr/cache/ lodspeakr/meta/)"
echo "Otherwise LODSPeaKr won't work"

