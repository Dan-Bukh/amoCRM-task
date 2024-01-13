<?php
$name = $_POST['name'];
$email = $_POST['email'];
$number = $_POST['number'];
$price = (int)$_POST['price'];


$subdomain = 'daniilanatol9'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/complex'; //Формируем URL для запроса
/** Получаем access_token из хранилища */
$access_token = file_get_contents('accessToken.txt');

$data = [
    [
        "price" =>  $price,
            '_embedded' => [
                "contacts" => [
                    [
                        "first_name" => $name,
                        "custom_fields_values" => [
                            [
                                "field_code" => "EMAIL",
                                "values" => [
                                    [
                                       "enum_code" => "WORK",
                                       "value" => $email,
                                    ]
                                 ]
                            ],
                            [
                                "field_code" => "PHONE",
                                "values" => [
                                    [
                                       "enum_code" => "WORK",
                                       "value" => $number,
                                    ]
                                 ]
                            ]
                        ]
                        
                    ]
                ],
            ]

        ]
];
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token,
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
$out = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$code = (int) $code;
$errors = [
    301 => 'Moved permanently.',
    400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
    401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
    403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
    404 => 'Not found.',
    500 => 'Internal server error.',
    502 => 'Bad gateway.',
    503 => 'Service unavailable.'
];

if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );

header('Location: /');
