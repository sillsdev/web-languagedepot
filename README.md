# web-languagedepot-api #

## Recommended Development Environment ##

Our recommended development environment for web development is Linux Ubuntu GNOME.

## Local Linux Development Setup ##

Run the Ansible-assisted setup [described here](https://github.com/sillsdev/ops-devbox) to install and configure a basic development environment.

### Manually Install and Configure MySQL ###

```
sudo apt-get install mysql-server
```
When prompted, set the default password to '*password*'. 

Create the users, databases and grant all privileges to local user.  Replace `<USER>` with your username.
```
mysql -u root -p
CREATE DATABASE languagedepot CHARACTER SET UTF8 ;
CREATE DATABASE languagedepotpvt CHARACTER SET UTF8;
create user '<USER>'@'localhost';
grant all on *.* to '<USER>'@'localhost';
create user 'test'@'localhost' identified by 'test';
grant all on *.* to 'test'@'localhost';
quit
```

Optional step if you have live site backups *languagedepot.sql* and *languagedepotpvt.sql* to restore into local environment:
```
mysql languagedepot < languagedepot.sql
mysql languagedepotpvt < languagedepotpvt.sql
```

### Installation and Deployment ###
1. If you are developing **hgresume**, clone its repo first [here](https://github.com/sillsdev/hgresume/blob/master/README.md#local-linux-development-setup).

2. Clone this repository. From your *home* folder...

```
mkdir src
cd src
git clone https://github.com/sillsdev/web-languagedepot-api --recurse-submodules
```

  * The `--recurse-submodules` is used to fetch many of the Ansible roles used by the Ansible playbooks in the deploy folder. If you've already cloned the repo without `--recurse-submodules`, run `git submodule update --init --recursive` to pull and initialize them.

3. Run the following Ansible playbooks to configure Ansible and run the site.

```
cd web-languagedepot-api/deploy
ansible-playbook -i hosts playbook_create_config.yml --limit localhost
ansible-playbook playbook_xenial.yml --limit localhost -K
```

4. Install `node_modules` used to build *Less* files and be able to run E2E tests
```
cd ..
npm install
gulp less
```

5. If you don't have live site backups, running the unit tests (below) will populate the MySQL database with some data (which is needed for the site to work).

6. To enable **hg-public.languagedepot.local**
```
sudo ln -s "$(pwd)/contrib" /var/www/languagedepot.org_hg-public/contrib
sudo ln -s "$(pwd)/cgi-bin" /var/www/languagedepot.org_hg-public/cgi-bin
sudo a2ensite languagedepot_org_hg-public.conf
sudo service apache2 reload
```

7. To add a **VCS** Mercurial repository with `<Identifier>` for testing
    * The installed Mercurial may be too new to work with Chorus so use the Chorus `Mercurial.zip`.
```
cd /var/vcs/public
sudo wget https://github.com/sillsdev/chorus/blob/master/lib/common/Mercurial-x86_64.zip?raw=true -O Mercurial.zip
sudo unzip Mercurial.zip
sudo chmod +x Mercurial/hg
sudo Mercurial/hg clone http://hg-public.qa.languagedepot.org/<Identifier>
sudo chown www-data:www-data -R /var/vcs/public
```

## Testing ##

### PHP API Unit Tests ###

Unit testing currently uses [PHPUnit](https://phpunit.de/) which was already installed by composer.

#### Integrating PHPUnit with PhpStorm (optional) ####

**File** -> **Settings** -> **Languages & Frameworks** -> **PHP** -> **PHPUnit**

Under PHPUnit Library, select `Use Composer autoloader` option
For `Path to script` browse to `web-languageforge-api/src/vendor/autoload.php`

Under Test Runner
Select *Default configuration file* and browse to `web-languageforge-api/test/php/phpunit.xml`

Select *Default bootstrap file* and browse to `web-languageforge-api/test/php/TestConfig.php`

#### Running the tests ####
In a terminal, `gulp test-php`.  This will setup a test environment and run the tests.

To test with debug info `gulp test-php --debug true`

To test with code coverage `gulp test-php --coverage true`.  
This will generate test coverage report in `test/CodeCoverage/php/index.html`. 

To run tests in PhpStorm, browse to the project view, right-click `test/php` folder and select `Run 'php'`.

## Updating dependencies ##

Occasionally developers need to update composer, bower or npm.  If something isn't working after a recent code change, try updating the dependencies:

#### Update npm packages ####

In the **root** folder: `npm install`

#### Update bower ####

In the **src** folder: `bower install`

#### Update composer ####

In the **src** folder: `composer install`

## Language Depot Stats ##

Browse to [admin.languagedepot.org](http://admin.languagedepot.org/) to see reports.
