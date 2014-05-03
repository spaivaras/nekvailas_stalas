#!/bin/bash

parse_yaml() {
   local prefix=$2
   local s='[[:space:]]*' w='[a-zA-Z0-9_]*' fs=$(echo @|tr @ '\034')
   sed -ne "s|^\($s\)\($w\)$s:$s\"\(.*\)\"$s\$|\1$fs\2$fs\3|p" \
        -e "s|^\($s\)\($w\)$s:$s\(.*\)$s\$|\1$fs\2$fs\3|p"  $1 |
   awk -F$fs '{
      indent = length($1)/2;
      vname[indent] = $2;
      for (i in vname) {if (i > indent) {delete vname[i]}}
      if (length($3) > 0) {
         vn=""; for (i=0; i<indent; i++) {vn=(vn)(vname[i])("_")}
         printf("%s%s%s=\"%s\"\n", "'$prefix'",vn, $2, $3);
      }
   }'
}

# read config file
#CONFIG='/path/to/config.yml'
echo "change CONFIG. remove me."
exit 0
eval $(parse_yaml $CONFIG "config_")
#echo

#BACKUP_DIR='/path/to/backup/dir/'
echo "change BACKUP_DIR. remove me."
exit 0
TIMESTAMP=`date +%Y%m%d%H%M%S`
FILE=$BACKUP_DIR'wonderwall_'$TIMESTAMP'.sql'
FILE_GZ=$FILE'.gz'

MYSQL_HOST=$config_db_host
MYSQL_USERNAME=$config_db_user
MYSQL_PASSWORD=$config_db_password
MYSQL_DATABASE=$config_db_dbname
MYSQL_TABLE='kickertable'
MYSQL_TRUNCATE_CMD='DELETE FROM '$MYSQL_TABLE';'

# backup to file
mysqldump -h $MYSQL_HOST -u $MYSQL_USERNAME -p$MYSQL_PASSWORD --no-create-info --skip-comments $MYSQL_DATABASE $MYSQL_TABLE | gzip > $FILE_GZ
# truncate table
mysql -h$MYSQL_HOST -u$MYSQL_USERNAME -p$MYSQL_PASSWORD -D$MYSQL_DATABASE<<EOFMYSQL
$MYSQL_TRUNCATE_CMD
EOFMYSQL