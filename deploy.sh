#!/bin/bash
# Nasadenie navrtame.sk na VPS (spustat ako root: deploy-navrtame)
# Instalacia: cp /home/navrtame/site/deploy.sh /usr/local/bin/deploy-navrtame && chmod +x /usr/local/bin/deploy-navrtame
set -e

SITE=/home/navrtame/site
DOCROOT=/home/navrtame/public_html
RECIPIENT_FILE=/home/navrtame/.form_recipient
INDEXNOW_KEY=11179fdc02195420b5fe7ffbd7cfe6f8

sudo -u navrtame git -C "$SITE" pull
sudo -u navrtame cp "$SITE"/{index.html,robots.txt,sitemap.xml,og-image.png,kontakt.php,"$INDEXNOW_KEY".txt} "$DOCROOT"/

# Adresar pre zalozny log dopytov z formulara
sudo -u navrtame mkdir -p /home/navrtame/formlog

# Prijemca formulara - drzany MIMO verejneho repozitara
if [ ! -s "$RECIPIENT_FILE" ]; then
  echo "!!! POZOR: $RECIPIENT_FILE neexistuje alebo je prazdny."
  echo "!!! Formular bude dopyty len logovat, e-maily sa neodoslu. Nastavte:"
  echo "!!!   echo 'adresa@domena.sk' > $RECIPIENT_FILE && chown navrtame:navrtame $RECIPIENT_FILE"
fi

# IndexNow ping - okamzite upozorni Bing/Seznam/Yandex na zmenu (Google IndexNow nepodporuje)
curl -s "https://api.indexnow.org/indexnow?url=https://navrtame.sk/&key=$INDEXNOW_KEY" -o /dev/null -w "IndexNow ping: HTTP %{http_code}\n" || true

# Self-test formuloveho endpointu: ocakavame presmerovanie 302 na /?odoslane=1...
CODE=$(curl -s -o /tmp/kontakt-selftest.out -w "%{http_code}" -X POST https://navrtame.sk/kontakt.php \
  --data-urlencode "Meno=Selftest deploy" \
  --data-urlencode "Telefon=+421000000000" \
  --data-urlencode "Sprava=Automaticky self-test po nasadeni - ignorujte." || echo 000)
if [ "$CODE" = "302" ]; then
  echo "Self-test formulara: OK (HTTP 302, dopyt zapisany v /home/navrtame/formlog/dopyty.log)"
else
  echo "!!! Self-test formulara ZLYHAL (HTTP $CODE). Ak vystup nizsie obsahuje PHP kod,"
  echo "!!! zapnite PHP pre domenu: Virtualmin -> navrtame.sk -> Web Configuration -> PHP Options."
  head -c 300 /tmp/kontakt-selftest.out; echo
fi

echo "Nasadene: $(sudo -u navrtame git -C "$SITE" log -1 --oneline)"
