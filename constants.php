<?php
$private_key = <<<EOPK
-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDCuyaSz6yVsS+v
xZlQYXHgipdybewAT4TSKoi0qh8DB3IYc06EwuPYiZugekuojcs+nir2DC3tYxNa
XWkQGSPLrLA3afVlAny4Q7s/nkKQ29HPa5jzsJFtI9/8eKDdsviAUHoQ1mpy8A9F
CYa+dilOGuO4q48UJUZ2G1La3prioaF/i42lOJIdLhZC5IsvbgSkQF3NBR0Dtmqb
Df5lcUBDhFeUM5HqzJEG7kp6j8YXd2s7b7XXyTOFWI8OMD1pVzjt1vcc/PjKy9Nf
mLFgyzxI9uz7LoYDxJP1T9lyH/N12GlAsNPiAcarWtdecrAoQbIzTnj+LQVIlq3A
MQ//3+YZAgMBAAECggEAUlLAWeyF77q6iDqnN/4aIG023V9vGCqF4jutE4OgHK23
JHMKzMF/hXmXW16YQafANrazPtWjTOpHsjovQmj+YwqcbLDU12EXzOaFcL29MnRb
3K6GOO96a4NxG9D7YP5aZBShpiCfW/v02KDQYBCgIa7oepe8oy2m/iNLAdB99jTU
uvCqFuHw4mdKFdmpWh/Yx15y6r6StRUPQpCETV6IOM/rMxNyx2KmGc2zVllpRbCn
Y69qCEpghXdmqFG3x1vf/0pe9t43Ru30CZGcK8cxhCV4dzPNrAgapfc8HEQHgnDl
NIyNhJNNOz6F+9ob7Sos9P8PdPV91zBMVTzJofJfIQKBgQD34QkIW6JpbhJ1ThJB
3KUqjcUAaafRki0IwKmfOYGC5su2Pq4jGOkp27IDUrKKgZd8SwwlbNC9vM5ySeZp
r7YAuTUqyORVFLliXDKYgbvjAt0KLjZOZ5ojb5KD0Dg+TLktckfIjHuakyn5Q8UK
2c4YX+CNeHHztSM2ml3lS1xmrQKBgQDJHFzP/J48mh0CnH2VJdSrrIaws8sDmPAI
uo2z9TEWfv/2QgAog+P7CiiWI0F9KO/yAOcDYhP4DG9QyjLM5WlXvWD4BYvgvT/R
nIG+ycbzmrX11B5oI64LyeTcqymEISdLR2sApHGM9dbcH5fSvIQaKC1nK5PnFbHu
cFfmGfxmnQKBgQDoN3WS0uWwB2JbaqxH70D8QJwR6ulAt8Rgsr01YVYnH7gwH0Bb
uPaWNC7sVgjVINqdomDanrpfRgXRo9GrqOTEgL2CO3lNC4Ew2Fa98Kvn28LfyrMt
eHk6QUftHJ/UHWRNYwZEpvcUtFFaJ1bs92bQBuIDJpb6TPOUL8FPEe6acQKBgAhT
RUBS69YEZzkJc2VuOHyW2siL2NagSSeDWYRenRaaUUxHdSw4MYLd68ozUW21+SDi
iq2oLL1y/lSw2iODR7YbH92ElULLcs3hlblpLvQ8rlWr30peV8EjLXc5GrHekXrb
TQGszQqRzsA0Cpvts+ZFTYPc2PWS/1eojOr4nms9AoGAMnTMsObRVzwDCn91PBa+
XnFOt1KYdp6brcGIxYi2WULqkawxCSWLJOBA+A3CHArKnRwfSx+3leQhMx8s6hSj
oMNAbGwj3gidqibzLXxeqR6qGaIIN/K2785gCtzX2bG4rXM2GaSjFujwHojCiAyf
qMB7sd3qz2L0uIr3PiBwDIk=
-----END PRIVATE KEY-----
EOPK;

    define('CLIENT_PRIVATE_KEY', $private_key);
    define('CLIENT_ASSERTION_TYPE', 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer');
    define('ESIGNET_SERVICE_URL', "https://esignet-mosipid.dst-dev.mosip.net");
    define('USERINFO_PRIVATE_KEY', $private_key);
    define('CLIENT_ID', '917vJsVG-eyezMz88_kf6TCp56ZFXl7t_iDwvCw5LI8');
    define('SCOPE','openid');
    define('STATE','scpe1234');
    define('ACR_VALUES','mosip:idp:acr:generated-code mosip:idp:acr:biometrics mosip:idp:acr:linked-wallet');
    define('CLAIMS','{"userinfo":{"individual_id":{"essential":true}, "name#en":{"essential":true},"name":{"essential":true},"phone_number":{"essential":false},"email":{"essential":true},"picture":{"essential":true},"gender":{"essential":true},"birthdate":{"essential":true},"address":{"essential":true}},"id_token":{}}');
    define('CLAIMS_LOCALES','en');
    define('UI_LOCALES','en-US');

    define('CALLBACK_URL', 'http://localhost/enrolmentPortal/callback.php');

    define("DB_HOST", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "");
    define("DB_NAME", "voter_database");

    define('ESIGNET_JWKS_URL', 'https://esignet-mosipid.dst-dev.mosip.net/.well-known/jwks.json');
    define('ESIGNET_JWT_DEFAULT_ALG', 'RS256');
    
    define('JWT_DEBUG_MODE', true);

?>