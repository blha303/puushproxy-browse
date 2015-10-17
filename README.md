puushproxy-browse
=================

Usage
-----
* Set up [puushproxy](https://github.com/blha303/puushproxy)
* Clone repository on same machine as puushproxy
* [Install the MongoDB PECL extension](http://php.net/manual/en/mongo.installation.php)
* Copy `config.sample.php` to `config.php`.
* Edit `config.php`, add your unique salt from puushproxy.
* `ln -s path/to/puushproxy/upload/folder upload`
* Add a .htaccess file in your upload directory with `Options -Indexes` for security.
* Configure Apache to set up a website in the repository folder.
* If you have any questions, make an issue [here](https://github.com/blha303/puushproxy-browse/issues/new).

Thanks
------

* [jannispl](https://github.com/mave), for puushproxy
* [Marco Olivo](http://olivo.net), for [Imagethumb](http://olivo.net/software/imagethumb/)
* 7boats.com, for [broken.png](http://cdn.7boats.com/wp-content/uploads/2011/07/56744mw8wxhws02.jpg)
