#!/bin/bash

mysql -uroot -p << END_SQL
	source sql/schema.sql;
	source sql/migrateV1.1_to_V1.2.sql
END_SQL
[ $? == 0 ] && echo 'Migration was succesful'
[ $? != 0 ] && echo 'Migration failed'
