# TwistAngular

Bootstrap aplikasi berbasis __REST__ menggunakan komponen:

  * [SlimFramework](http://www.slimframework.com/) - Slim Framework
  * [Twig](http://twig.sensiolabs.org/) - PHP Templating
  * [jQuery](https://jquery.com/)
  * [Twitter Bootstrap](http://getbootstrap.com/) - CSS Framework
  * [AngularJS](https://angularjs.org/)
  * [Font-Awesome](https://fortawesome.github.io/Font-Awesome/)
  * [Firebase PHP JWT](https://github.com/firebase/php-jwt) - JSON based token

## Instalasi
- Instal PHP (bisa gunakan [XAMPP](https://www.apachefriends.org/download.html) yang berisi satu paket Apache, MySQL, PHP dan Perl). 
- Instal [node.js](https://nodejs.org/)
- Instal [gulp](gulpjs.com/) dan [bower](http://bower.io/) secara global dengan perintah (jika menjalankan dari cmd acuhkan tanda $ di depan perintah):
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
- Install jQuery, bootstrap dan FontAwesome menggunakan bower dan memindahkan filenya ke folder css dan js
```sh
$ bower install
$ gulp bower
```
- Untuk dapat menggunakan livereload, pasang atau install plugin [LiveReload](https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei) (Chrome).
- Jalankan perintah berikut ini setiap kali akan bekerja:
```sh
$ gulp
```
- Buka browser dan akses __localhost__ dan pastikan muncul pesan sukses di layar browser. Untuk meng-_enable_-kan livereload pastikan icon livereload di pojok kanan atas browser tengahnya berwarna gelap (klik icon jika tengahnya masih berwarna terang)

## Tutorial
- Tutorial selengkapnya masih akan dikerjakan :D

## Lisensi
MIT
__Free Software__