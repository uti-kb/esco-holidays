# esco-holidays
Polskie święta zwykłe i ruchome

Wstęp
-----
Ta mała biblioteka zwraca polskie święta ustawowo wolne od pracy. Wielkanoc jest świętem ruchomym. Biblioteka potrafi wyliczyć to święto od roku 1582 do 2499 wg algorytmu Gaussa.

Wymagania
---------
Sprawdź plik [composer.json](composer.json)

Instalacja
----------
Najprostszym sposobem instalacji biblioteki jest użycie composera. W konsoli wpisz:
```console
$ composer require guliano/esco-holidays
```

Użycie
------
Obecnie biblioteka udostępnia jedną statyczną metodę, za pomocą której możemy wyświetlić wszystkie święta z podanego zakresu dat:
```php
$dateFrom = new \DateTime('2015-01-01');
$dateTo = new \DateTime('2015-12-31');
$holidays = HolidaysService::getHolidaysBetween($dateFrom, $dateTo);
var_dump($holidays);
```

Licencja
--------
MIT