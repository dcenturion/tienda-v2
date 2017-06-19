<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class CheckBoxGroup {
    //Constants
    const WIDTH_MATCH_PARENT = 12;
    const WIDTH_HALF_PARENT = 6;

    public $name            = "defaultName";
    public $options         = []; //Array of CheckBox Class
    public $width           = CheckBoxGroup::WIDTH_MATCH_PARENT;
    
    //Public methods 
    public function __construct() {
        
    }
    
    public function addCheckBox(CheckBox $checkBox){
        $this->options []= $checkBox;
        
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
    private function getCheckBoxesProperties(){
        //Recover all elements
        $elements = [];
        
        foreach ($this->options as $checkBox){
            $elements []= $checkBox->asArray();
        }
        
        return $elements;
    }
    
    private function getSettings(){
        $mySettings = [
            "name"      => $this->name,
            "options"   => $this->getCheckBoxesProperties(),
            "width"     => $this->width
        ];
        
        return $mySettings;
    }
}