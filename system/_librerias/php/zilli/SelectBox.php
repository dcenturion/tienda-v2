<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class SelectBox {
    //Constants
    const WIDTH_MATCH_PARENT = 12;
    const WIDTH_HALF_PARENT = 6;
    
    public $disabled        = false;
    public $id              = null;
    public $indexOption     = 0;
    public $labelText       = null;
    public $name            = "defaultName";
    public $options         = [];
    public $width           = SelectBox::WIDTH_MATCH_PARENT;
    
    //Public methods 
    public function __construct() {
        
    }
    
    public function addValue($value, $disabled = false){
        $this->options []= [
            "disabled" => $disabled,
            "value"    => $value
        ];
        
        return $this;
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
            "disabled"          => $this->disabled,
            "id"                => $this->id,
            "indexOption"       => $this->indexOption,
            "labelText"         => $this->labelText,
            "name"              => $this->name,
            "options"           => $this->options,
            "width"             => $this->width
        ];
        
        return $mySettings;
    }
}