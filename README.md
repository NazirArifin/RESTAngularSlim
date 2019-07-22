# TwistAngular

Bootstrap aplikasi berbasis __REST__ menggunakan komponen:

  * [SlimFramework](http://www.slimframework.com/) - Slim Framework
  * [Blade] (https://github.com/PhiloNL/Laravel-Blade) - PHP Templating
  * [jQuery](https://jquery.com/)
  * [Font-Awesome](https://fortawesome.github.io/Font-Awesome/)
  * [Firebase PHP JWT](https://github.com/firebase/php-jwt) - JSON based token

## Instalasi
- Instal PHP (bisa gunakan [XAMPP](https://www.apachefriends.org/download.html) yang berisi satu paket Apache, MySQL, PHP dan Perl). 
- Instal [node.js](https://nodejs.org/)
- Instal [gulp](gulpjs.com/) secara global dengan perintah (jika menjalankan dari cmd acuhkan tanda $ di depan perintah):
```sh
$ npm install -g gulp
```
- Instal [composer]()
- Download atau clone repository ini dengan perintah:
```sh
$ git clone git@github.com:NazirArifin/TwistAngular.git
```
- Pindahkan file ke web server, pastikan bahwa file __index.php__, __package.json__, __gulpfile.js__ dsb berada di folder root.
- Install _dependency_ gulp dengan perintah:
```sh
$ npm install
```
- Install _dependency_ composer dengan perintah:
```sh
$ composer install
```

- Ubah file __gulpfile.js__ fungsi browserSyncInit() bagian proxy dan sesuaikan dengan server PHP Anda.
- Jalankan perintah berikut ini setiap kali akan bekerja:
```sh
$ gulp
```

## Tutorial
- Tutorial selengkapnya masih akan dikerjakan :D

## Lisensi
MIT
__Free Software__