#!/bin/sh
git archive --format=tar --prefix=reviewlead/ $1 | gzip > ../reviewlead-$1.tgz
