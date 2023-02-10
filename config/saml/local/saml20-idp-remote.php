@@ -1,203 +0,0 @@
<?php

$metadata['http://idp.ngeo.local.ge.ch:8080/simplesaml/saml2/idp/metadata.php'] = [
  'metadata-set' => 'saml20-idp-remote',
  'entityid' => 'http://idp.ngeo.local.ge.ch:8080/simplesaml/saml2/idp/metadata.php',
  'SingleSignOnService' => [
    [
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'http://idp.ngeo.local.ge.ch:8080/simplesaml/saml2/idp/SSOService.php',
    ],
  ],
//  'SingleLogoutService' => [
//    [
//      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
//      'Location' => 'http://idp.ngeo.local.ge.ch:8080/simplesaml/saml2/idp/SingleLogoutService.php',
//    ],
//  ],
  'certData' => 'MIICmjCCAYICCQDX5sKPsYV3+jANBgkqhkiG9w0BAQsFADAPMQ0wCwYDVQQDDAR0ZXN0MB4XDTE5MTIyMzA5MDI1MVoXDTIwMDEyMjA5MDI1MVowDzENMAsGA1UEAwwEdGVzdDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMdtDJ278DQTp84O5Nq5F8s5YOR34GFOGI2Swb/3pU7X7918lVljiKv7WVM65S59nJSyXV+fa15qoXLfsdRnq3yw0hTSTs2YDX+jl98kK3ksk3rROfYh1LIgByj4/4NeNpExgeB6rQk5Ay7YS+ARmMzEjXa0favHxu5BOdB2y6WvRQyjPS2lirT/PKWBZc04QZepsZ56+W7bd557tdedcYdY/nKI1qmSQClG2qgslzgqFOv1KCOw43a3mcK/TiiD8IXyLMJNC6OFW3xTL/BG6SOZ3dQ9rjQOBga+6GIaQsDjC4Xp7Kx+FkSvgaw0sJV8gt1mlZy+27Sza6d+hHD2pWECAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAm2fk1+gd08FQxK7TL04O8EK1f0bzaGGUxWzlh98a3Dm8+OPhVQRi/KLsFHliLC86lsZQKunYdDB+qd0KUk2oqDG6tstG/htmRYD/S/jNmt8gyPAVi11dHUqW3IvQgJLwxZtoAv6PNs188hvT1WK3VWJ4YgFKYi5XQYnR5sv69Vsr91lYAxyrIlMKahjSW1jTD3ByRfAQghsSLk6fV0OyJHyhuF1TxOVBVf8XOdaqfmvD90JGIPGtfMLPUX4m35qaGAU48PwCL7L3cRHYs9wZWc0ifXZcBENLtHYCLi5txR8c5lyHB9d3AQHzKHMFNjLswn5HsckKg83RH7+eVqHqGw==',
  'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
  'contacts' => [
    [
      'emailAddress' => 'na@example.com',
      'contactType' => 'technical',
      'givenName' => 'Administrator',
    ],
  ],
];
