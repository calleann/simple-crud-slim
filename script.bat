@ECHO off

 set now=%date%
 set datef=%date:~-4%-%date:~3,2%-%date:~0,2%
 echo 'today is : %now%'
 echo %datef%

 C:/xampp/mysql/bin/mysqldump --opt -h localhost -u root app > C:/xampp/htdocs/Slim/backup/backup_test_%datef%.sql
 echo 'yay'
