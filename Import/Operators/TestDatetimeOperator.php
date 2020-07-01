<?php

namespace Sintra\TrainingBundle\Import\Operators;

use Sintra\TrainingBundle\Import\ColumnConfig\Operator\FieldSetter;


class TestDatetimeOperator extends FieldSetter{
    
    protected $additionalData;
    
    public function __construct(\stdClass $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->additionalData = json_decode($config->additionalData,true);
    }
    
    /**
     * Search the picklist entry by the display name 
     * and retrieve the corresponding picklist value
     */
    public function process($element, &$target, array &$rowData, $colIndex, array &$context = array()) {  
        
        $value = $rowData[$colIndex];
        
        $field = $this->getFieldname();
        $format = $this->additionalData["format"];
        
        if($format == null || empty($format)){
            $format = 'Y-m-d';
        }
        
        if($this->validateDate($value, $format)){
            $reflection = new \ReflectionObject($target);
            $setFieldMethod = $reflection->getMethod('set'. ucfirst($field));
            $setFieldMethod->invoke($target, $this->convertToTimestamp($value, $format));
        }else{
            throw new \Exception("Invalid date for format '$format' and date '$value'");
        }
    }
    
    function validateDate($date, $format)
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
    
    function convertToTimestamp($date, $format){
        $d = \DateTime::createFromFormat($format, $date);
        
        return $d;
    }

}
