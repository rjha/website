x=`date +%d%m%Y`
mysqldump  --complete-insert --add-drop-table   --triggers  --routines -u root -p fsdb1 > fsdb1.dev.sql


