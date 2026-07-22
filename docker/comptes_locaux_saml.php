<?php
$config = [
  'admin' => [
    'core:AdminPassword',
  ],
  'example-userpass' => [
    'exampleauth:UserPass',
    'administrateur:admin' => [
      "FullName" => ["Admin Istrateur (DI)"],
      "Roles" => [
        "SITEOFFICIEL.NGEO-GE.ADMINISTRATEUR",
        "SITEOFFICIEL.NGEO-GE.UTILISATEUR"
      ],
      "Login" => ["Administrateur"],
      "Email" => ["admin.local@dummy.mail"]
    ],
    'contributeur:contrib' => [
      "FullName" => ["Contri Buteur (DI)"],
      "Roles" => [
        "SITEOFFICIEL.NGEO-GE.CONTRIBUTEUR",
        "SITEOFFICIEL.NGEO-GE.UTILISATEUR"
      ],
      "Login" => ["Contributeur"],
      "Email" => ["contributeur.local@dummy.mail"]
    ],
    'admin-site:site' => [
      "FullName" => ["Admin Site (DI)"],
      "Roles" => [
        "SITEOFFICIEL.NGEO-GE.ADMIN-SITE",
        "SITEOFFICIEL.NGEO-GE.UTILISATEUR"
      ],
      "Login" => ["Admin-site"],
      "Email" => ["admin-site.local@dummy.mail"]
    ],
  ],
];
