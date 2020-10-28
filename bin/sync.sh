#!/bin/sh

DEVDIR="web/app/uploads/"
DEVSITE="http://trace.test"

STAGDIR="forge@178.62.99.195:/home/forge/trace.in-beta.link/web/app/uploads/"
STAGSITE="https://trace.in-beta.link"

PRODDIR="forge@178.62.121.111:/home/forge/www.tracesolutions.co.uk/web/app/uploads/"
PRODSITE="http://www.tracesolutions.co.uk"

FROM=$1
TO=$2

case "$1-$2" in
  development-production) DIR="up";  FROMSITE=$DEVSITE;  FROMDIR=$DEVDIR;  TOSITE=$PRODSITE; TODIR=$PRODDIR; ;;
  development-staging)    DIR="up"   FROMSITE=$DEVSITE;  FROMDIR=$DEVDIR;  TOSITE=$STAGSITE; TODIR=$STAGDIR; ;;
  production-development) DIR="down" FROMSITE=$PRODSITE; FROMDIR=$PRODDIR; TOSITE=$DEVSITE;  TODIR=$DEVDIR; ;;
  staging-development)    DIR="down" FROMSITE=$STAGSITE; FROMDIR=$STAGDIR; TOSITE=$DEVSITE;  TODIR=$DEVDIR; ;;
  *) echo "usage: $0 development production | development staging | production development | production staging" && exit 1 ;;
esac

read -r -p "Would you really like to reset the $TO database and sync $DIR from $FROM? [y/N] " response

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
  cd ../ &&
  wp "@$TO" db export &&
  wp "@$FROM" db export - | wp "@$TO" db import - &&
  wp "@$TO" search-replace "$FROMSITE" "$TOSITE" &&
  rsync -az --progress "$FROMDIR" "$TODIR"
fi