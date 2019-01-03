'use strict';
 
var gulp = require('gulp');
var sass = require('gulp-sass');
var eslint = require('gulp-eslint');
//var minify = require('gulp-minify');
//var sourcemaps = require('gulp-sourcemaps');

sass.compiler = require('node-sass');

gulp.task('sass', function () {
	return gulp.src('./sass/**/style.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('./'));
});

gulp.task('sass:watch', function () {
	gulp.watch('./sass/**/*.scss', ['sass']);
});

gulp.task('js:task', function () {
	return gulp.src(['./js/*.js','!./js/*-min.js'])
	// eslint() attaches the lint output to the "eslint" property
	// of the file object so it can be used by other modules.
		.pipe(eslint())
		// eslint.format() outputs the lint results to the console.
		// Alternatively use eslint.formatEach() (see Docs).
		.pipe(eslint.format())
		// To have the process exit with an error code (1) on
		// lint error, return the stream and pipe to failAfterError last.
		.pipe(eslint.failAfterError());
	//.pipe( sourcemaps.init() )
	//.pipe(minify())
	//.pipe( sourcemaps.write( '.' ) )
	//.pipe(gulp.dest('./js'));
});

gulp.task('default', ['sass', 'js:task']);