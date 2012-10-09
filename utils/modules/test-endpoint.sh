#!/bin/bash

PIDFILE=/tmp/fusekiPid
PID=`cat $PIDFILE`
kill -0 $PID

