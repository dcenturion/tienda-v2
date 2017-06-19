<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class Button {
    //Constants
    const WIDTH_MATCH_PARENT = 12;
    const WIDTH_HALF_PARENT = 6;
    const WIDTH_AUTO = 1;
    
    public $disabled    = false;
    public $name        = "defaultName";
    public $text        = "null";
    public $type        = "button";
    public $waveEffect  = true;
    public $waveLight   = true;
    public $width       = Button::WIDTH_MATCH_PARENT;
    
    //Public methods 
    public function __construct() {
        
    }
    
    public function asArray(){
        return $this->getSettings();
    }
    
    public function asJSON(){
        $settings = $this->getSettings();
        
        return json_encode($settings);
    }
    
    //Private methods
    private function getSettings(){
        $mySettings = [
            "disabled"      => $this->disabled,
            "name"          => $this->name,
            "text"          => $this->text,
            "type"          => $this->type,
            "waveEffect"    => $this->waveEffect,
            "waveLight"     => $this->waveLight,
            "width"         => $this->width
        ];
        
        return $mySettings;
    }
}