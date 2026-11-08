<?php
// Kontaktný formulár navrtame.sk — spracovanie na vlastnom serveri.
// Príjemcovia sa čítajú z /home/navrtame/.form_recipient (jedna adresa na
// riadok; súbor je mimo verejného repozitára aj mimo document rootu).
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

// Anti-spam: pole _t vypĺňa JavaScript stránky pri načítaní. Bot, ktorý
// POST-uje priamo na endpoint bez načítania stránky, token nemá.
$isSpam = (strpos($line('_t', 40), 'nvt-') !== 0);

// Limit: najviac 5 dopytov za hodinu z jednej IP (počíta sa z logu).
$ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
if (!$isSpam && $ip !== '' && is_readable(LOG_FILE)) {
    $hourAgo = time() - 3600;
    $count = 0;
    foreach (array_slice(file(LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [], -200) as $l) {
        if (strpos($l, 'ip=' . $ip . ' ') !== false
            && preg_match('/^\[([0-9 :-]{19})\]/', $l, $m)
            && strtotime($m[1]) >= $hourAgo) {
            $count++;
        }
    }
    if ($count >= 5) {
        $isSpam = true;
    }
}

// Záložný zápis na disk — dopyt sa nestratí, ani keby e-mail zlyhal.
if (!is_dir(dirname(LOG_FILE))) {
    @mkdir(dirname(LOG_FILE), 0750, true);
}
@file_put_contents(
    LOG_FILE,
    sprintf(
        "[%s]%s meno=%s | tel=%s | email=%s | priemer=%s | ip=%s | sprava=%s\n",
        date('Y-m-d H:i:s'),
        $isSpam ? ' [SPAM]' : '',
        $meno,
        $telefon,
        $email,
        $priemer,
        $ip,
        str_replace("\n", ' / ', $sprava)
    ),
    FILE_APPEND | LOCK_EX
);

// Spam: botovi potvrdíme „úspech", ale e-mail neodošleme.
// Dopyt zostáva zaznamenaný v logu s značkou [SPAM] pre prípad omylu.
if ($isSpam) {
    header('Location: /?odoslane=1#kontakt');
    exit;
}

$recipients = [];
foreach (preg_split('/\R+/', (string)@file_get_contents(RECIPIENT_FILE)) ?: [] as $r) {
    $r = trim($r);
    if ($r !== '' && filter_var($r, FILTER_VALIDATE_EMAIL)) {
        $recipients[] = $r;
    }
}
$sent = false;

if ($recipients !== []) {
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
    // Piaty parameter nastavuje obálkového odosielateľa (envelope-from),
    // aby sa zhodoval s hlavičkou From a SPF domény navrtame.sk.
    $sent = mail(implode(', ', $recipients), $subject, $body, $headers, '-f' . FROM_ADDRESS);
}

header('Location: ' . ($sent ? '/?odoslane=1#kontakt' : '/?chyba=odoslanie#kontakt'));
