var gulp = require('gulp');
var gutil = require('gulp-util');
var _execute = require('child_process').exec;
var async = require('async');
var _template = require('lodash.template');
var rename = require("gulp-rename");
var less = require('gulp-less');
var phpunit = require('gulp-phpunit');
var protractor = require('gulp-protractor');
var webdriverStandalone = require('gulp-protractor').webderiver_standalone;
var webdriverUpdate = require('gulp-protractor').webdriver_update;
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');

var execute = function(command, options, callback) {
  if (options == undefined) {
    options = {};
  }

  options.maxBuffer = 1024 * 1000; // byte

  var template = _template(command);
  command = template(options);
  if (!options.silent) {
    gutil.log(gutil.colors.green(command));
  }

  if (!options.dryRun) {
    var process = _execute(command, options, callback || undefined);

    process.stdout.on('data', function (data) {
      gutil.log(data.slice(0, -1)); // remove trailing \n
    });

    process.stderr.on('data', function (data) {
      gutil.log(gutil.colors.yellow(data.slice(0, -1))); // remove trailing \n
    });

  } else {
    callback(null);
  }
};

var paths = {
  src_ng: ['src/app-ng/**/*.js', 'src/app-ng/**/*.html', 'src/assets/*'],
  src_less: ['src/app-ng/**/*.less'],
  src_api: ['src/api/**/*.php'],
  test: ['tests/**/*.php']
};

// livereload
var livereload = require('gulp-livereload');
var lr = require('tiny-lr');
var server = lr();

gulp.task('default', function() {
  // place code for your default task here
});

gulp.task('do-reload', function() {
  return gulp.src('src/index.php').pipe(livereload(server));
});

gulp.task('reload', function() {
  server.listen(35729, function(err) {
    if (err) {
      return console.log(err);
    }
    gulp.watch(paths.src_ng, [ 'do-reload' ]);
    gulp.watch(paths.src_less, [ 'less' ]);
  });
});

gulp.task('less', function() {
  gulp.src(paths.src_less)
    .pipe(sourcemaps.init())
    .pipe(less())
    .pipe(concat('site.css'))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('src/assets')
  );
});

gulp.task('upload', function(cb) {
  var options = {
    dryRun: false,
    silent : false,
    src : "src",
    dest : "root@public.languagedepot.org:/var/www/languagedepot.org_admin/htdocs/"
  };
  execute(
    'rsync -rzlt --chmod=Dug=rwx,Fug=rw,o-rwx --delete --exclude-from="upload-exclude.txt" --stats --rsync-path="sudo -u www-data rsync" --rsh="ssh" <%= src %>/ <%= dest %>',
    options,
    cb
  );
});

gulp.task('db-copy-public', function(cb) {
  var options = {
    dryRun : false,
    silent : true,
    dest : "root@public.languagedepot.org",
    password : process.env.password_db,
    user: process.env.USER
  };
  execute(
    'ssh -C <%= dest %> mysqldump -u <%= user %> --password=<%= password %> languagedepot | mysql -u <%= user %> --password=<%= password %> -D languagedepot',
    options,
    cb
  );
});

gulp.task('db-copy-private', function(cb) {
  var options = {
    dryRun : false,
    silent : true,
    dest : "root@public.languagedepot.org",
    password : process.env.password_db,
    user: process.env.USER
  };
  execute(
    'ssh -C <%= dest %> mysqldump -u <%= user %> --password=<%= password %> languagedepotpvt | mysql -u <%= user %> --password=<%= password %> -D languagedepotpvt',
    options,
    cb
  );
});

gulp.task('db-backup', function(cb) {
  var options = {
    dryRun : false,
    silent : true,
    password : process.env.password_db,
    user: process.env.USER
  };
  execute(
    'mysqldump -u <%= user %> --password=<%= password %> languagedepot languagedepotpvt | gzip > data/backup.sql.gz',
    options,
    cb
  );
});

gulp.task('update-webdriver', webdriverUpdate);
gulp.task('start-webdriver', webdriverStandalone);

gulp.task('test-php-startServer', function(cb) {
  execute(
    './build-startServer.sh',
    null,
    cb
  );
});

gulp.task('test-php', function() {
  var src = 'tests/api/phpunit.xml';
  var params = require('yargs')
    .option('debug', {
      demand: false,
      describe: 'flag to run phpunit with debug',
      type: 'boolean' })
    .option('coverage', {
      demand: false,
      describe: 'flag to run phpunit with code coverage',
      type: 'boolean' })
    .argv;
  var options = {
    dryRun: false,
    debug: false,
    logJunit: 'PhpUnitTests.xml'
  };
  if (params.debug) {
    options.debug = true;
    delete options.logJunit;
  }
  if (params.coverage) {
    options.coverageHtml = 'tests/CodeCoverage/Api/';
  }

  gutil.log("##teamcity[importData type='junit' path='PhpUnitTests.xml']");
  return gulp.src(src)
    .pipe(phpunit('src/vendor/bin/phpunit', options));
});
gulp.task('test-php').description = 'Unit tests for API';

gulp.task('watch', function() {
  gulp.watch([paths.src_api, paths.test], ['test']);
});
