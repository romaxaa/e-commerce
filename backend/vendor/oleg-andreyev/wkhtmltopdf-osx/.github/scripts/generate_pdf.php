<?php

require __DIR__ . '/../../vendor/autoload.php';

use Knp\Snappy\Pdf;
use OAndreyev\WKHTMLToPDF\WKHTMLToPDF;

$snappy = new Pdf();
$arch = php_uname('m');
if ($arch === 'x86_64') {
    $snappy->setBinary(WKHTMLToPDF::PATH_AMD64);
} else {
    $snappy->setBinary(WKHTMLToPDF::PATH_I368);
}
echo $snappy->getOutputFromHtml('<p>foobar</p>');
