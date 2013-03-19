#!/bin/sh

result=`cat $1 |grep -v ^$2`
echo "$result" > $1
