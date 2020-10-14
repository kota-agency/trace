
#!/bin/sh

cd ../

B2_BUCKET_NAME="d4r"
FORGE_SITE_NAME="www.design4retail.co.uk"

SQL_FILE=database_backup.sql
UPLOADS_FILE=uploads_backups.tar.gz
UPLOADS_DIR=/home/forge/$FORGE_SITE_NAME/web/app/

# Backup database
wp db export $SQL_FILE --add-drop-table --quiet --url=http://blah.com

# Compress the database dump file
gzip $SQL_FILE

# Upload db export to B2
/usr/local/bin/b2 upload_file $B2_BUCKET_NAME $SQL_FILE.gz $SQL_FILE.gz

# Remove db export file from server
rm $SQL_FILE.gz

# Move to uploads directory
cd $UPLOADS_DIR

# Compress upload directory
tar -zcf $UPLOADS_FILE uploads

# Upload compressed uploads to B2
/usr/local/bin/b2 upload_file $B2_BUCKET_NAME $UPLOADS_FILE $UPLOADS_FILE

# Remove compress uploads file from server
rm $UPLOADS_FILE
