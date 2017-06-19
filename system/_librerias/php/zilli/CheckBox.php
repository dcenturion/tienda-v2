<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class CheckBox {
    //Constants
    const WIDTH_MATCH_PARENT = 12;
    const WIDTH_HALF_PARENT = 6;

    public $checked         = false;
    public $disabled        = false;
    public $effectFilledIn  = true;
    public $id              = null;
    public $labelText       = "defaultLabelText";
    public $name            = "defaultName";
    public $value           = null;
    public $width           = CheckBox::WIDTH_MATCH_PARENT;
    
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
            "effectFilledIn"    => $this->effectFilledIn,
            "id"                => $this->id,
            "labelText"         => $this->labelText,
            "name"              => $this->name,
            "value"             => $this->value,
            "width"             => $this->width
        ];
        
        return $mySettings;
    }
}