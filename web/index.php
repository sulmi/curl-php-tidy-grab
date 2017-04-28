<?php

namespace web\GetPage;

include_once '../GetPageClass.php';

use Page\GetPageClass as PageClass;
use tidyNode;

$o = new PageClass([
    'url' => 'https://www.debian.org/',
    'cookie' => 'debian.txt',
        ]);
$body = $o->GoPage()->body();

//$o1 = new PageClass([
//    'url' => 'http://oferty.praca.gov.pl/portal/index.cbop#/listaOfert',
//    'cookie' => 'offers1.txt',
//        ]);
//$body1 = $o1->GoPage()->body();
//echo($body1);
//
//$o2 = new PageClass([
//    'url' => 'http://oferty.praca.gov.pl/portal/oferta/wyszukaj.cbop',
//    'cookie' => 'offers1.txt',
//        ]);
//$o2->setHttpHeader(['Accept: application/json, text/javascript, */*; q=0.01',
//    'X-Requested-With: XMLHttpRequest'
//    ]);
//$referer='Referer: http://oferty.praca.gov.pl/portal/index.cbop';
//$o2->setReferer($referer);
//$o2->GoPage();
//var_dump($o2);
//die;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <body>
        <div>
            <?php
            if ($body instanceof tidyNode) {
                $key = 'offersLink';
                $o->findRegEx($body, 'a', '', 'href', '/security\//', $key);
            }
            $count = $o->dataSearchCount($key);
            var_dump($o->getDataSearchKey($key));
            die;
            if ($count > 0) {
                foreach ($o->getDataSearchKey($key) as $row) {
//        $row =new \tidyNode();
                    echo($row->getParent()->getParent()->child[4]->child[0]) . ' ';
                    echo($row->getParent()->getParent()->child[2]->child[0]) . ' ';
                    echo($row->getParent()->getParent()->child[1]->child[0]) . ' ';
//        echo($row->child[0]).'';
                    echo('<a href="' . $o->hostUrl . $row->attribute['href'] . '">Czytaj</a><br>');
                }
            }
//var_dump($o->dataSearchCount($key));
//die;
//if ($body1 instanceof tidyNode) {
//    $key = 'pracagovpl';
//    $o1->findRegEx($body1, 'div', '', 'class', '/oferta\-pozycja\-max\-tabela/', $key);
//}
//$count = $o1->dataSearchCount($key);
//if ($count > 0) {
//    foreach ($o1->getDataSearchKey($key) as $row) {
////        $row =new \tidyNode();
//        echo($row->getParent()->getParent()->child[4]->child[0]) . ' ';
//        echo($row->getParent()->getParent()->child[2]->child[0]) . ' ';
//        echo($row->getParent()->getParent()->child[1]->child[0]) . ' ';
////        echo($row->child[0]).'';
//        echo('<a href="' . $o->hostUrl . $row->attribute['href'] . '">Czytaj</a><br>');
//    }
//}
////var_dump($o->dataSearchCount($key));
////die;
//if ($body2 instanceof tidyNode) {
//    $key = 'pracagovpl2';
//    $o2->findRegEx($body2, 'div', '', 'class', '/oferta\-pozycja\-max\-tabela/', $key);
//}
//$count = $o2->dataSearchCount($key);
//if ($count > 0) {
//    foreach ($o2->getDataSearchKey($key) as $row) {
////        $row =new \tidyNode();
//        echo($row->getParent()->getParent()->child[4]->child[0]) . ' ';
//        echo($row->getParent()->getParent()->child[2]->child[0]) . ' ';
//        echo($row->getParent()->getParent()->child[1]->child[0]) . ' ';
////        echo($row->child[0]).'';
//        echo('<a href="' . $o->hostUrl . $row->attribute['href'] . '">Czytaj</a><br>');
//    }
//}
//var_dump($o->dataSearchCount($key));
//die;
            ?>
        </div>
    </body>
</html>
