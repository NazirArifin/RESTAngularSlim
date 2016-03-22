# TwistAngular

Bootstrap aplikasi berbasis __REST__ menggunakan komponen:

  * [SlimFramework](http://www.slimframework.com/) - Slim Framework
  * [Twig](http://twig.sensiolabs.org/) - PHP Templating
  * [jQuery]
  * [Twitter Bootstrap] - CSS Framework
  * [AngularJS]
  * [Font-Awesome](https://fortawesome.github.io/Font-Awesome/)
  * [Firebase PHP JWT](https://github.com/firebase/php-jwt) - JSON based token

## Bagaimana Memulai
- Install PHP
- Install [node.js]
- Instal [gulp] dan [bower](http://bower.io/) dengan perintah:
```sh
$ npm install -g gulp
$ npm install -g bower
```
- Download atau clone repository ini dengan perintah:
```sh
$ git clone git@github.com:NazirArifin/TwistAngular.git
```
- Pindahkan file dalam folder ke __htdocs__, pastikan bahwa file __index.php__, __package.json__, __gulpfile.js__ dsb berada di folder root.
- Install _dependency_ gulp dengan perintah:
```sh
$ npm install
```
- Install jQuery, bootstrap dan FontAwesome menggunakan bower dan memindahkan file ke folder css dan js
```sh
$ bower install
$ gulp bower
```