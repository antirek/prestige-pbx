#!/bin/sh
SOX=/usr/bin/sox

LEFT="$1"
RIGHT="$2"
OUT="$3"

MP3=`echo "$OUT" |sed 's/.wav/.mp3/'`

rm -f MP3
rm -f OUT

$SOX $LEFT -c 2 $LEFT.wav remix 0 1
$SOX $RIGHT -c 2 $RIGHT.wav remix 1 0

$SOX --combine mix-power $LEFT.wav $RIGHT.wav $MP3

#remove temporary files
test -w $LEFT && rm $LEFT
test -w $RIGHT && rm $RIGHT

test -w $LEFT.wav && rm $LEFT.wav
test -w $RIGHT.wav && rm $RIGHT.wav

# eof
