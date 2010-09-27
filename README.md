EPIC TOME
=========

Epic Tome is a set of web application tools for [Epic TCG](http://epictcg.com).
It's written in PHP, MySQL, jQuery, and DHTML.

You can view the current version of the site [here](http://epictome.com).

Currently, there are two tools available:

* A searchable spoiler, with card images and a Google Checkout storefront.
* A sealed deck generator

Installation
------------

Epic Tome requires a web server with PHP enabled and access to a MySQL
database. Follow these steps:

1. Get the code repository

        git clone git://github.com/wmorganjr/epictome.git

2. Insert the card data into your database

        mysql -h your.host -u your.username -p your.db < cards.sql

3. Edit config.php to have access to your database. It's strongly recommended
that you use a separate user account for this. Only SELECT access is needed.

4. If you want card images, you have to download them separately, into the
scans/ directory. You can pull them from the main site:

        wget -r -l1 -nd -P scans -A.jpg epictome.com/scans

License
-------

The Epic Trading Card Game and all card images are Copyright 2009 Epic Trading Card Game and redistributed with permission.

All other content is Copyright 2010 Will Morgan. 

This software is distributed under the terms of the Eclipse Public License v1.

