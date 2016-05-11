#!/bin/sh

. ./var.txt

rm -f $CRON

echo "*/1 * * * * /var/www/html/pbx-monitor/bin/rrd_collector.sh -d db/rrd_db" >> $CRON
echo "17 1 * * * /usr/local/bin/update_www.sh" >> $CRON