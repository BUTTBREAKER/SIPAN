<?php

return [
  'PDO' => [
    'DSN' => 'sqlite::memory:',
    'USER' => null,
    'PASSWORD' => null,
  ],
  'TIMEZONE' => 'America/Caracas',
  'TEST_ENDPOINT' => 'http://localhost/sipan',

  # ======================================================================================
  # =           Credenciales de Google (https://console.cloud.google.com/auth)           =
  # ======================================================================================
  'GOOGLE_AUTH_CLIENT_ID' => '{google-client-id}',
  'GOOGLE_AUTH_CLIENT_SECRET' => '{google-client-secret}',
  'GOOGLE_AUTH_REDIRECT_URI' => 'http://localhost/sitcav/oauth2/google',
];
