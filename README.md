# navrtame.sk — jadrové vŕtanie do betónu

Jednostránkový prezentačný web služby **jadrové vŕtanie** (Bratislava a okolie).
Celá stránka je jediný súbor bez závislostí: [`index.html`](./index.html).

**Živá stránka:** https://ttomas1.github.io/jadrovevrtanie/

## Nasadenie na vlastné VPS (navrtame.sk)

Server: AlmaLinux 9 + Virtualmin. Súbory na nasadenie: `index.html`, `robots.txt`, `sitemap.xml`.

1. **DNS u registrátora:** A záznam `@` → IP servera, A záznam `www` → IP servera (TTL 3600).
2. **Virtualmin:** Create Virtual Server → doména `navrtame.sk` (bez DB, bez PHP).
3. **Súbory:** nahrať do `/home/navrtame/public_html/` (scp/SFTP alebo `git clone` tohto repa a symlink/kópia).
4. **HTTPS:** Virtualmin → Manage Virtual Server → Setup SSL Website → Let's Encrypt (doplniť aj `www.navrtame.sk`), zapnúť presmerovanie HTTP → HTTPS.
5. **Aktualizácie:** upraviť súbory tu v repo a na serveri `git pull` (alebo znova scp).

## Zapnutie GitHub Pages (bezplatné, jednorazovo)

1. Otvorte **Settings → Pages** tohto repozitára.
2. V časti **Build and deployment** zvoľte **Deploy from a branch**.
3. Vyberte vetvu **`main`** a priečinok **`/ (root)`**, uložte.
4. O 1 – 2 minúty je stránka dostupná na adrese vyššie.

Vlastnú doménu `navrtame.sk` je možné neskôr pripojiť v **Settings → Pages → Custom domain**.

## Čo si upraviť pred ostrým spustením

Všetko je v `index.html`:

| Čo | Ako nájsť | Poznámka |
|---|---|---|
| Telefónne číslo | vyhľadajte `+421 910 323 969` a `+421910323969` | reálne číslo, vyskytuje sa na viacerých miestach |
| E-mail | vyhľadajte `info@navrtame.sk` | generický zobrazovaný e-mail k navrhnutej doméne |
| Ceny | sekcia `<section id="cennik">` | orientačné trhové ceny, upravte podľa seba |
| Kontaktný formulár | `action="https://formsubmit.co/7060e3a5c9d195b1369b3ccb56069ab4"` | aktivovaný náhodný alias FormSubmit — skrýva skutočnú cieľovú adresu pred spambotmi |
| Fotky | sekcia `<section id="praca">` | zatiaľ ilustračné SVG obrázky; vlastnú fotku vložíte nahradením celého `<svg>…</svg>` za `<img src="foto.jpg" alt="Jadrové vŕtanie">` (súbor fotky nahrajte vedľa index.html) |

> Doména: pôvodne zvolená `jadrovevrtanie.sk` je obsadená; `navrtame.sk` bola podľa DNS kontroly a webového vyhľadávania (2026-08) voľná a bez kolízie s existujúcou značkou — pred registráciou ešte raz overte u registrátora.
