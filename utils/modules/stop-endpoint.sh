#!/bin/bash

PIDFILE=/tmp/fusekiPid
kill `cat $PIDFILE`
rm $PIDFILE
