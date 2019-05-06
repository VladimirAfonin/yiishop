<?php
namespace backend\entities;


class ProxyGrab
{
    public $proxyList;

    public function __construct()
    {
        $this->proxylist = 'proxy.txt';
    }

    public function grab()
    {
        $content = file_get_contents('https://checkerproxy.net/archive/' . date('Y-m-d'));
        preg_match_all('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,5}/', $content, $match);
        $count = count(array_unique($match[0]));
        echo "* Grabbed $count proxies!\n";
        # Write proxys to file
        $fh = fopen($this->proxylist, "w");
        for ($i = 0; $i < $count; $i++) {
            fwrite($fh, $match[0][$i] . "\n");
        }
        fclose($fh);
    }

    function check()
    {
        # This URL has to always be online, use services like google, fb, yahoo...
        $url = 'http://m.google.com/robots.txt';
        $ch = new ProxyRequest();
        $ch->url = $url;
        $ch->start();
        $proxies = file($this->proxylist);
        $fh = fopen("proxy.txt", "w");
        for ($i = 0; $i < sizeof($proxies); $i++) {
            if ( ! in_array($i, $ch->bad_proxies)) {
                fwrite($fh, $proxies[$i] . "\n");
            }
        }
        fclose($fh);
        echo '* Removed ' . sizeof($ch->bad_proxies) . " bad proxies!\n";
    }


}