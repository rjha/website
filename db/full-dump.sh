x=`date +%d%m%Y`
mysqldump  --complete-insert --add-drop-table   --triggers  --routines -u root -p wbdb1 > wbdb1.dev.sql


