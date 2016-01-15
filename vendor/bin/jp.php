#!/usr/bin/env sh
SRC_DIR="`pwd`"
cd "`dirname "$0"`"
cd "../mtdowling/jmespath.php/bin"
BIN_TARGET="`pwd`/jp.php"
cd "$SRC_DIR"
"$BIN_TARGET" "$@"
