#!/bin/sh
. ./var.txt

#_#cp -f $DIR_CP_FILE/yum.repos.d/* /etc/yum.repos.d/
#_#ln -sf /usr/share/zoneinfo/Europe/Moscow /etc/localtime

#_#yum -y install openssl-devel libidn-devel ncurses-libs ncurses-devel ncurses libssh2-devel libssh-devel make gcc mysql-devel gtk+-devel gtk2-devel gtk2 tcpdump nc libdnet-devel libdnet-progs libdnet libtool-ltdl libtool-ltdl-devel libtool
#_#echo "end part 1, press ENTER"
#_#read
#_#yum -y install libpcap-devel libpcap gcc gcc-c++ bison-devel bison-runtime bison flex nmap wipe php perl-CPAN wireshark perl-suidperl mysql mysql-devel  mysql-server php-mysql rrdtool urw-fonts php-gd sox sox-devel MySQL-python atop htop php-domxml ncftp
#_#echo "end part 2, press ENTER"
#_#read
#_#yum -y install asterisk asterisk-addons-mysql asterisk-voicemail asterisk-configs asterisk-sounds-moh-opsound-wav 
#_#echo "end part 5, press ENTER"
#_#read

#_#mv -f /etc/yum.conf /etc/yum.conf_old 2> /dev/null
#_#cp -f $DIR_CP_FILE/yum.conf  /etc/yum.conf

#_#cp -f $DIR_CP_FILE/mc/hotlist  /root/.mc/hotlist
#_#wget http://www.asteriskforum.ru/files/asterisk_103.syntax -O /usr/share/mc/syntax/asterisk.syntax
#_#cp -f $DIR_CP_FILE/Syntax  /etc/mc/Syntax

#_#cp -f $DIR_CP_FILE/codec* /usr/lib/asterisk/modules/

#_#mkdir -p /var/spool/asterisk/monitor/
#_#mkdir -p  /var/lib/asterisk/sounds/avtoobzvon/records
#_#chmod 777 /var/lib/asterisk/sounds/avtoobzvon
#_#chmod 777 /var/lib/asterisk/sounds/avtoobzvon/records

#_#chown -R asterisk.asterisk /var/spool/asterisk/monitor/
#_#chmod 777 /var/spool/asterisk/monitor/

#_#mkdir -p /var/lib/asterisk/sounds/ru/
#_#cd /var/lib/asterisk/sounds/ru/

#_#wget -q --no-check-certificate https://github.com/pbxware/asterisk-sounds-additional/tarball/master -O- \ |  tar xzv  --strip-components 1 -C /var/lib/asterisk/sounds/ru/
#_#wget -q --no-check-certificate https://github.com/pbxware/asterisk-sounds/tarball/master -O- \ |  tar xzv  --strip-components 1 -C /var/lib/asterisk/sounds/ru/
#_#cp -f $DIR_CP_FILE/voice* /var/lib/asterisk/sounds/

#_#mkdir -p /var/lib/asterisk/moh/connect
#_#cp -f /var/lib/asterisk/sounds/ru/priv-introsaved.wav /var/lib/asterisk/moh/connect
#_#cp -f /var/lib/asterisk/sounds/ru/silence/3.wav /var/lib/asterisk/moh/connect


#_#echo "######################################## install cpan DBI ############################################################"
#_#read
#_#perl -MCPAN -e 'install DBI'
#_#echo "######################################## install cpan AMI ############################################################"
#_#read
#_#perl -MCPAN -e 'install Asterisk::AMI'
#_#echo "######################################## install cpan AGI ############################################################"
#_#read
#_#perl -MCPAN -e 'install Asterisk::AGI'
#_#echo "######################################## install cpan JSON ###########################################################"
#_#read
#_#perl -MCPAN -e 'install JSON'

#_#cd $DIR
#_#cp -f  $DIR_CP_FILE/usr/local/bin/* /usr/local/bin/

