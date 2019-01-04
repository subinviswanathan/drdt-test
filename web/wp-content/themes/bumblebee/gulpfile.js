'use strict';
 
var gulp = require('gulp');
var sass = require('gulp-sass');
var es = require('event-stream');
 
sass.compiler = require('node-sass');

gulp.task('sass', function () {
  return gulp.src('./sass/**/style_main.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./'));
});

var component = ['header', 'homepage', 'listicle', 'article', 'archive', 'footer'];

// create a css per feature
gulp.task('saas:component', function () {
	return es.merge(component.map(function (item) {
		return gulp.src('./sass/' + item + '.scss')
			.pipe(sass().on('error', sass.logError))
			.pipe(gulp.dest('./'));
	}));
});
 
gulp.task('sass:watch', function () {
  gulp.watch('./sass/**/*.scss', ['sass']);
});

gulp.task('default', ['sass','saas:component']);