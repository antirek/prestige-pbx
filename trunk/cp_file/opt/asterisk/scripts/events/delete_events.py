#!/bin/env python

import sys,datetime,MySQLdb

db = MySQLdb.connect(host="localhost",
                     user="uuuuuuuuuu",
                     passwd="pppppppppp",
                     db="bbbbbbbbbb")

cursor = db.cursor()
cdate = datetime.datetime.now()
ymd  = str(cdate.year) + '-' + str(cdate.month) + '-' + str(cdate.day)
cmd = "DELETE FROM events WHERE timestamp <  '" + ymd + "'"
cursor.execute(cmd)
sys.exit()
