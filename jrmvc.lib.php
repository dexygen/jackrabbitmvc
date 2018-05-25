<?php
  /*
   * jrMvc (JackRabbitMvc) is a one-file MVC micro-framework for PHP5.
   * 
   * Copyright (c) 2007-2018, George M. Jempty
   *
   * https://opensource.org/licenses/MIT
   *
   * "A designer knows he has achieved perfection not when there is nothing left
   * to add, but when there is nothing left to take away."
   * (Antoine de Saint-Exupery)
   *
   * (CONTROLLER) USAGE:
   *
   * require('jrmvc.lib.php');                               // 1) require
   * 
   * class DemoController extends AbstractJrMvcController {  // 2) extend
   *   function applyInputToModel() {                        // 3) implement
   *     $mto = new JrMvcMTO('jrmvc.tpl.php');               // 4) instantiate
   *     $mto->setModelValue('message', 'bare bones demo');  // 5) assign
   *     // Display in template with $model['message']
   *     return $mto;                                        // 6) return
   *   }
   * }
   * 
   * DemoController::sendResponse(new DemoController());     // 7) send
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
    
    function setView($view) {
      $this->view = $view;    
    }
    
    function setModel($model) {
      $this->model = $model;
    }
    
    function setModelValue($key, $value) {
      $this->model[$key] = $value;
    }
    
    protected function unsetMostGlobals() {
      $session = $GLOBALS['_SESSION'];
      unset($GLOBALS);
      $GLOBALS['_SESSION'] = $session;      
    }
  }
  
  class JrMvcMto extends AbstractMTO {    
    function __construct($view) {
      $this->setView($view);
    }
    
    function applyModelToView() {
      $this->unsetMostGlobals();
      $model = $this->model;
      include($this->view);
    }    
  }
?>
