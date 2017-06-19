<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class Switch_ {
    public $checked             = false;
    public $disabled            = false;
    public $disableLabelText    = "disableLabelTextDefault";
    public $enableLabelText     = "enableLabelTextDefault";
    public $name                = "defaultName";
    
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
            "checked"           => $this->checked,
            "disabled"          => $this->disabled,
            "disableLabelText"  => $this->disableLabelText,
            "enableLabelText"   => $this->enableLabelText,
            "name"              => $this->name,
        ];
        
        return $mySettings;
    }
}