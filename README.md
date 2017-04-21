web-languagedepot-stats
=======================

browse to [admin.languagedepot.org](http://admin.languagedepot.org/) to see reports.

# web-languagedepot-api #


## Recommended Development Environment ##

Our recommended development environment for web development is Linux Ubuntu GNOME.

---------------------------------

### Local Linux Development Setup <a id="LocalSetup"></a>

Start with the Ansible-assisted setup [described here](https://github.com/sillsdev/ops-devbox) to install and configure a basic development environment.


#### Installation and Deployment
After creating your Ansible-assisted setup, clone this repository. From your *home* folder...

````
mkdir src/
cd src/
git clone https://github.com/sillsdev/web-languagedepot-api
````

## Updating dependencies ##

Occasionally developers need to update composer, bower or npm.  If something isn't working after a recent code change, try updating the dependencies:

#### Update npm packages ####

In the **root** folder: `npm install`

#### Update bower ####

In the **src** folder: `bower install`

#### Update composer ####

In the **src** folder: `composer install`

## Testing ##

### PHP API and Unit Tests ###

Unit testing currently uses [PHPUnit](https://phpunit.de/) which was already installed by composer.

#### Integrating PHPUnit with PhpStorm ####

**File** -> **Settings** -> **Languages & Frameworks** -> **PHP** -> **PHPUnit**

Under PHPUnit Library, select `Use Composer autoloader` option
For `Path to script` browse to `web-languageforge-api/src/vendor/autoload.php`

Under Test Runner
Select *Default configuration file* and browse to `web-languageforge-api/tests/phpunit.xml`

Select *Default boostrap file* and browse to `web-languageforge-api/tests/TestConfig.php`

#### Running the tests ####
In the **root** folder, start the test server `gulp test-php-startServer`
In a separate terminal, `gulp test-php`.

To test with debug info `gulp test-php --debug true`

To test with code coverage `gulp test-php --coverage true`.  
This will generate test coverage report in `tests/CodeCoverage/index.html`. 

To run tests in PhpStorm, browse to the project view, right-click `tests` and select `Run tests`.
