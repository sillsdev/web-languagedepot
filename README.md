web-languagedepot-stats
=======================

# web-languagedepot-api #

## Manually install and configure mysql ##

```
sudo apt-get install mysql-server
```
When prompted, set the default password to '*password*'. 

Create the users, databases and grant all privileges to local user.  Replace `<USER>` with your username.
```
mysql -u root -p
create database languagedepot;
create database languagedepotpvt;
create user '<USER>'@'localhost';
grant all on *.* to '<USER>'@'localhost';
create user 'test'@'localhhost' identified by 'test';
grant all on *.* to 'test'@'localhost';
quit
```

Restore from command prompt
```
mysql languagedepot < languagedepot.sql
mysql languagedepotpvt < languagedepotpvt.sql
```
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

Select *Default bootsrap file* and browse to `web-languageforge-api/tests/TestConfig.php`

#### Running the tests ####
In a separate terminal, `gulp test-php`.

To test with debug info `gulp test-php --debug true`

To test with code coverage `gulp test-php --coverage true`.  
This will generate test coverage report in `tests/CodeCoverage/index.html`. 

To run tests in PhpStorm, browse to the project view, right-click `tests` and select `Run tests`.
