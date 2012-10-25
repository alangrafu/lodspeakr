Visualbox
=========
author: Alvaro Graves (alvaro@graves.cl)
version: 20121025

Simplest Installation
---------------------

Simply go to your web server root directory (e.g., /var/www/visualizations) and run

   bash < <(curl -sL http://visualbox.org/install)

You will be required to answer 3 questions:

* What is the location of visualbox? If you are running the script in `/var/www/visualizations` it is highly likely it will be `http://localhost/visualizations` or `http://yourdomainname/visualizations`
* What is the domain of the data you want to query? For now, you can leave it as the default (i.e., press Enter)
* What is the URL of your SPARQL endpoint? Where should Visualbox look to execute SPARQL queries.

Finally, give write permissions to the web server in `visualbox/meta`, `visualbox/cache`, `visualbox/settings.inc.php`  and `visualbox/components`. This can be done in several ways:

* `sudo chown WEBSERVERUSER visualbox/meta visualbox/cache visualbox/settings.inc.php visualbox/components`
    * **Note** You can find the name of your web server user by running `ps aux|egrep "apache|httpd|www" |egrep -v "grep|root"|awk '{print $1}'|uniq`
* Alternatively you can run `chdmod -R 777 visualbox/meta visualbox/cache visualbox/settings.inc.php visualbox/components` but this is highly discouraged

Now you can go to `http://localhost/visualizations` and you should see the welcome page. You can check now [How to create a new visualization](https://github.com/alangrafu/visualbox/wiki/How-to-create-a-new-visualization)

More documentation on installation of LODSPeaKr/Visualbox is available at the LODSPeaKr wiki:

    https://github.com/alangrafu/lodspeakr/wiki