#_#echo "######################################## databases ############################################################"
#_#echo "Start DB"
#_#read
#_#$DIR/createdb.sh
#_#echo "######################################## end databases ############################################################"

echo "######################################## install config files ############################################################"
echo "Press ENTER"
read


echo "alias sql=\"mysql -u root asterisk -p$SQLPWD\"" >> /root/.bash_profile
mkdir /etc/asterisk_orign
cp -fR  /etc/asterisk/* /etc/asterisk_orign
cp -f  $DIR_CP_FILE/agi-bin/* /var/lib/asterisk/agi-bin
mv -f /etc/php.ini /etc/php.ini_orign
cp -f $DIR_CP_FILE/php.ini /etc/php.ini  2> /dev/null

mv -f /etc/httpd/conf/httpd.conf  /etc/httpd/conf/httpd.conf_orign 
cp -f $DIR_CP_FILE/httpd.conf /etc/httpd/conf/httpd.conf  2> /dev/null
cp -fR  $DIR_CP_FILE/html/* /var/www/html
cp -fR  $DIR_CP_FILE/html/.htaccess /var/www/html/.htaccess
cp -fR  $DIR_CP_FILE/cgi-bin/* /var/www/cgi-bin
chmod 777 /var/www/html/pbx-monitor/charts/




echo "" > /var/www/html/.htpasswd
/usr/bin/htpasswd -cb /var/www/html/.htpasswd $WWWUSER $WWWPWD
service httpd restart

sed -i "s/asteriskmysql=.*/asteriskmysql=$ASTERISKUSER/" /var/lib/asterisk/agi-bin/var.txt
sed -i "s/asteriskpasswd=.*/asteriskpasswd=$SQLPWDASTERISK/" /var/lib/asterisk/agi-bin/var.txt
sed -i "s/asteriskdbname=.*/asteriskdbname=$ASTERISKDB/" /var/lib/asterisk/agi-bin/var.txt


echo "######################## start change  html dir #######################################"
sed -i "s/\$ASTERISKUSER=;/\$ASTERISKUSER=\"$ASTERISKUSER\";/" /var/www/html/var.php
sed -i "s/\$SQLPWDASTERISK=;/\$SQLPWDASTERISK=\"$SQLPWDASTERISK\";/" /var/www/html/var.php
sed -i "s/\$ASTERISKDB=;/\$ASTERISKDB=\"$ASTERISKDB\";/" /var/www/html/var.php
sed -i "s/\$ASTERISKCDR=;/\$ASTERISKCDR=\"$ASTERISKCDR\";/" /var/www/html/var.php
sed -i "s/\$SQLSERVER=;/\$SQLSERVER=\"$SQLSERVER\";/" /var/www/html/var.php
sed -i "s/\$DOMAINNAMEVOICEMAIL=;/\$DOMAINNAMEVOICEMAIL=\"$DOMAINNAMEVOICEMAIL\";/" /var/www/html/var.php
sed -i "s/\$ADMINMAIL=;/\$ADMINMAIL=\"$ADMINMAIL\";/" /var/www/html/var.php
sed -i "s/\$AMIUSER=;/\$AMIUSER=\"$AMIUSER\";/" /var/www/html/var.php
sed -i "s/\$AMIPWD=;/\$AMIPWD=\"$AMIPWD\";/" /var/www/html/var.php
sed -i "s/\$AMIUSERPHONEBOOK=;/\$AMIUSERPHONEBOOK=\"$AMIUSERPHONEBOOK\";/" /var/www/html/var.php
sed -i "s/\$AMIPWDPHONEBOOK=;/\$AMIPWDPHONEBOOK=\"$AMIPWDPHONEBOOK\";/" /var/www/html/var.php
sed -i "s/\$LIMITONPAGE=;/\$LIMITONPAGE=\"$LIMITONPAGE\";/" /var/www/html/var.php
sed -i "s/\$PHONEBOOKPWD=;/\$PHONEBOOKPWD=\"$PHONEBOOKPWD\";/" /var/www/html/var.php

echo "######################## end  change  html dir #######################################"

