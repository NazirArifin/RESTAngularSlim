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
	livereload = require('gulp-livereload');

/** compile scss in src */
gulp.task('sass', function() {
	return gulp.src('./src/**/*.scss').
		pipe(plumber()).
		pipe(sass()).
		pipe(prefixer({ browser: ['> 1%'], cascade: false })).
		pipe(concat('custom.min.css')).
		pipe(minify()).
		pipe(gulp.dest('./css'));
});

/** concat and minify js in src */
gulp.task('js', function() {
	return gulp.src('./src/**/*.js').
		pipe(plumber()).
		pipe(uglify()).
		pipe(concat('custom.min.js')).
		pipe(gulp.dest('./js'));
});

/** copy html in src to view */
gulp.task('html', function() {
	return gulp.src('./src/**/*.html').
		pipe(flatten()).
		pipe(gulp.dest('./view')).
		pipe(livereload());
});

/** watch php file in controller and model */
gulp.task('php', function() {
	return gulp.src(['./controller/**/*.php', './model/**/*.php']).
		pipe(watch(['./controller/**/*.php', './model/**/*.php'])).
		pipe(livereload());
});

/** serve task */
gulp.task('serve', function() {

});

/** watch task */
gulp.task('watch', function() {
	//livereload.listen({ quiet: true });
	livereload.listen({ quiet: false });
	gulp.watch('./src/**/*.js', ['js']);
	gulp.watch('./src/**/*.html', ['html']);
	gulp.watch('./src/**/*.scss', ['sass']);
});

/** default task */
gulp.task('default', ['watch', 'php', 'html', 'js', 'sass']);