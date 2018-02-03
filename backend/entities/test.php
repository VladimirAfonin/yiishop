<?php

namespace backend\entities;

class Test
{
    public static function findWorkProxy($step = 0, $steps = 30, $haveProxy = null)
    {
        $true = true;
        $proxy = ( ($haveProxy != null) ? $haveProxy : ((array) self::getProxyResponse(self::PUBPROXY_API_URL)) );

        while ($true) {
            if(count($proxy) > 1) {
                shuffle($proxy);
                $http_code = self::checkProxy($proxy[$step]);
            } else {
                $http_code = self::checkProxy($proxy);
            }

            $step++;
            if($step == $steps && $http_code != 200) { throw new \RuntimeException('попытки закончились, попробуй снова!') ;}
            $true = (($step < $steps) && ($http_code != 200));
        }
        return [
            'proxy' => $proxy[$step],
            'step' => $step,
        ];
    }
}