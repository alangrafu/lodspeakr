#!/bin/bash

mysql_port="3360"
mysql_host="localhost"
mysql_user="root"
mysql_pass=""

basedir="http://localhost/"
ns=$basedir
endpoint="http://localhost/sparql?"
everything_ok="n"

while [ "$everything_ok" != "y" ]
do
  echo "==Basic Information=="
  echo "slodps needs to gather some basic information first to configure the installation properly"
  echo -n "Type the base url of your site where slodps' index.php will be located (default '$basedir'): "
  read aux_basedir
  echo ""
  if [ "$aux_basedir" != "" ] 
  then
  	basedir=$aux_basedir
  fi
  
  echo -n "Type the local namespace you will use (default '$ns'): "
  read aux_ns
  echo ""
  if [ "$aux_ns" != "" ] 
  then
  	ns=$aux_ns
  fi

  echo -n "What is the URL of your SPARQL endpoint? (default $endpoint): "
  read aux_endpoint
  echo ""
  if [ "$aux_endpoint" != "" ] 
  then
  	endpoint=$aux_endpoint
  fi

  echo "==Internal SPARQL endpoint=="
  echo "slodps reads from your SPARQL endpoint, however it needs to add metadata to its own SPARQL endpoint"
  echo ""
  echo "Ok, to create slodps' endpoint I need a MySQL database"
  echo -n "What is host where the database is located (default $mysql_host)? "
  echo ""
  read aux_mysql_host
  if [ "$aux_mysql_host" != "" ] 
  then
  	mysql_host=$aux_mysql_host
  fi
  	
  echo -n "What is port the database is listening to (default $mysql_port)? "
  read aux_mysql_port  
  echo ""
  if [ "$aux_mysql_port" != "" ] 
  then
  	mysql_port=$aux_mysql_port
  fi

  echo -n "What is user for this database (default $mysql_user)? "
  read mysql_user
  echo ""
  if [ "$aux_mysql_user" != "" ] 
  then
  	mysql_user=$aux_mysql_user
  fi
  
  echo -n "What is password for $mysql_user? "
  read mysql_pass
  echo ""
  if [ "$aux_mysql_pass" != "" ] 
  then
  	mysql_pass=$aux_mysql_pass
  fi

  echo "==Configuration=="
  echo "Ok, so I have the following configuration:"
  echo "slodps is installed at $basedir"
  echo "The local namespace is $ns"
  echo "Your SPARQL endpoint is located at $endpoint"
  echo "For slodps internal sparql endpoint the configuration is as follows:"
  echo "Host: $mysql_host"
  echo "Port: $mysql_port"
  echo "User: $mysql_user"
  echo "Pass: $mysql_pass"
  
  echo -n "Is everything ok (y/n)?"
  read everything_ok
  
done
