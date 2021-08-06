find /web/ogcapi/ -type f -print0 | sudo xargs -0 chmod 0664
find /web/ogcapi/ -type d -print0 | sudo xargs -0 chmod 2775

