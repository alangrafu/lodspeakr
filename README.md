LODSPeaKr
=========

author: Alvaro Graves (alvaro@graves.cl)

version: 20130612


[LODSPeaKr](http://lodspeakr.org) is a framework for creating Linked Data applications in a simple and easy way. You can see [several applications](http://alangrafu.github.com/lodspeakr/applications.html) created using LODSPeaKr.

Simplest Installation
---------------------

Simply go to your web server root directory (e.g., /var/www/) and run

   bash < <(curl -sL http://lodspeakr.org/install)

You will be required to answer 3 questions:

* What is the location of lodspeakr? If you are running the script in `/var/www/visualizations` it is highly likely it will be `http://localhost/visualizations` or `http://yourdomainname/visualizations`
* What is the domain of the data you want to query? For now, you can leave it as the default (i.e., press Enter)
* What is the URL of your SPARQL endpoint? Where should Visualbox look to execute SPARQL queries.

Finally, give write permissions to the web server in `lodspeakr/meta`, `lodspeakr/cache`, `lodspeakr/settings.inc.php`  and `lodspeakr/components`. This can be done in several ways:

* `sudo chown WEBSERVERUSER lodspeakr/meta lodspeakr/cache lodspeakr/settings.inc.php lodspeakr/components`
    * **Note** You can find the name of your web server user by running `ps aux|egrep "apache|httpd|www" |egrep -v "grep|root"|awk '{print $1}'|uniq`
* Alternatively you can run `chdmod -R 777 lodspeakr/meta lodspeakr/cache lodspeakr/settings.inc.php lodspeakr/components` but this is highly discouraged

More documentation on installation of LODSPeaKr is available at the [LODSPeaKr wiki](https://github.com/alangrafu/lodspeakr/wiki)
