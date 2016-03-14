/* File: gulpfile.js */

var gulp  = require('gulp'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	plumber = require('gulp-plumber'),
	minify = require('gulp-minify-css'),
	sass = require('gulp-sass'),
	prefixer = require('gulp-autoprefixer'),
	sourcemaps = require('gulp-sourcemaps'),
	flatten = require('gulp-flatten'),
	watch = require('gulp-watch'),
	inject = require('gulp-inject'),
	filter = require('gulp-filter'),
	livereload = require('gulp-livereload');

/** vendor bower */
gulp.task('bower', function() {
	/**
	 * copy javascript
	 */
	gulp.src([
		'bower_components/jquery/dist/jquery.min.js',
		'bower_components/bootstrap/dist/js/bootstrap.min.js',
		'bower_components/angular/angular.min.js'
	]).
		pipe(flatten()).pipe(gulp.dest('./js'));

	/**
	 * copy css
	 */
	gulp.src([
		'bower_components/bootstrap/dist/css/bootstrap.min.css',
		'bower_components/font-awesome/css/font-awesome.min.css',
	]).
		pipe(flatten()).pipe(gulp.dest('./css'));

	/**
	 * copy fonts
	 */
	gulp.src([
		'bower_components/bootstrap/fonts/*.*',
		'bower_components/font-awesome/fonts/*.*',
	]).
		pipe(flatten()).pipe(gulp.dest('./fonts'));

	/**
	 * concat javascript
	 */
	
	/**
	 * concat css
	 */
});

/** vendor non bower */
gulp.task('vendor', function() {
	/**
	 * concat javascript
	 */
	gulp.src('vendor/**/*.js').
		pipe(plumber()).
		pipe(uglify()).
		pipe(concat('vendor.min.js')).
		pipe(gulp.dest('./js'));

	/**
	 * concat css
	 */
	gulp.src('vendor/**/*.css').
		pipe(plumber()).
		pipe(minify()).
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
		pipe(minify()).
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
		pipe(inject(gulp.src([
			'./css/!(custom)*.min.css',
			'./css/custom.min.css'
		], { read: false }))).
		pipe(inject(gulp.src([
			'./js/jquery.min.js',
			'./js/angular.min.js'
		], { read: false }), { starttag: '<!-- inject:head:{{ext}} -->' })).
		pipe(inject(gulp.src([
			// './js/*.js', 
			// '!./js/jquery.min.js',
			// './js/custom.min.js'
			'./js/!(custom|jquery|angular)*.min.js',
			'./js/custom.min.js'
		], { read: false }))).
		pipe(flatten()).
		pipe(gulp.dest('./view')).
		pipe(livereload());
});

/** watch php file in controller and model */
gulp.task('php', function() {
	return gulp.src(['./controller/**/*.php', './model/**/*.php'], { read: false }).
		pipe(watch([
			'./controller/**/*.php', 
			'./model/**/*.php'
		])).
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