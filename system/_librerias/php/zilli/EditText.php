<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class EditText {
    //Constants
    const WIDTH_MATCH_PARENT = 12;
    const WIDTH_HALF_PARENT = 6;
    
    public $disabled    = false;
    public $id          = null;
    public $labelText   = null;
    public $name        = "defaultName";
    public $type        = "text";
    public $validate    = false;
    public $value       = null;
    public $width       = EditText::WIDTH_MATCH_PARENT;
    
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
            "id"            => $this->id,
            "labelText"     => $this->labelText,
            "name"          => $this->name,
            "type"          => $this->type,
            "validate"      => $this->validate,
            "value"         => $this->value,
            "width"         => $this->width
        ];
        
        return $mySettings;
    }
}