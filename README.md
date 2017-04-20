web-languagedepot-stats
=======================

## Manually install and configure mysql

```
sudo apt-get install mysql-server
```
When prompted, set the default password to **password** 

Create the users, databases and grant all privileges to local user.  Replace *[USER]* with your username
```
mysql -u root -p
create database languagedepot;
create database languagedepotpvt;
create user '[USER]'@'localhost';
grant all on *.* to '[USER]'@'localhost';
create user 'test'@'localhhost' identified by 'test';
grant all on *.* to 'test'@'localhost';
quit
```

Restore from command prompt
```
mysql languagedepot < languagedepot.sql
mysql languagedepotpvt < languagedepotpvt.sql
```
