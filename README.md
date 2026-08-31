# navrtame.sk — jadrové vŕtanie do betónu

Jednostránkový prezentačný web služby **jadrové vŕtanie** (Bratislava a okolie).
Celá stránka je jediný súbor bez závislostí: [`index.html`](./index.html).

**Živá stránka:** https://ttomas1.github.io/jadrovevrtanie/

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
