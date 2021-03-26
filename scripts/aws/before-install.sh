#!/bin/bash

FILE=/var/www/html/staging/app/lang/pt_BR.txt.BAK
if [ -f $FILE ] ; then
        rm  $FILE
fi


if [{$APPLICATION_NAME} == "helpdezk-staging"]
then
  LANG_PATH=/var/www/html/staging/app/lang/
elif [{$APPLICATION_NAME} == "quintana-helpdezk"]
then
  LANG_PATH=/var/www/html/helpdezk/app/lang/
fi

mv {LANG_PATH}pt_BR.txt {LANG_PATH}pt_BR.txt.BAK




