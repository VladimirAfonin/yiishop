<?php

return [
    'class'           => 'yii\web\UrlManager',
//    'hostInfo' => $params['backendHostInfo'],
    'baseUrl'         => '',
    'enablePrettyUrl' => true,
    'showScriptName'  => false,
//    'useStrictParsing' => true,
    'rules'           => [
        'GET modification/create/<id:\d+>' => 'shop/modification/create', // working!
//        'admin/product' => 'admin/product/index', // working!
        '<_m:debug>/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',
//        'GET modification/create/<id:\d+>' => 'shop/modification/create', // working!
        '<_a:login|logout>' => 'auth/<_a>',                   // login|logout => auth/login or auth/logout
        '<_c:[\w\-]+>' => '<_c>/index',                       // product => product/index
        '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',               // product/2 => product/view
        '<_c:[\w\-]+>/<_a:[\w-]+>' => '<_c>/<_a>',            // product/delete => product/delete
        '<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => '<_c>/<_a>',  // product/2/update => product/update,
//        '<_c:[\w\-]>' => '<>'

    // also we may from somewhere in app add our rule: Yii::$app->urlManager->addRules([...], true); // top or bottom

       /* [
            'class'   => 'yii\web\UrlManager',
            'pattern' => '<action:login>',
            'route'   => 'site/login',F
            'suffix'  => '.html',
            'verb' => 'GET',

        ],*/

       /*class PageUrlManager implements \yii\web\UrlRuleInterface
       {

           /**
            * Parses the given request and returns the corresponding route and parameters.
            * @param \yii\web\UrlManager $manager the URL manager
            * @param \yii\web\Request $request the request component
            * @return array|bool the parsing result. The route and the parameters are returned as an array.
            * If false, it means this rule cannot be used to parse this path info.
            */
           /*public function parseRequest($manager, $request)
           {
              // localhost/info
               $path = $request->pathInfo;
               if(preg_match('#^[\w-]+$#', $path)) {
                    if($page = Page::find()->where(['slug' => $path])->one()) {
                        return ['page/view', 'slug' => $page->slug];
                    }
               }
               return false;
           }*/

//           /**
//            * Creates a URL according to the given route and parameters.
//            * @param \yii\web\UrlManager $manager the URL manager
//            * @param string $route the route. It should not have slashes at the beginning or the end.
//            * @param array $params the parameters
//            * @return string|bool the created URL, or false if this rule cannot be used for creating this URL.
//            */
          /* public function createUrl($manager, $route, $params)
           {
               if($route == 'page/view') {
                   $url = $params['slug'];
                   unset($params['slug']);
                   if(!empty($params) && ($query = http_build_query($params) !== '')) {
                    $url .= '?'.$query;
                   }
                   return $url;
               }
               return false;
           }*/
//       }

    ],
];