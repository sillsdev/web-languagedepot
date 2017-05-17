// -------------------------------------
//   Task: Display Tasks
// gulp -T                 Print the task dependency tree
// gulp --tasks-simple     Print a list of gulp task names
// -------------------------------------

var gulp = require('gulp');
var gutil = require('gulp-util');
var _execute = require('child_process').exec;
var async = require('async');
var _template = require('lodash.template');
var rename = require("gulp-rename");
var less = require('gulp-less');
var phpunit = require('gulp-phpunit');
var protractor = require('gulp-protractor');
var webdriverStandalone = require('gulp-protractor').webdriver_standalone;
var webdriverUpdate = require('gulp-protractor').webdriver_update;
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var path = require('path');
var livereload = require('gulp-livereload');
var lr = require('tiny-lr');
var server = lr();

var execute = function(command, options, callback) {
  if (!options) {
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
  test: ['test/**/*.php']
};

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
    'mysqldump -u <%= user %> --password=<%= password %> languagedepot | gzip > data/languagedepot.sql.gz;' +
    'mysqldump -u <%= user %> --password=<%= password %> languagedepotpvt | gzip > data/languagedepotpvt.sql.gz;',
    options,
    cb
  );
});

//region less

gulp.task('less', function() {
  return gulp.src(paths.src_less)
    .pipe(sourcemaps.init())
    .pipe(less())
    .pipe(concat('site.css'))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('src/assets')
    );
});

// endregion less

//region test

// -------------------------------------
//   Task: test-php-setupTestEnvironment
// -------------------------------------
gulp.task('test-php-setupTestEnvironment', function (cb) {
  var options = {
    dryRun: false,
    silent: false,
    sqlFile: 'test/testlanguagedepot.sql'
  };
  execute(
    'mysql languagedepot < <%= sqlFile %>; ' +
    'mysql languagedepotpvt < <%= sqlFile %>',
    options,
    cb
  );
});

// -------------------------------------
//   Task: test-php-run
// -------------------------------------
gulp.task('test-php-run', function() {
  var src = 'test/php/phpunit.xml';
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
    options.coverageHtml = 'test/CodeCoverage/php';
  }

  gutil.log("##teamcity[importData type='junit' path='PhpUnitTests.xml']");
  return gulp.src(src)
    .pipe(phpunit('src/vendor/bin/phpunit', options));
});
gulp.task('test-php-run').description = 'run API and Unit tests';

// -------------------------------------
//   Task: test-php
// -------------------------------------
gulp.task('test-php',
  gulp.series(
    'test-php-setupTestEnvironment',
    'test-php-run')
);

// -------------------------------------
//   Task: test-php-watch
// -------------------------------------
gulp.task('test-php-watch', function () {
  gulp.watch([paths.src_api, paths.test], ['test-php']);
});

// -------------------------------------
//   Task: E2E Test: Webdriver Update
// -------------------------------------
gulp.task('test-e2e-webdriver_update', webdriverUpdate);

// -------------------------------------
//   Task: E2E Test: Webdriver Standalone
// -------------------------------------
gulp.task('test-e2e-webdriver_standalone', webdriverStandalone);

// -------------------------------------
//   Task: Test Restart Webserver
// -------------------------------------
gulp.task('test-restart-webserver', function (cb) {
  execute(
    'sudo service apache2 restart',
    null,
    cb
  );
});

// endregion test

// region build

// -------------------------------------
//   Task: Build Composer
// -------------------------------------
gulp.task('build-composer', function (cb) {
  var options = {
    dryRun: false,
    silent: false,
    cwd: './src'
  };
  execute(
    'composer install',
    options,
    cb
  );
});

// -------------------------------------
//   Task: Build Bower
// -------------------------------------
gulp.task('build-bower', function (cb) {
  var options = {
    dryRun: false,
    silent: false,
    cwd: './src'
  };
  execute(
    'bower install',
    options,
    cb
  );
});

// -------------------------------------
//   Task: Change Group to www-data
// -------------------------------------
gulp.task('build-changeGroup', function (cb) {
  execute(
    'sudo chgrp -R www-data src; sudo chgrp -R www-data test; ',
    null,
    cb
  );
});

gulp.task('build-changeGroup').description =
  'Ensure www-data is the group for src and test folder';

// -------------------------------------
//   Task: Build Upload to destination
// -------------------------------------
gulp.task('build-upload', function (cb) {
  var params = require('yargs')
    .option('dest', {
      demand: true,
      type: 'string' })
    .option('uploadCredentials', {
      demand: true,
      type: 'string' })
    .argv;
  var options = {
    dryRun: false,
    silent: false,
    includeFile: 'upload-include.txt',  // read include patterns from FILE
    excludeFile: 'upload-exclude.txt',  // read exclude patterns from FILE
    rsh: '--rsh="ssh -v -i ' + params.uploadCredentials + '"',
    src: 'src/',
    dest: path.join(params.dest, 'htdocs')
  };

  execute(
    'rsync -progzlt --chmod=Dug=rwx,Fug=rw,o-rwx ' +
    '--delete-during --stats --rsync-path="sudo rsync" <%= rsh %> ' +
    '--include-from="<%= includeFile %>" ' +
    '--exclude-from="<%= excludeFile %>" ' +
    '<%= src %> <%= dest %>',
    options,
    cb
  );

  // For E2E tests, upload test dir to destination
  if (params.dest.includes('e2etest')) {
    options.src = 'test/';
    options.dest = path.join(params.dest, '/test');

    execute(
      'rsync -progzlt --chmod=Dug=rwx,Fug=rw,o-rwx ' +
      '--delete-during --stats --rsync-path="sudo rsync" <%= rsh %> ' +
      '<%= src %> <%= dest %> --exclude php',
      options,
      cb
    );
  }
});

// -------------------------------------
//   Task: Build (General)
// -------------------------------------
gulp.task('build',
  gulp.series(
    gulp.parallel(
      'build-composer',
      'build-bower'),
    'less',
    'build-changeGroup')
);

// -------------------------------------
//   Task: Build and Upload to destination
// -------------------------------------
gulp.task('build-and-upload',
  gulp.series(
    'build',
    'build-upload',
    'test-restart-webserver')
);

// -------------------------------------
//   Task: Build, PHP Tests, Upload
// -------------------------------------
gulp.task('build-php',
  gulp.series(
    'build',
    'test-php',
    'build-upload',
    'test-restart-webserver')
);
gulp.task('build-php').description =
  'Build and Run PHP tests on CI server; Deploy to dev site';

// endregion build

gulp.task('default', gulp.series('build'));
