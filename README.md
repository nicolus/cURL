cURL
====

A simple curl OO wrapper.

usage examples : 

```php
use Curl\Curl;

try {
    $url = "http://www.google.com";
    //A simple GET request (with followlocation and no timeout by default)
    echo Curl::get($url);

    //A simple POST request (multipart) :
    echo Curl::post($url, ["param1" => "value1" ]);

    //A simple POST request (x-www-urlencoded) :
    echo Curl::post($url, "param1=value1&param2=value2");

    //Using a proxy :
    echo (new Curl)
        ->setUrl($url)
        ->setProxy("myproxy.com:31280", "username", "password")
        ->request();

    //A more complex request
    $curl = new Curl;
    echo $curl
        ->setUrl($url)
        ->addHeaders(['X-CUSTOMHEADER: aaa', 'pipoheader: bbb'])
        ->addHeaders('trololo: ccc')
        ->setAuth("Username", "Password")
        ->setTimeout(30)
        ->setUserAgent(Curl::UA_FIREFOX)
        ->request();

    //Get info for the last request :
    print_r($curl->getInfo());

} catch (Exception $e) {
    echo "erreur avec une requête curl : " . $e->getMessage() ."<br>\n";
    echo "code : " . $e->getCode() ."<br>\n";
}
```