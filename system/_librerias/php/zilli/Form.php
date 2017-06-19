<?php
/**
 *  @author Aaron Nuñez
 *  @version 0.0.1
 */
class Form {
    public $container   = null;
    public $elements    = [];
    public $id          = "defaultFormId";
    public $params      = [];
    public $requestURI  = null;
    public $width       = 500;
    
    //Public methods 
    public function __construct() {
        
    }
    
    public function addElement($element){
        $this->elements[$element->name]= $element;
        
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
    private function getElementsProperties(){
        //Recover all elements
        $elements = [];
        
        foreach ($this->elements as $element){
            $typeElement = get_class($element);
            $elementProperties = $element->asArray();
            
            switch ($typeElement){
                case "Switch_":
                    $typeElement = "Switch";
                    break;
            }
            
            switch ($typeElement){
                case "Button":
                case "CheckBox":
                case "CheckBoxGroup":
                case "DatePicker":
                case "EditText":
                case "RadioButton":
                case "RadioButtonGroup":
                case "SelectBox":
                case "Switch":
                case "TextArea":
                case "TimePicker":
                    //Add Element like Array
                    $elements [] = [
                        "name"          => $elementProperties["name"],
                        "type"          => $typeElement,
                        "properties"    => $elementProperties
                    ];
                    break;
            }
        }
        
        return $elements;
    }
    
    private function getSettings(){
        //Recover my settings
        $mySettings = [
            "containerId"     => $this->container,
            "elements"      => $this->getElementsProperties(),
            "id"            => $this->id,
            "params"        => $this->params,
            "requestURI"    => $this->requestURI,
            "width"         => $this->width
        ];
        
        return $mySettings;
    }
}