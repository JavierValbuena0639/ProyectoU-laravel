<?php

return [
    // Lista de dominios públicos de correo que NO se permiten para crear cuentas
    // Puedes extenderla con el env BLOCKED_EMAIL_DOMAINS (coma separada)
    'blocked_email_domains' => (function () {
        $default = [
            // Global populares
            'gmail.com','googlemail.com',
            'hotmail.com','hotmail.es','hotmail.fr','hotmail.de',
            'live.com','live.co.uk','live.com.mx',
            'outlook.com','outlook.es','outlook.fr','outlook.de','outlook.com.br',
            'msn.com','passport.com',
            'yahoo.com','ymail.com','rocketmail.com','yahoo.co.uk','yahoo.fr','yahoo.es','yahoo.co.in',
            'icloud.com','me.com','mac.com',
            'aol.com','aim.com',
            'gmx.com','gmx.net','gmx.de','web.de','mail.com',
            'protonmail.com','proton.me','pm.me',
            'yandex.com','yandex.ru','rambler.ru',
            'mail.ru','inbox.ru','list.ru','bk.ru',
            'zoho.com','zohomail.com',
            'tutanota.com','hushmail.com','fastmail.com','hey.com','posteo.de',

            // Europa
            't-online.de','orange.fr','wanadoo.fr','sfr.fr','free.fr','laposte.net',
            'libero.it','virgilio.it','tin.it','alice.it',
            'bluewin.ch','gmx.ch',
            'seznam.cz','centrum.cz','volny.cz','post.cz',
            'o2.pl','wp.pl','onet.pl','interia.pl',
            'ukr.net','bigmir.net','inbox.lv',

            // Norteamérica ISP
            'bellsouth.net','att.net','sbcglobal.net','verizon.net','frontier.com',
            'comcast.net','charter.net','spectrum.net',
            'shaw.ca','rogers.com','cogeco.ca',

            // LATAM
            'uol.com.br','bol.com.br','terra.com.br','ig.com.br','globo.com','zipmail.com.br',
            'live.com.mx','prodigy.net.mx','yahoo.com.mx',

            // Asia
            'qq.com','163.com','126.com','yeah.net',
            'sina.com','sohu.com','aliyun.com',
            'naver.com','daum.net',
            'rediffmail.com',

            // Descartables / temporales
            'mailinator.com','10minutemail.com','guerrillamail.com','sharklasers.com','grr.la','pokemail.net',
            'temp-mail.org','temp-mail.io','tempmail.net','tempinbox.com','mytemp.email',
            'yopmail.com','getnada.com','maildrop.cc','mailnesia.com','dispostable.com','trashmail.com',
            'throwawaymail.com','moakt.com','fakemailgenerator.com','mintemail.com','spamgourmet.com','dropmail.me',

            // Proveedores de envío (ESP) comunes
            'amazonses.com','sendgrid.net','sendgrid.com','mailgun.org','mailgun.com','sparkpostmail.com','sparkpost.com',
            'sendinblue.com','brevo.com','postmarkapp.com','mandrillapp.com','mandrill.com','mailchimp.com','elasticemail.com',
            'mailjet.com','socketlabs.com','smtp.com','sendpulse.com'
        ];
        $envList = env('BLOCKED_EMAIL_DOMAINS');
        if (is_string($envList) && trim($envList) !== '') {
            $extra = array_map(fn($d) => strtolower(trim($d)), explode(',', $envList));
            // Unir y eliminar duplicados
            return array_values(array_unique(array_merge($default, $extra)));
        }
        return $default;
    })(),
];