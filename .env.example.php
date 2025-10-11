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
  'GOOGLE_AUTH_REDIRECT_URI' => 'http://localhost/SIPAN/oauth2/google',

  # =========================================================================================
  # =           Credenciales de Github (https://github.com/settings/applications)           =
  # =========================================================================================
  'GITHUB_AUTH_CLIENT_ID' => '{github-client-id}',
  'GITHUB_AUTH_CLIENT_SECRET' => '{github-client-secret}',
  'GITHUB_AUTH_REDIRECT_URI' => 'http://localhost/SIPAN/oauth2/github',

  # ================================================
  # =           Credenciales de Facebook           =
  # ================================================
  'FACEBOOK_AUTH_CLIENT_ID' => '{facebook-app-id}',
  'FACEBOOK_AUTH_CLIENT_SECRET' => '{facebook-app-secret}',
  'FACEBOOK_AUTH_REDIRECT_URI' => 'http://localhost/SIPAN/oauth2/facebook',

  # ===============================================
  # =           Credenciales de Twitter           =
  # ===============================================
  'TWITTER_AUTH_CLIENT_ID' => '{twitter-client-id}',
  'TWITTER_AUTH_CLIENT_SECRET' => '{twitter-client-secret}',
  'TWITTER_AUTH_REDIRECT_URI' => 'http://localhost/SIPAN/oauth2/twitter',
];
