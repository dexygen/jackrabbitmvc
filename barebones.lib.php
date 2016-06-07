<?php
  /*
   * BareBones is a one-file, no-configuration, MVC framework for PHP5.
   *
   * "A designer knows he has achieved perfection not when there is nothing left
   * to add, but when there is nothing left to take away."
   * (Antoine de Saint-Exupery) 
   * 
   *
   * (CONTROLLER) USAGE:
   *
   * require('barebones.lib.php');                             // 1) require
   * 
   * class DemoController extends AbstractBareBonesController {// 2) extend
   *   function applyInputToModel() {                          // 3) implement
   *     $mto = new BareBonesMTO('barebones.tpl.php');         // 4) instantiate
   *     $mto->setModelValue('message', 'bare bones demo');    // 5) assign
   *     session_start(); $_SESSION['preserved'] = 1;          // 6) preserve
   *     return $mto;                                          // 7) return
   *   }
   * }
   * 
   * DemoController::sendResponse(new DemoController());       // 8) send
   *
   *Copyright (c) 2007, George M. Jempty
   *
   *  https://opensource.org/licenses/MIT
   * 
   */  

  interface IBareBonesController {
    function setMto(IModelXfer $mto);
    static function sendResponse(IBareBonesController $controller);
    function applyInputToModel();
  }
  
  abstract class AbstractBareBonesController implements IBareBonesController {
    protected $mto;
    
    function setMto(IModelXfer $mto) {
      $this->mto = $mto;
    }
    
    static function sendResponse(IBareBonesController $controller) {
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
    
    protected function preserveSession() {
      $session = $GLOBALS['_SESSION'];
      unset($GLOBALS);
      $GLOBALS['_SESSION'] = $session;      
    }
  }
  
  class BareBonesMto extends AbstractMTO {    
    function __construct($view) {
      $this->setView($view);
    }
    
    function applyModelToView() {
      $this->preserveSession();
      $model = $this->model;
      include($this->view);
    }    
  }
?>
