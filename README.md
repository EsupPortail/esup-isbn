# Esup-ISBN
Service mobile qui permet de connaitre la disponibilité d'un ouvrage dans un ou plusieurs catalogues publics (OPAC).
Pour interroger ces catalogues publics, il se base sur l'identifiants de l'ouvrage (ISBN) qui est, soit :
+ lu via le scanner mobile
+ saisi manuellement 

Ce service fonctionne à la fois sur iPhone et sur Android.


## Installation
Créer un nouveau canal et appeler isbn-scan.php


## Configuration
localisation.js :
Indiquer le ou les catalogues publics que ce service doit utiliser en jouant sur le champ 'select' (0/1) :
```
   {
    "name" : "LA ROCHELLE BU",
    "URL" : "http://bib.univ-lr.fr/client/fr_FR/bulr/search/results?qu=<code>",
    "type" : "ISBN",
    "select" : 1
   },
```


