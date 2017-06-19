<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class RadioButtonGroup {
    //Constants
    const WIDTH_MATCH_PARENT = 12;
    const WIDTH_HALF_PARENT = 6;

    public $name    = "defaultName";
    public $options = []; //Array of CheckBox Class
    public $width   = RadioButtonGroup::WIDTH_MATCH_PARENT;
    
    //Public methods 
    public function __construct() {
        
    }
    
    public function addRadioButton(RadioButton $radioButton){
        $this->options []= $radioButton;
        
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
    private function getRadioButtonsProperties(){
        //Recover all elements
        $elements = [];
        
        foreach ($this->options as $radioButton){
            $elements []= $radioButton->asArray();
        }
        
        return $elements;
    }
    
    private function getSettings(){
        $mySettings = [
            "name"      => $this->name,
            "options"   => $this->getRadioButtonsProperties(),
            "width"     => $this->width
        ];
        
        return $mySettings;
    }
}