mkdir -p /usr/X11R6/lib/X11/fonts/TTF/
cp -f $DIR_CP_FILE/fonts/* /usr/X11R6/lib/X11/fonts/TTF/


################################################################################################################################################################################################
################################################################################################################################################################################################

mkdir -p /opt/asterisk/scripts/events
cp -fR  $DIR_CP_FILE/opt/asterisk/scripts/events/* /opt/asterisk/scripts/events

sed -i "s/USERNAME       = 'uuuuuuuuuu'/USERNAME       = '$AMIUSER'/" /opt/asterisk/scripts/events/ProxyMan.py
sed -i "s/SECRET         = 'pppppppppp'/SECRET         = '$AMIPWD'/" /opt/asterisk/scripts/events/ProxyMan.py
sed -i "s/SQLuser        = 'uuuuuuuuuu'/SQLuser        = '$ASTERISKUSER'/" /opt/asterisk/scripts/events/ProxyMan.py
sed -i "s/SQLpass        = 'pppppppppp'/SQLpass        = '$SQLPWDASTERISK'/" /opt/asterisk/scripts/events/ProxyMan.py
sed -i "s/SQLdb          = 'bbbbbbbbbb'/SQLdb          = '$ASTERISKDB'/" /opt/asterisk/scripts/events/ProxyMan.py


sed -i "s/user=\"uuuuuuuuuu\"/user=\"$ASTERISKUSER\"/" /opt/asterisk/scripts/events/delete_events.py 
sed -i "s/passwd=\"pppppppppp\"/passwd=\"$SQLPWDASTERISK\"/" /opt/asterisk/scripts/events/delete_events.py 
sed -i "s/db=\"bbbbbbbbbb\"/db=\"$ASTERISKDB\"/" /opt/asterisk/scripts/events/delete_events.py 



################################################################################################################################################################################################
################################################################################################################################################################################################
$DIR/crontab.sh
/usr/local/bin/update_www.sh


################################################################################################################################################################################################
if [ "$MULTIFON" == "1" ]; then
wget "https://sm.megafon.ru/sm/client/routing/set?login=$MULTIFONNUMBER@multifon.ru&password=$MULTIFONPWD&routing=1" --output-document=$DIR/res_multifon.txt
cat $DIR/res_multifon.txt
wget "https://sm.megafon.ru/sm/client/routing?login=$MULTIFONNUMBER@multifon.ru&password=$MULTIFONPWD"
fi
################################################################################################################################################################################################
################################################################################################################################################################################################
################################################################################################################################################################################################
################################################################################################################################################################################################
################################################################################################################################################################################################
################################################################################################################################################################################################

rm -f /etc/asterisk/sip*
rm -f /etc/asterisk/extensions*
rm -f /etc/asterisk/cdr_mysql.conf
rm -f /etc/asterisk/voicemail.conf
rm -f /etc/asterisk/manager.conf
rm -f /etc/asterisk/features.conf
rm -f /etc/asterisk/logger.conf 
rm -f /etc/asterisk/musiconhold.conf

echo "######################################################### mysql ##########################################################"
echo "[global]
hostname=127.0.0.1
dbname=asterisk
table=cdr
password=$SQLPWDASTERISK
user=$ASTERISKUSER
port=3306
sock=/tmp/mysql.sock
charset=utf8
" >> /etc/asterisk/cdr_mysql.conf


echo "######################################################### sip ##########################################################"
echo ";versions: $VERSION
[general]
#include sip_general.conf
#include sip_reg.conf
" >> /etc/asterisk/sip.conf



if [ "$MULTIFON" == "1" ]; then

echo "
#include sip_mf_reg.conf
#include sip_mf_peers.conf
#include sip_trunk.conf
" >> /etc/asterisk/sip.conf

echo ";version $VERSION
register => $MULTIFONNUMBER:$MULTIFONPWD@sbc.multifon.ru/$MULTIFONNUMBER
" >> /etc/asterisk/sip_mf_reg.conf

echo ";version $VERSION
[mf](!)
type=friend
qualify=yes
nat=no
insecure=port,invite
host=sbc.multifon.ru
fromdomain=multifon.ru
dtmfmode=inband
context=multifon-in
canreinvite=no
disallow=all
allow=ulaw
allow=alaw

[$MULTIFONNUMBER](mf)
username=$MULTIFONNUMBER
secret=$MULTIFONPWD
" >> /etc/asterisk/sip_mf_peers.conf 


fi


echo "
#include sip_peers.conf
" >> /etc/asterisk/sip.conf

echo ";versions: $VERSION
LANGUAGE=ru

bindport=5433
;externip=
;localnet=
Language=ru
qualyfiy=yes
nat=yes
canreinvite=no
useragent=Planet
vmexten=*97
disallow=all
allow=ulaw
allow=alaw
context=nocontext
callerid=RusUsers
tos_sip=cs3
tos_audio=ef
tos_video=af41
alwaysauthreject=yes
srvlookup = no

allowsubscribe=yes
notifyringing=yes
limitonpeer=yes
notifyhold=yes
subscribecontext=lab
callcounter=yes
buggymwi=yes
counteronpeer=ye

" >> /etc/asterisk/sip_general.conf


echo ";versions: $VERSION
" >> /etc/asterisk/sip_reg.conf

echo ";versions: $VERSION
" >> /etc/asterisk/sip_trunk.conf


echo ";versions: $VERSION
[peers](!)
host=dynamic
qualify=yes
type=friend
nat=yes
insecure=invite,port
canreinvite=no
disallow=all
allow=ulaw
allow=alaw
allow=g729
context=office
hasvoicemail=yes
callgroup=1
pickupgroup=1
" >> /etc/asterisk/sip_peers.conf

echo "######################################################### voicemail ##########################################################"
echo ";versions: $VERSION
[general]
emaildateformat=%A, %B %d, %Y at %r
pagerdateformat=%A, %B %d, %Y at %r
[zonemessages]
eastern=America/New_York|'vm-received' Q 'digits/at' IMp
central=America/Chicago|'vm-received' Q 'digits/at' IMp
central24=America/Chicago|'vm-received' q 'digits/at' H N 'hours'
military=Zulu|'vm-received' q 'digits/at' H N 'hours' 'phonetic/z_p'
european=Europe/Copenhagen|'vm-received' a d b 'digits/at' HM
format=wav49|wav
attach=yes
pbxskip=yes
serveremail=vm@$DOMAINNAMEVOICEMAIL
fromstring=VoiceMail $DOMAINNAMEVOICEMAIL
maxsilence=3
silencethreshold=128
skipms=3000
review=yes
operator=yes
nextaftercmd=yes
maxsecs=60
minsecs=4
emailbody=\${VM_NAME},
\n\nYou have a new mail \${VM_MAILBOX}:
\n\n\tFrom:\t\${VM_CALLERID}\n
\tDurations:\t \${VM_DUR} sec.\n
\tDate:\t\${VM_DATE}\n\n
For access to you mailbox call to *98 from you IP phone.\n
or visit http://$DOMAINNAMEVOICEMAIL/recordings/index.php \n
[default]
9999 => 3629,Example Mailbox,root@localhost
" >> /etc/asterisk/voicemail.conf



for (( c=$STARTPEER; $c<$STOPPEER; c=$c+1 ));
do
rnd=`cat /dev/urandom |tr -dc A-Za-z0-9| (head -c $1 > /dev/null 2>&1 || head -c 8)`
let peer=$c

echo "[$peer](peers)
username=$peer
secret=$rnd
" >> /etc/asterisk/sip_peers.conf

rnd2=`cat /dev/urandom |tr -dc 0-9| (head -c $1 > /dev/null 2>&1 || head -c 3)`

echo "$peer => $rnd2,$peer,$peer@$DOMAINNAMEVOICEMAIL,,attach=yes" >> /etc/asterisk/voicemail.conf

echo "insert into pbook set calld=\"$peer\", name=\"user $peer\", bemerkung=\"descriptions $peer\";" | mysql -u $ASTERISKUSER $ASTERISKDB -p$SQLPWDASTERISK

done

echo "insert into pbook set calld=\"78123090607\", name=\"SPB ATS-Prestige\", bemerkung=\" Техническая поддержка IP АТС\";" | mysql -u $ASTERISKUSER $ASTERISKDB -p$SQLPWDASTERISK
echo "insert into pbook set calld=\"79019034449\", name=\"MSK ATS-Prestige\", bemerkung=\" Техническая поддержка IP АТС\";" | mysql -u $ASTERISKUSER $ASTERISKDB -p$SQLPWDASTERISK

echo "######################################################### extensions ##########################################################"
echo "; version $VERSION\
;;;; t - ответишвий переводит
;;;; T - звонящий переводит
[general]
static=yes
writeprotect=no
clearglobalvars=no

[globals]
#include extensions_peers.conf
#include extensions_phonebook.conf
#include extensions_in.conf
#include extensions_xfer.conf
#include extensions_other.conf

SIP_GROUP1=SIP/$STARTPEER
SIP_GROUP2=SIP/$STARTPEER&SIP/$STOPPEER

DYNAMIC_FEATURES=VUp#VDown 
Vol=5

" >> /etc/asterisk/extensions.conf


if [ "$MULTIFON" == "1" ]; then
echo "
#include extensions_mf.conf
" >> /etc/asterisk/extensions.conf

echo ";version $VERSION
[multifon-in]
exten => $MULTIFONNUMBER,1,NoOp(\" Call from number $MULTIFONNUMBER \")
same => n,Progress()
same => n,AGI(bl.agi)
same => n,AGI(record.agi)
same => n,Dial(\${SIP_GROUP1},,t)
same => n,Hangup

exten => s,1,Hangup
exten => _X.,1,Hangup
" >> /etc/asterisk/extensions_mf.conf

fi

echo ";version $VERSION
[office]
include => park
include => chanspy
include => vm
include => conf_bridge
include => auto_call
include => record
include => call-local
include => call-to-world


[call-local]
exten => _XXX,1,NoOp(internal phones \${EXTEN} status - \${SIPPEER(\${EXTEN},status)})
same => n,Set(__DYNAMIC_FEATURES=pitch0#pitch1#pitch2#pitch3#pitch4#pitch5#pitch6#pitch7#pitch8#pitch9)
same => n,Set(local_call=1)
same => n,AGI(pbook_sql.agi)
same => n,Set(NUMBER=\${EXTEN})
same => n,GotoIf(\$[\"\${SIPPEER(\${EXTEN},status)}\" = \"\"]?number_exists)
same => n,GotoIf(\$[\"\${SIPPEER(\${EXTEN},status):0:2}\" = \"UN\"]?number_not_connected)
same => n,AGI(rec.agi)
same => n,set(CDR(Userfield)=\${EXTEN})
same => n,Dial(SIP/\${EXTEN},300,mtT)
same => n,Goto(s-\${DIALSTATUS},1)
same => n,Hangup()
same => n(number_not_connected),Voicemail(\${NUMBER},u)
same => n,Hangup()
same => n(number_exists),Playback(pbx-invalid)
same => n,Hangup()

exten => s-NOANSWER,1,Voicemail(\${NUMBER},u)
exten => s-CHANUNAVAIL,1,Voicemail(\${NUMBER},b)
exten => s-BUSY,1,Voicemail(\${NUMBER},b)
exten => _s-.,1,Voicemail(\${NUMBER},u)

[vm]
exten => *97,1,Log(NOTICE, Dialing from \${CALLERID(all)} to VoiceMail without password)
same => n,VoiceMailMain(\${CALLERID(num)}@default,s)
same => n,Hangup

exten => *98,1,Log(NOTICE,  Dialing out from \${CALLERID(all)} to VoiceMail with password)
same => n,VoiceMailMain(@default)
same => n,Hangup

[call-to-world]
exten => _9X.,1,AGI(rec.agi)
same => n,Set(__DYNAMIC_FEATURES=pitch0#pitch1#pitch2#pitch3#pitch4#pitch5#pitch6#pitch7#pitch8#pitch9)
same => n,Dial(SIP/\${EXTEN:1}@$TRUNK1)




" >> /etc/asterisk/extensions_peers.conf

echo ";version $VERSION
[pbook]
exten => _000XXX,1,Set(local_call=1)
same => n,Set(local_call=1)
same => n,AGI(pbook_sql.agi)
same => n,Set(__DYNAMIC_FEATURES=pitch0#pitch1#pitch2#pitch3#pitch4#pitch5#pitch6#pitch7#pitch8#pitch9)
same => n,set(CDR(Userfield)=\${EXTEN:3})
same => n,AGI(rec.agi)
same => n,Dial(SIP/\${EXTEN:3},,tTkK)

exten => _000X.,1,NoOp(start call from Phone Book)
same => n,Set(__DYNAMIC_FEATURES=pitch0#pitch1#pitch2#pitch3#pitch4#pitch5#pitch6#pitch7#pitch8#pitch9)
same => n,AGI(rec.agi)
same => n,Dial(SIP/\${EXTEN:3}@$TRUNK1,,tk)

include => park
include => conf_bridge
include => call-local


" >> /etc/asterisk/extensions_phonebook.conf

echo ";version $VERSION
[in]
include => park
include => call-local
include => call-from-world

[call-from-world]
exten => s,1,NoOp(\"Incoming call\")
same => n,set(CDR(Userfield)=)
same => n,GoTo(_X.,1)

exten => _X.,1,NoOp(\"Call from Trunk\")
same => n,AGI(bl.agi)
same => n,AGI(pbook_sql.agi)
same => n,AGI(rec.agi)
same => n,Dial(\${SIP_GROUP1},15,mtk)
same => n,Dial(\${SIP_GROUP2},30,mtk)
same => n,Voicemail($STARTPEER)


[nocontext]
exten => s,1,GoTo(in,_X.,1)
exten=> _X.,1,GoTo(in,_X.,1)

" >> /etc/asterisk/extensions_in.conf

echo ";version $VERSION
[blindxfer] 
include => conf_xfer
include => all_xfer

[all_xfer]
exten => _X.,1,NOOP(\${BLINDTRANSFER}) 
same => n,set(LANGUAGE=ru}) 
same => n,dial(SIP/\${EXTEN}@$TRUNK1,10,m) 
same => n,Gotoif(\$[\"\${DIALSTATUS}\" = \"ANSWER\"]?hangup:callback) 
same => n(callback),Dial(SIP/\${BLINDTRANSFER:4:3},,tm)
same => n(hangup),hangup() 

exten => _XXX,1,NOOP(\${BLINDTRANSFER}) 
same => n,set(LANGUAGE=ru}) 
same => n,dial(SIP/\${EXTEN},10,m) 
same => n,Gotoif(\$[\"\${DIALSTATUS}\" = \"ANSWER\"]?hangup:callback) 
same => n(callback),Dial(SIP/\${BLINDTRANSFER:4:3},,tm)
same => n(hangup),hangup()


" >> /etc/asterisk/extensions_xfer.conf

echo ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;; http://wapo-spb.livejournal.com/24758.html ;;;;;;;;;;;;;;;;;;;;;;;;;
[macro-conf]

exten => s,1,NoOp(---------------------ALL INFO about CHANNELS!!!!---------------)
same => n,NoOp(BRIDGEPEER: \${BRIDGEPEER} for number: \${CALLERID(num)} )
same => n,ChannelRedirect(\${BRIDGEPEER},office,888\${CALLERID(num)},1)
same => n,NoOp(------------------ALL INFO about CHANNELS!!!!-----------------)

[conf_bridge]
exten => *0,1,NoOp(Conf)
same => n,ConfBridge(\${CALLERID(num)},default_bridge,,sample_admin_menu)
same => n,Hangup

include => conf_xfer


[conf_xfer]
exten => _888XXX,1,NoOp(Transfer to conferenc: \${EXTEN:3})
same => n,ConfBridge(\${EXTEN:3},default_bridge,,sample_user_menu)

;;;;;;;;;;;;;;;;;; http://wapo-spb.livejournal.com/24758.html ;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[chanspy]
exten => 710,1,ChanSpy(SIP,qb)
exten => 711,1,ChanSpy(SIP,qbw)

[park]
exten => _70[0-9],1,ParkedCall(\${EXTEN})

[record]
exten => 720,1,Set(FILE_NAME=\${STRFTIME(\${EPOCH},,%Y.%m.%d)}_\${STRFTIME(\${EPOCH},,%H.%M.%S)})
same => n,Record(avtoobzvon/\${FILE_NAME}.wav)
same => n,Playback(avtoobzvon/\${FILE_NAME})
same => n,Hangup

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;; auto call ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[auto_call]
exten => 00,1,NoOp(Start auto call)
same => n,AGI(auto_call.agi)
same => n,Playback(beep&beep&beep)
same => n,Hangup

[auto_call_start]
exten => _X.,1,Dial(SIP/\${EXTEN}@$TRUNK1)

[auto_call_bridge]
exten => s,1,Dial(SIP/\${src},,A(beep)m(connect))


;;;;;;;;;;;;;;;; auto call ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[tmp]


exten => 1234,1,Answer()
same => n,agi(googletts.agi,\"Это текст на русском\",ru)
  
exten => 1235,1,Answer()
same => n,agi(googletts.agi,\"Скажите на русском.\",ru)
same => n(record),agi(speech-recog.agi,ru-RU)
same => n,Verbose(1,Script returned: \${status} , \${id} , \${confidence} , \${utterance})
same => n,GotoIf(\$[\"\${status}\" = \"0\"]?success:fail)
same => n(success),GotoIf(\$[\"\${confidence}\" > \"0.8\"]?playback:retry)
  
same => n(playback),agi(googletts.agi,\"Вы сказали\",ru)
same => n,agi(googletts.agi,\"\${utterance}\",ru)
same => n,agi(googletranslate.agi,\"\${utterance}\",en)
same => n,goto(end)
  
same => n(retry),agi(googletts.agi,\"Можете повторить?\",ru)
same => n,goto(record)
  
same => n(fail),agi(googletts.agi,\"Не получены данные.\",ru)
same => n(end),Hangup()
  
exten => 1236,1,Set(MYTEXT=\"This is some random text for translation.\")
same => n,agi(googletranslate.agi,\"${MYTEXT}\",en)
same => n,Verbose(1,In Italian: \${gtranslation})
 
exten => 1237,1,agi(mstts.agi,\"Говорим на русском языке.\",ru)
  
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;;;;;;;;;;;; http://wapo-spb.livejournal.com/8763.html ;;;;;;;;;;;;;;;
[spy0]
exten => s,1,Chanspy(\${chan},v(-4)wqBbE)
exten => s,n,Hangup

[spy1]
exten => 201,1,Answer
same => n,Set(VOLUME(TX)=-1)
same => n,Playback(\${audio})
same => n,Hangup

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; add wav to channels
[macro-create_spy1]
exten => s,1,AGI(voice1.php,\${CHANNEL})
[macro-create_spy2]
exten => s,1,AGI(voice2.php,\${CHANNEL})
[macro-create_spy3]
exten => s,1,AGI(voice3.php,\${CHANNEL})

;;;;;;;;;;;; http://wapo-spb.livejournal.com/8763.html ;;;;;;;;;;;;;;;

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[avtoobzvon]
exten => _X.,1,Dial(SIP/\${EXTEN}@$TRUNK1)

[avtoobzvon_bridge]
exten => s,1,set(time1=\${STRFTIME(\${EPOCH},,%s)})
same => n,Playback(\${file1})
same => n,SayNumber(\${debt})
same => n,Playback(\${file2})
same => n,Set(REC_FILE=\"/var/lib/asterisk/sounds/avtoobzvon/records/\${STRFTIME(\${EPOCH},,%Y%m%d%H%M%S)}_\${number}\")
same => n,Monitor(wav,\${REC_FILE},o)
same => n,Playback(beep)
same => n,Wait(15)

exten => h,1,set(time2=\${STRFTIME(\${EPOCH},,%s)})
exten => h,n,AGI(avtoobzvon_end.agi)

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;http://www.asteriskforum.ru/viewtopic.php?p=74178&highlight=vup#74178

[macro-VolumeUp] 
exten => s,1,Set(Vol=\$[\${Vol}+5]) 
same => n,Set(VOLUME(TX)=\${Vol}) 

[macro-VolumeDown] 
exten => s,1,Set(Vol=\$[\${Vol}-5]) 
same => n,Set(VOLUME(TX)=\${Vol})


" >> /etc/asterisk/extensions_other.conf 

echo "######################################################### manager  ##########################################################"
echo ";version $VERSION
[general]
enabled = yes
webenabled = no
port = 5038
bindaddr = 0.0.0.0

[$AMIUSER]
secret = $AMIPWD
deny=0.0.0.0/0.0.0.0
permit=127.0.0.1/255.255.255.0
read = system,call,log,verbose,command,agent,user,originate
write = system,call,log,verbose,command,agent,user,originate

[$AMIUSERPHONEBOOK]
secret = $AMIPWDPHONEBOOK
deny=0.0.0.0/0.0.0.0
permit=127.0.0.1/255.255.255.0
read = system,call,log,verbose,command,agent,user,originate
write = system,call,log,verbose,command,agent,user,originate

" >> /etc/asterisk/manager.conf

echo "######################################################### featuremap  ##########################################################"
echo ";version $VERSION
[general]
parkext => 700
parkpos => 701-709
context => parkedcalls
parkingtime => 600

[featuremap]
blindxfer => ##
parkcall => #72

[applicationmap]
pitch0 => *0,self/both,Macro,conf

pitch1 => *1,self/caller,Macro,create_spy1 ; aeroport
pitch2 => *2,self/caller,Macro,create_spy2 ; metro
pitch3 => *3,self/caller,Macro,create_spy3 ; metranom

pitch4 => *4,self/caller,Set(PITCH_SHIFT(rx)=1.5) ; baby
pitch5 => *5,self/caller,Set(PITCH_SHIFT(rx)=1.0) ; normal
pitch6 => *6,self/caller,Set(PITCH_SHIFT(rx)=0.8) ; barabas

pitch7 => *7,self/caller,Set(VOLUME(RX)=-50) ; voice down
pitch8 => *8,self/caller,Set(VOLUME(RX)=0) ; voice normal
pitch9 => *9,self/caller,Set(VOLUME(RX)=50) ; voice 

VUp     => 52*,self,Macro,VolumeUp 
VDown   => 58*,self,Macro,VolumeDown

" >> /etc/asterisk/features.conf


echo "######################################################### logger  ##########################################################"
echo ";version $VERSION
[general]
[logfiles]
console => notice,warning,error
messages => notice,warning,error
full => notice,warning,error,debug,verbose,dtmf,fax

" >> /etc/asterisk/logger.conf 


echo "######################################################### moh  ##########################################################"
echo ";version $VERSION
[general]

[default]
mode=files
directory=moh

[connect]
mode=files
directory=/var/lib/asterisk/moh/connect

" >> /etc/asterisk/musiconhold.conf 




service asterisk restart

chkconfig --add httpd 
chkconfig --level 345 httpd on
chkconfig httpd on

chkconfig --add mysqld
chkconfig --level 345 mysqld on
chkconfig mysqld on

chkconfig --add asterisk
chkconfig --level 345 asterisk on
chkconfig asterisk on

