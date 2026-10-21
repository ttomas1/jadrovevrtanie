<?php
// Kontaktný formulár navrtame.sk — spracovanie na vlastnom serveri.
// E-mail príjemcu sa číta z /home/navrtame/.form_recipient (súbor mimo
// verejného repozitára aj mimo document rootu, aby adresa nebola zverejnená).
// Každý dopyt sa pre istotu zapisuje aj do /home/navrtame/formlog/dopyty.log.
declare(strict_types=1);

const RECIPIENT_FILE = '/home/navrtame/.form_recipient';
const LOG_FILE       = '/home/navrtame/formlog/dopyty.log';
const FROM_ADDRESS   = 'formular@navrtame.sk';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: /#kontakt');
    exit;
}

// Hodnoty idúce do hlavičiek nesmú obsahovať nové riadky (header injection).
$line = static function (string $key, int $max): string {
    $v = trim((string)($_POST[$key] ?? ''));
    $v = str_replace(["\r", "\n", "\0"], ' ', $v);
    return mb_substr($v, 0, $max);
};

$honey   = $line('_honey', 20);
$meno    = $line('Meno', 100);
$telefon = $line('Telefon', 40);
$email   = $line('Email', 120);
$priemer = $line('Priemer', 60);
$sprava  = mb_substr(str_replace(["\r", "\0"], '', trim((string)($_POST['Sprava'] ?? ''))), 0, 5000);

// Honeypot vyplnený = spam bot; tvárime sa, že prešlo.
if ($honey !== '') {
    header('Location: /?odoslane=1#kontakt');
    exit;
}

if ($meno === '' || $telefon === '' || $sprava === '') {
    header('Location: /?chyba=prazdne#kontakt');
    exit;
}

// Záložný zápis na disk — dopyt sa nestratí, ani keby e-mail zlyhal.
if (!is_dir(dirname(LOG_FILE))) {
    @mkdir(dirname(LOG_FILE), 0750, true);
}
@file_put_contents(
    LOG_FILE,
    sprintf(
        "[%s] meno=%s | tel=%s | email=%s | priemer=%s | sprava=%s\n",
        date('Y-m-d H:i:s'),
        $meno,
        $telefon,
        $email,
        $priemer,
        str_replace("\n", ' / ', $sprava)
    ),
    FILE_APPEND | LOCK_EX
);

$to = trim((string)@file_get_contents(RECIPIENT_FILE));
$sent = false;

if ($to !== '' && filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $body = "Nový dopyt z navrtame.sk\n"
        . "------------------------------\n"
        . "Meno:    {$meno}\n"
        . "Telefón: {$telefon}\n"
        . "E-mail:  " . ($email !== '' ? $email : '—') . "\n"
        . "Priemer: " . ($priemer !== '' ? $priemer : '—') . "\n"
        . "------------------------------\n"
        . "Správa:\n{$sprava}\n";

    $headers = [
        'From'         => 'navrtame.sk <' . FROM_ADDRESS . '>',
        'Content-Type' => 'text/plain; charset=UTF-8',
    ];
    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $headers['Reply-To'] = $email;
    }

    $subject = '=?UTF-8?B?' . base64_encode('Dopyt z navrtame.sk – ' . $meno) . '?=';
    $sent = mail($to, $subject, $body, $headers);
}

header('Location: ' . ($sent ? '/?odoslane=1#kontakt' : '/?chyba=odoslanie#kontakt'));
