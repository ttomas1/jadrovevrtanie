# jadrovevrtanie.sk — jadrové vŕtanie do betónu

Jednostránkový prezentačný web služby **jadrové vŕtanie** (Bratislava a okolie).
Celá stránka je jediný súbor bez závislostí: [`index.html`](./index.html).

**Živá stránka:** https://ttomas1.github.io/jadrovevrtanie/

## Zapnutie GitHub Pages (bezplatné, jednorazovo)

1. Otvorte **Settings → Pages** tohto repozitára.
2. V časti **Build and deployment** zvoľte **Deploy from a branch**.
3. Vyberte vetvu **`main`** a priečinok **`/ (root)`**, uložte.
4. O 1 – 2 minúty je stránka dostupná na adrese vyššie.

Vlastnú doménu `jadrovevrtanie.sk` je možné neskôr pripojiť v **Settings → Pages → Custom domain**.

## Čo si upraviť pred ostrým spustením

Všetko je v `index.html`:

| Čo | Ako nájsť | Poznámka |
|---|---|---|
| Telefónne číslo | vyhľadajte `+421 900 000 000` a `+421900000000` | zástupné číslo, vyskytuje sa na viacerých miestach |
| E-mail | vyhľadajte `info@jadrovevrtanie.sk` | generický e-mail k navrhnutej doméne |
| Ceny | sekcia `<section id="cennik">` | orientačné trhové ceny, upravte podľa seba |
| Kontaktný formulár | `action="https://formsubmit.co/info@jadrovevrtanie.sk"` | nahraďte reálnym e-mailom; po prvom odoslaní príde od FormSubmit aktivačný e-mail, ktorý treba potvrdiť |
