### jrMvc (JackRabbitMvc) is a one-file MVC micro-framework for PHP5.

>"A designer knows he has achieved perfection not when there is nothing left
to add, but when there is nothing left to take away."
(Antoine de Saint-Exupery)

See [demo](http://dexygen.com/jrmvc/)

#### index.php:
```
<?php
require('jrmvc.lib.php');

class DemoController extends AbstractJrMvcController {
  function applyInputToModel() {
    $mto = new JrMvcMTO('demo.tpl.php');
    
    $mto->setModelValue('foo', 'bar');
    $mto->setModelValue('hello', 'world');
    $mto->setModelValue('what', 'ever');

    return $mto;
  }
}

DemoController::sendResponse(new DemoController());
?>
```

#### demo.tpl.php
```
<pre>
$model:
<?php print_r($model); ?>
</pre>
```
