/* File: gulpfile.js */

var gulp  = require('gulp'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	plumber = require('gulp-plumber'),
	minify = require('gulp-clean-css'),
	sass = require('gulp-sass'),
	prefixer = require('gulp-autoprefixer'),
	sourcemaps = require('gulp-sourcemaps'),
	flatten = require('gulp-flatten'),
	watch = require('gulp-watch'),
	inject = require('gulp-inject'),
	filter = require('gulp-filter'),
  minifyInline = require('gulp-minify-inline'),
	livereload = require('gulp-livereload');


/** CONFIGURATION */
// copy files from bower
var bower = {
	js: [
		'bower_components/jquery/dist/jquery.min.js',
		'bower_components/bootstrap/dist/js/bootstrap.min.js',
		'bower_components/angular/angular.min.js'
	],
	css: [
		'bower_components/bootstrap/dist/css/bootstrap.min.css',
		'bower_components/font-awesome/css/font-awesome.min.css',
	],
	fonts: [
	'bower_components/bootstrap/fonts/*.*',
	'bower_components/font-awesome/fonts/*.*',
	] 
}

// html inject
var htmlInject = {
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
var phpSrc = [
	'./controller/**/*.php', 
	'./model/**/*.php'
];

/** END CONFIGURATION */


/** vendor bower */
gulp.task('bower', function() {
	gulp.src(bower.js).pipe(flatten()).pipe(gulp.dest('./js'));
	gulp.src(bower.css).pipe(flatten()).pipe(gulp.dest('./css'));
	gulp.src(bower.fonts).pipe(flatten()).pipe(gulp.dest('./fonts'));
});

/** vendor non bower */
gulp.task('vendor', function() {
	gulp.src('vendor/**/*.js').
		pipe(plumber()).
		pipe(uglify()).
		pipe(concat('vendor.min.js')).
		pipe(gulp.dest('./js'));

	gulp.src('vendor/**/*.css').
		pipe(plumber()).
		pipe(minify({ keepSpecialComments: 0 })).
		pipe(concat('vendor.min.css')).
		pipe(gulp.dest('./css'));
});

/** compile scss in src */
gulp.task('sass', ['html'], function() {
	return gulp.src('./src/**/*.scss').
		pipe(plumber()).
		pipe(sass()).
		pipe(prefixer({ browser: ['> 1%'], cascade: false })).
		pipe(concat('custom.min.css')).
		pipe(minify({ keepSpecialComments: 0 })).
		pipe(gulp.dest('./css')).
		pipe(livereload());
});

/** concat and minify js in src */
gulp.task('js', ['html'], function() {
	return gulp.src('./src/**/*.js').
		pipe(plumber()).
		pipe(uglify()).
		pipe(concat('custom.min.js')).
		pipe(gulp.dest('./js')).
		pipe(livereload());
});

/** copy html in src to view */
gulp.task('html', function() {
	return gulp.src('./src/**/*.html').
		pipe(inject(gulp.src(htmlInject.css, { read: false }))).
		pipe(inject(gulp.src(htmlInject.jsHead, { read: false }), { starttag: '<!-- inject:head:{{ext}} -->' })).
		pipe(inject(gulp.src(htmlInject.js, { read: false }))).
		pipe(flatten()).
    pipe(minifyInline()).
		pipe(gulp.dest('./view')).
		pipe(livereload());
});

/** watch php file in controller and model */
gulp.task('php', function() {
	return gulp.src(phpSrc, { read: false }).
		pipe(watch(phpSrc)).
		pipe(livereload());
});

/** watch task */
gulp.task('watch', function() {
	livereload.listen({ quiet: true });
	// livereload.listen({ quiet: false });
	
	// gulp-watch js and css changes then update html for inject
	gulp.src(['js/*.js', 'css/*.css'], { read: false }).
		pipe(watch(['js/*.js', 'css/*.css'], function() { gulp.start('html'); }));
	// gulp-watch untuk js
	gulp.src('src/**/*.js', { read: false }).
		pipe(watch('src/**/*.js', function() { gulp.start('js'); }));
	// gulp-watch untuk html
	gulp.src('src/**/*.html', { read: false }).
		pipe(watch('src/**/*.html', function() { gulp.start('html'); }));
	// gulp-watch untuk sass
	gulp.src('src/**/*.scss', { read: false }).
		pipe(watch('src/**/*.scss', function() { gulp.start('sass'); }));
});

/** default task */
gulp.task('default', ['php', 'html', 'js', 'sass', 'watch']);