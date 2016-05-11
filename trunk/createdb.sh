#!/bin/sh

. ./var.txt


DATABASE_NAME='asterisk'; 
DB_TABLE_NAME='codes'

/usr/bin/mysqladmin -u root password $SQLPWD

mv -f /etc/my.cnf /etc/my.cnf_orign 2> /dev/null
cp -f $DIR_CP_FILE/my.cnf /etc/my.cnf

service mysqld stop
killall mysqld

mysqld_safe --skip-grant-tables &

echo "update user set Password=PASSWORD('$SQLPWD') where User='root';flush privileges;" | mysql -u root mysql

service mysqld stop
killall mysqld

sleep 5

service mysqld restart

echo "\nChange root password on $SQLPWD - complite. Continion press ENTER";
read


echo "create database if not exists asterisk;" | mysql -u root -p$SQLPWD
echo "GRANT ALL PRIVILEGES ON asterisk.* TO '$ASTERISKUSER'@'localhost' IDENTIFIED BY '$SQLPWDASTERISK' WITH GRANT OPTION;" | mysql -u root  -p$SQLPWD

mysql -u $ASTERISKUSER asterisk -p$SQLPWDASTERISK < $DIR/dump.sql


DOWNFILE='http://www.rossvyaz.ru/docs/articles/ABC-3x.html'; 
wget -c -q -O - $DOWNFILE | grep "^<tr>" | sed -e 's/<\/td>//g' -e 's/<tr>//g' -e 's/<\/tr>//g' -e 's/[\t]//g' -e 's/^<td>//g' -e 's/<td>/;/g' -e 's/|/-/g' | iconv -c -f WINDOWS-1251 -t UTF8 > $TMPDIR/$DB_TABLE_NAME
mysqlimport --user=$ASTERISKUSER --password=$SQLPWDASTERISK --columns "code_abcdef,code_from,code_to,code_volume,operator,region" --local --fields-terminated-by=";" --lines-terminated-by="\\n" $DATABASE_NAME $TMPDIR/$DB_TABLE_NAME

DOWNFILE='http://www.rossvyaz.ru/docs/articles/ABC-4x.html'; 
wget -c -q -O - $DOWNFILE | grep "^<tr>" | sed -e 's/<\/td>//g' -e 's/<tr>//g' -e 's/<\/tr>//g' -e 's/[\t]//g' -e 's/^<td>//g' -e 's/<td>/;/g' -e 's/|/-/g' | iconv -c -f WINDOWS-1251 -t UTF8 > $TMPDIR/$DB_TABLE_NAME
mysqlimport --user=$ASTERISKUSER --password=$SQLPWDASTERISK --columns "code_abcdef,code_from,code_to,code_volume,operator,region" --local --fields-terminated-by=";" --lines-terminated-by="\\n" $DATABASE_NAME $TMPDIR/$DB_TABLE_NAME

DOWNFILE='http://www.rossvyaz.ru/docs/articles/ABC-8x.html'; 
wget -c -q -O - $DOWNFILE | grep "^<tr>" | sed -e 's/<\/td>//g' -e 's/<tr>//g' -e 's/<\/tr>//g' -e 's/[\t]//g' -e 's/^<td>//g' -e 's/<td>/;/g' -e 's/|/-/g' | iconv -c -f WINDOWS-1251 -t UTF8 > $TMPDIR/$DB_TABLE_NAME
mysqlimport --user=$ASTERISKUSER --password=$SQLPWDASTERISK --columns "code_abcdef,code_from,code_to,code_volume,operator,region" --local --fields-terminated-by=";" --lines-terminated-by="\\n" $DATABASE_NAME $TMPDIR/$DB_TABLE_NAME

DOWNFILE='http://www.rossvyaz.ru/docs/num/DEF-9x.html'; 
wget -c -q -O - $DOWNFILE | grep "^<tr>" | sed -e 's/<\/td>//g' -e 's/<tr>//g' -e 's/<\/tr>//g' -e 's/[\t]//g' -e 's/^<td>//g' -e 's/<td>/;/g' | iconv -c -f WINDOWS-1251 -t UTF8 > $TMPDIR/$DB_TABLE_NAME
mysqlimport --user=$ASTERISKUSER --password=$SQLPWDASTERISK --columns "code_abcdef,code_from,code_to,code_volume,operator,region" --local --fields-terminated-by=";" --lines-terminated-by="\\n" $DATABASE_NAME $TMPDIR/$DB_TABLE_NAME

