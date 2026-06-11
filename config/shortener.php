<?php

return [

    /*
    | Domini per cui è vietato creare short link (phishing/abuse noti).
    | Confronto case-insensitive, include i sottodomini.
    */
    'blocked_domains' => array_filter(explode(',', (string) env('SHORTENER_BLOCKED_DOMAINS', ''))),

    /*
    | Se true, risolve il DNS dell'host e rifiuta IP privati/riservati (anti-SSRF).
    | Disattivato nei test per evitare lookup di rete.
    */
    'resolve_dns' => env('SHORTENER_RESOLVE_DNS', true),

];
