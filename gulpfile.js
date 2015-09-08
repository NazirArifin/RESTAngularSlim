/* File: gulpfile.js */

var gulp  = require('gulp'),
	/*jshint = require('gulp-jshint'),*/
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	minify = require('gulp-minify-css'),
	sass = require('gulp-sass'),
	sourcemaps = require('gulp-sourcemaps');

gulp.task('default', ['watch']);

gulp.task('build-scss', function() {
	return gulp.src('css/sass/**/*.scss').
		pipe(concat('custom.min.css')).
		pipe(sass()).
		pipe(minify()).
		pipe(gulp.dest('css'));
});

gulp.task('build-tools-css', function() {
	return gulp.src('css/tools/**/*.css').
		pipe(concat('tools.min.css')).
		pipe(minify()).
		pipe(gulp.dest('css'));
});

gulp.task('build-js', function() {
	return gulp.src('js/custom/**/*.js').
		pipe(sourcemaps.init()).
			pipe(concat('custom.min.js')).
			pipe(uglify()).
		pipe(sourcemaps.write()).
		pipe(gulp.dest('js'));
});

gulp.task('build-tools-js', function() {
	return gulp.src('js/tools/**/*.js').
		pipe(concat('tools.min.js')).
		pipe(uglify()).
		pipe(gulp.dest('js'));
});

gulp.task('build-helpers-js', function() {
	return gulp.src('js/helpers/**/*.js').
		pipe(concat('helpers.min.js')).
		pipe(uglify()).
		pipe(gulp.dest('js'));
});

gulp.task('watch', function() {
	gulp.watch('css/sass/**/*.scss', ['build-scss']);
	gulp.watch('css/tools/**/*.css', ['build-tools-css']);
	gulp.watch('js/custom/**/*.js', ['build-js']);
	gulp.watch('js/tools/**/*.js', ['build-tools-js']);
	gulp.watch('js/helpers/**/*.js', ['build-helpers-js']);
});