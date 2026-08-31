#!/bin/bash
# Nasadenie navrtame.sk na VPS (spustat ako root: bash deploy.sh alebo /usr/local/bin/deploy-navrtame)
# Instalacia: cp /home/navrtame/site/deploy.sh /usr/local/bin/deploy-navrtame && chmod +x /usr/local/bin/deploy-navrtame
set -e

SITE=/home/navrtame/site
DOCROOT=/home/navrtame/public_html
INDEXNOW_KEY=11179fdc02195420b5fe7ffbd7cfe6f8

sudo -u navrtame git -C "$SITE" pull
sudo -u navrtame cp "$SITE"/{index.html,robots.txt,sitemap.xml,og-image.png,"$INDEXNOW_KEY".txt} "$DOCROOT"/

# IndexNow ping - okamzite upozorni Bing/Seznam/Yandex na zmenu (Google IndexNow nepodporuje)
curl -s "https://api.indexnow.org/indexnow?url=https://navrtame.sk/&key=$INDEXNOW_KEY" -o /dev/null -w "IndexNow ping: HTTP %{http_code}\n" || true

echo "Nasadene: $(sudo -u navrtame git -C "$SITE" log -1 --oneline)"
