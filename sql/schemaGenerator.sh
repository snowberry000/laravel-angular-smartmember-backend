#!/bin/sh

mysql="mysql -h192.168.10.10 -e "
$mysql "use smartmembers; show tables " |
	awk '{print $1}' |
	while read line; do
		
		fields=`$mysql "desc smartmembers.$line" | grep -v Field |sort | awk '{printf("%s, ", $1)}'`
	  	fields=`echo $fields | rev | cut -d',' -f2- |rev`
	  	
		code=`$mysql "desc smartmember.smartm_$line" &> /dev/null; echo $?`
		if [ $code == 0 ]; then
			echo "INSERT INTO smartmembers.$line ("
			echo " $fields )"
			echo "  SELECT " 
	  	
	  		fields=`$mysql "desc smartmember.smartm_$line" | grep -v Field |sort | awk '{printf("%s, ", $1)}'`
	  		fields=`echo $fields | sed -e s/old_id\,//`
	  		fields=`echo $fields | sed -e s/date/from_unixtime\(date\)/`
	  		fields=`echo $fields | rev | cut -d',' -f2- |rev`

	  		echo $fields

	 		echo " FROM smartmember.$line"
	 		echo ""	
	 	fi		
	done
	
# $mysql "use smartmember; show tables" | 
# 	awk '{print $1}' |
# 	while read line; do 
# 	 	echo "SELECT " 
	  	
# 	  	fields=`$mysql "desc smartmember.$line" | grep -v Field |sort | awk '{printf("%s, ", $1)}'`
# 	  	fields=`echo $fields | sed -e s/old_id\,//`
# 	  	fields=`echo $fields | sed -e s/date/from_unixtime\(date\)/`
# 	  	fields=`echo $fields | rev | cut -d',' -f2- |rev`

# 	  	echo $fields

# 	 	echo " FROM smartmember.$line"
# 	 	echo ""
#  	 done

