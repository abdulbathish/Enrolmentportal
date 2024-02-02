<?php
$private_key = <<<EOPK
-----BEGIN PRIVATE KEY-----
MIIEwgIBADANBgkqhkiG9w0BAQEFAASCBKwwggSoAgEAAoIBAgCUk7Q5IHJevPbP
xdL611XtNqQ9Q01DJAUhYPgzIxdwKpVvcIA9UaRjPQ3Qskl6C0SO0wVw1KY8AYR2
iVIpYr2x2U6yj9TFxSb2xVDXjVoEoM3lNAAjpf30/KbXXySOWebme/DJudVEwSpZ
rEmljTzQeThBQybWQvy5kB1Ost/oyEkiFmCaVuGZZaesMXcUQv8NNh12vRbclw4b
PDDwQqJE30QsL0o31/N8YiVnewEqH0TuS2gTLQ4zIcT67rXDUgptNoMNGuo4MCRa
izHzCLlqsLUtgSMMp73lJV5lEYlKi2oWjrMbZecN07NL1kpfBK6cAqFqOervN9wa
Fab8xQrDCwIDAQABAoIBAgCK0vPbSND0alWmz22RyXBVI/AT+fWQHXDZvlRK26gD
uxZDuPdp1AXoX9yvulZjPXICjXOQ+HCWsshFRYvKdaNPMP+SYkfpvovomXcu4LlS
h7m6Rns209tVdFij9hcfFytjAj17DzMRefeLMCrkD/LZy9nfSNJZ8t9WtyxbI1GJ
nLueq8FinSYgMkZYBLnN7ovb24JkgFn0RrLNfig7l45UuImIa3E9vrjZZtaR14BE
yll/zCoyq5omWS1A9Q6QMYqYfafTgWdaF5JyhC3YVH1tlpwk//fiM8q+TFapch1X
1JB0a8/HtoA1aVakmH5M8m3jQcNxc0VqRmMF2rsOb/Ni6QKBgQ3Bi5iNE8k48ilO
s2I8BhV990Aqnp1WuYIVtXJ/8itvF9EtQ0AQFWzgzEVZSq42m7YoXUqR+g/9trWH
njL0C3x1C7IxcVWm6ewWZ2vACjTwuU/xX3Y3VkIu1UJfton2shyC33fWZ2F+myMp
cp+d9ZvxhR80k5efByAwPDwy3bbzJQKBgQrNBOfF1HGbO60Lc6oAirKf5CgVlpHA
Sn1CUAqsXZVSucgeE3oR0fVJ/mHiwp66VC+Z0EHfX2EdJw1AOd/8dqQRaKRDQ/Z0
W3e05p9yarvV+BLmOCvoE/yTOHKEzW4Y7Gcv+ceH+ZFpcD2AJKt0YzRN9T4ivfJM
vf/+aRwFYIUebwKBgQgWQHrudTNWxwaBvfOCVhFMfI68f4L/+Q8AtCscDMJ8DQRs
F50R14aqoWwjkkPY1rHACRhNuTStczxE/jv2PMpuBPI9HuO7vMmOYj52J4n3+vmE
bRqSbaN65OSgYC+7V/pq9fT9lsk6JPoG49vygPGsixZNUndQhl/l+4NoY++qAQKB
gQhNlaDLb0aqjFS6Af/FjNpGmnHKLsb7KQhkCv45hXO+db2GiXiE7IABkwIS/YRi
P0ecmgGZw0v3ykaggh1Tiq0UCPqGD2AOESbUNZqLrcuacKuuua3fKkY3suQufXJO
zKWCtyURveRwRu2wbgA0Z/MUnxxWPule341FOe5ln86WFwKBgQawxdA6SGdvyhY/
5pWEK8qPb/wxuK/7/3KGpXVzomxo9t5YvGCiqUaU1FE7cn/0Z66e7icLFpd3ep6Y
x24XDZmli95sUbrvoiW5tKJwSckHFiD7I6uvve6MwKxzG/+k1UiiO+lU3+hMRHWO
EDdBEsDHco7T04JxLAepV8MjDb0eJw==
-----END PRIVATE KEY-----
EOPK;

    define('CLIENT_PRIVATE_KEY', $private_key);
    define('CLIENT_ASSERTION_TYPE', 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer');
    define('ESIGNET_SERVICE_URL', "https://esignet.mecstage.mosip.net");
    define('USERINFO_PRIVATE_KEY', $private_key);
    define('CLIENT_ID', '4wIFOn6o0ZtVu6VWzkCa5RKAkr6DD2MMIvtdGPORhkU');
    define('SCOPE','openid profile');
    define('STATE','scpe1234');
    define('ACR_VALUES','mosip:idp:acr:generated-code mosip:idp:acr:biometrics mosip:idp:acr:linked-wallet');
    define('CLAIMS','{"userinfo":{"individual_id":{"essential":true}, "name#en":{"essential":true},"name":{"essential":true},"phone_number":{"essential":true},"email":{"essential":true},"picture":{"essential":false},"gender":{"essential":true},"birthdate":{"essential":true},"address":{"essential":true}},"id_token":{}}');
    define('CLAIMS_LOCALES','en');
    define('UI_LOCALES','en-US');

    define('CALLBACK_URL', 'http://10.13.13.42/Enrolmentportal/callback.php');

    define("DB_HOST", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "");
    define("DB_NAME", "voter_database");

?>