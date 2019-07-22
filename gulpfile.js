const { src, dest, parallel, watch, series } = require('gulp');
const concat = require('gulp-concat');
const cache = require('gulp-cached');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const minify = require('gulp-clean-css');
const sass = require('gulp-sass');
const prefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const flatten = require('gulp-flatten');
const inject = require('gulp-inject');
const minifyInline = require('gulp-minify-inline');
const htmlmin = require('gulp-htmlmin');
const browserSync = require('browser-sync').create();

/** CONFIGURATION */
// html inject
const htmlInject = {
	css: [
		'./css/!(custom)*.css',
		'./css/custom.min.css'
	],
	jsHead: [
		'./js/jquery.min.js',
		'./js/angular.min.js'
	],
	js: [
		'./js/!(custom|jquery|angular)*.js',
		'./js/custom.min.js'
	]
};

// file php yang diwatch
const phpSrc = [
	'./controller/**/*.php', 
	'./model/**/*.php'
];

/** END CONFIGURATION */

function vendorJS() {
  return src('vendor_public/**/*.js')
    .pipe(plumber())
    .pipe(uglify())
    .pipe(concat('vendor.min.js'))
    .pipe(dest('js'));
}
function vendorCSS() {
  return src('vendor_public/**/*.css')
    .pipe(plumber())
    .pipe(minify())
    .pipe(concat('vendor.min.css'))
    .pipe(dest('css'));
}

/** CSS */
function css() {
  return src('src/**/*.scss')
    .pipe(plumber())
    .pipe(sourcemaps.init({ largeFile: true }))
    .pipe(sass())
    .pipe(prefixer({ browsers: ['> 1%'], cascade: false }))
    .pipe(concat('custom.min.css'))
    .pipe(minify())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('css'))
    .pipe(browserSync.stream());
}
function watchCss() {
  watch('src/**/*.scss', { ignoreInitial: false }, css);
}

/** JS */
function js() {
  return src('src/**/*.js')
    .pipe(plumber())
    .pipe(babel({ presets: ['es2015'] }))
    .pipe(sourcemaps.init({ largeFile: true }))
    .pipe(concat('custom.min.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('js'))
    .pipe(browserSync.stream());
}
function watchJs() {
  watch('src/**/*.js', { ignoreInitial: false }, js);
}

/** HTML */
function html() {
  return src('src/**/*.html')
    .pipe(cache('html'))
    .pipe(
      inject(
        src(htmlInject.css, { read: false, allowEmpty: true }), { quiet: true }
      )
    )
    .pipe(
      inject(
        src(htmlInject.jsHead, { read: false, allowEmpty: true }), { quiet: true, starttag: '<!-- inject:head:{{ext}} -->' }
      )
    )
    .pipe(
      inject(
        src(htmlInject.js, { read: false, allowEmpty: true }), { quiet: true }
      )
    )
    .pipe(minifyInline())
    .pipe(htmlmin({
      collapseWhitespace: true,
      removeComments: true
    }))
    .pipe(flatten())
    .pipe(dest('view'))
    .pipe(browserSync.stream());
}
function watchHtml() {
  watch('src/**/*.html', { ignoreInitial: false }, html);
}

function browserSyncInit() {
  browserSync.init({
    proxy: '127.0.0.1:8181',
    baseDir: './',
    port: 5000,
    open: true,
    notify: false
  });
}

exports.vendor = parallel(vendorCSS, vendorJS);
exports.default = parallel(
  watchJs, watchCss, watchHtml, browserSyncInit
);