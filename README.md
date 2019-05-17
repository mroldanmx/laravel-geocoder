# Laravel Geocoder

Find the location data from a string, such as prov, city, and coordinates. Dead Simple.
Works for US and CA addresses.

Uses https://geocoder.ca on the background

### Usage

```php

$geo = new Geocoder();
$locationObject = $geo->locate("V8L4S2");

$location->standard->city; //Sidney
$location->standard->prov; //BC

```

```json
{
  "standard": {
    "staddress": {},
    "stnumber": {},
    "prov": "BC",
    "city": "Sidney",
    "confidence": "0.9"
  },
  "Dissemination_Area": { "adauid": "59170008", "dauid": "59170036" },
  "longt": "-123.408216",
  "postal": "V8L4S2",
  "latt": "48.662774"
}
```

### Using proxy

To try avoiding the geocoder.ca service throttling us.

It'll try the request until a location is received without ERROR_ACCESS_DENIED

we can activate a proxy list by doing:

```php
$geo->debug = true;
$geo->useProxy= true;

//will download a list of proxies from $geo->proxyListURL. Set to https://proxy.rudnkh.me/txt by default

$geo->locate("V8L4S2");
```

#### Continuous Testing

The composer.json comes with [PHPUnit]() and the task runner [Robo](https://github.com/consolidation/Robo).

To execute the task runner defined in the `RoboFile`:

```
$ composer watch
```

The robo plugin executes the tests **every time a change is made in src or tests folder**. You can tweak this to your preference in the RoboFile.
