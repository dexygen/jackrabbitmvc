<?php
  /*
   * jrMvc (JackRabbitMvc -- formerly barebonesmvc-php) 
   * is a one-file MVC micro-framework for PHP5.
   * 
   * Copyright (c) 2007-2019, George M. Jempty
   *
   * "A designer knows he has achieved perfection not when there is nothing left
   * to add, but when there is nothing left to take away."
   * (Antoine de Saint-Exupery)
   *
   * LICENSE: https://opensource.org/licenses/MIT
   *
   * USAGE:
   *
   <?php
     require('jrmvc.lib.php');                              // 1) require library                           

     class DemoMTO extends JrMvcMTO {                       // 1a) optionally extend MTO, ideal when not using template, e.g. JSON
       function onNullView() {
         echo json_encode($this->model);                    
         // Instead a binary such as an xls or pdf could be sent
       }
     }

     class DemoController extends AbstractJrMvcController { // 2) extend Controller  
        function applyInputToModel() {                      // 3) implement only required method
           // Sample demo.tpl.php content: <pre>$model: <?php print_r($model); ?></pre>
           $mto = new JrMvcMTO('demo.tpl.php');             // 4) instantiate
           // To output json instead use extended MTO above: $mto = new DemoMTO(JrMvcMTO::NULL_VIEW);
                     
           $mto->setModelValue('Su', 'Sunday');             // 5) assignments              
           $mto->setModelValue('Mo', 'Monday');
           $mto->setModelValue('Tu', 'Tuesday');
           $mto->setModelValue('We', 'Wednesday');
           $mto->setModelValues(['Th'=>'Thursday', 'Fr'=>'Friday', 'Sa'=>'Saturday']);

           return $mto;                                     // 6) return MTO
         }
     }

     DemoController::sendResponse(new DemoController());    // 7) send response
   *
   * OUTPUT:
   *
     $model: Array
      (
      [Su] => Sunday
      [Mo] => Monday
      [Tu] => Tuesday
      [We] => Wednesday
      [Th] => Thursday
      [Fr] => Friday
      [Sa] => Saturday
      )
   */ 

  interface IJrMvcController {
    function setMto(IModelXfer $mto);
    static function sendResponse(IJrMvcController $controller);
    function applyInputToModel();
  }
  
  abstract class AbstractJrMvcController implements IJrMvcController {
    protected $mto;
    
    function setMto(IModelXfer $mto) {
      $this->mto = $mto;
    }
    
    static function sendResponse(IJrMvcController $controller) {
      $controller->setMto($controller->applyInputToModel());
      $controller->mto->applyModelToView();
    }
  }
  
  interface IModelXfer {
    function setView($view);
    function setModel($model);
    function setModelValue($key, $value);
    function applyModelToView();
  }

  abstract class AbstractMTO implements IModelXfer {
    protected $view;
    protected $model;
    const NULL_VIEW = null;
    
    function setView($view) {
      $this->view = $view;    
    }
    
    function setModel($model) {
      $this->model = $model;
    }
    
    function setModelValue($key, $value) {
      $this->model[$key] = $value;
    }
    
    function setModelValues($arr) {
      foreach ($arr as $key => $value) {
        $this->model[$key] = $value;
      }
    }
    
    protected function unsetNonSessionGlobals() {
      $session = $GLOBALS['_SESSION'];
      unset($GLOBALS);
      $GLOBALS['_SESSION'] = $session;      
    }
    
    protected function onNullView() {}
  }
  
  class JrMvcMto extends AbstractMTO {    
    function __construct($view) {
      $this->setView($view);
    }
    
    function applyModelToView() {
      # Ensures view does not have access to get/post variables,
      # thus encouraging all access to them to occur within controller
      $this->unsetNonSessionGlobals();
      
      if (is_null($this->view)) {
        $this->onNullView();
      }
      else {
        $model = $this->model;
        include($this->view); 
      }
    }    
  }
?>