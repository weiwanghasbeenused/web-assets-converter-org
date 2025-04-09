#!/bin/bash

CURRENT=`pwd`
LOG="static/bash/logs/log"
IMG_FORMATS=("jpg" "jpeg" "png" "tiff" "heif")
VID_FORMATS=("mp4" "mov")
FILES="$@"

if [ ! -f $LOG ]
  then
  touch $LOG
fi
echo -e "\n" >>$LOG;
echo "  ($(date +"%Y-%m-%d %T")) media-converter.sh begins" >>$LOG
for f in $FILES
do 
 FN="${f%.*}"
 EXT="${f##*.}"
 if [ ! -e "$FN.webp" ] && [[ $(echo ${IMG_FORMATS[@]} | fgrep -w $EXT) ]]
 then
 echo "  ($(date +"%Y-%m-%d %T")) >>> converting... $f" >>$LOG
 cwebp -q 85 $f -o "$FN.webp" 2>>$LOG
 echo "  ($(date +"%Y-%m-%d %T")) <<< converting done... $f " >>$LOG
 fi
 if [ ! -e "$FN.webm" ] && [[ $(echo ${VID_FORMATS[@]} | fgrep -w $EXT) ]]
 then
 echo "  ($(date +"%Y-%m-%d %T")) >>> converting... $f" >>$LOG
 ffmpeg -i $f -c:v libvpx-vp9 -crf 30 -b:v 0 -b:a 128k -c:a libopus "$FN.webm" 2>>$LOG
 echo "  ($(date +"%Y-%m-%d %T")) <<< converting done... $f" >>$LOG
 fi
done
echo "  ($(date +"%Y-%m-%d %T")) media-converter.sh ends" >>$LOG
