# Adresslabor PHP CheckClient

The Adresslabor CheckClient is a utility class for the Adresslabor address validation service.

For more information visit <a href="https://adresslabor.de" target="_blank">adresslabor.de</a>

## Installation

````shell
composer require adresslabor/check-client
````

## Usage

```php

// initialize the checkClient
$apiCid = "0000";
$apiKey = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$associative = true;

// The client will test your credentials first
$checkClient = new \Adresslabor\CheckClient($apiCid, $apiKey, $associative);

// get your available credits
$availableCredits = $checkClient->credits;

// https://adresslabor.de/de/produkte/adress-check-dach.html
$scResult = $checkClient->addressCheckDACH("Kolping Str.", "14", "63768", "Hösbach", "DE");
$scxResult = $checkClient->addressCheckDACH("Kolping Str.", "14", "63768", "Hösbach", "DE", true);

// https://adresslabor.de/de/produkte/adress-check-world.html
$scIntResult = $checkClient->addressCheckWorld("Kolping Str.", "14", "63768", "Hösbach", "DE","","","", "", "", "");

// https://adresslabor.de/de/produkte/fake-check.html
$fkResult = $checkClient->fakeCheck("Donald", "Duck", "Kolping Str.", "14", "63768", "Hösbach", "DE");

// https://adresslabor.de/de/produkte/name-check-b2c.html
$ncResult = $checkClient->nameCheckB2C("Rolf", "Paschold", "Frau", "Dr.");

// https://adresslabor.de/de/produkte/e-mail-check.html
$emResult = $checkClient->emailCheck("prof@adresslabor.de");
$emxResult = $checkClient->emailCheck("prof@adresslabor.de", true);

// https://adresslabor.de/de/produkte/telefonverzeichnis.html
$pbResult = $checkClient->telephoneDirectory("Donald", "Duck", "Kolping Str.", "63768", "Hösbach", "DE", "+4912345789", "Dr.", "14");
$pbtResult = $checkClient->telephoneDirectory("Donald", "Duck", "Kolping Str.", "63768", "Hösbach", "DE", "+4912345789", "Dr.", "14", true);

// https://adresslabor.de/de/produkte/ust-idnr-check.html
$vatidResult = $checkClient->vatNumberCheck("DE304496992");
$vatidxResult = $checkClient->vatNumberCheck("DE304496992", true);

// execute multiple checks in one single request
$multiCheckResult = $checkClient->check(\Adresslabor\CheckClient::PATH_V3, [
    'product' => 'fk,nc,scx',
    'firstname' => 'Rolf',
    'lastname' => 'Paschold',
    'street' => 'Kolping Str.',
    'hno' => '14',
    'zip' => '63768',
    'city' => 'Hösbach'
]);


```
