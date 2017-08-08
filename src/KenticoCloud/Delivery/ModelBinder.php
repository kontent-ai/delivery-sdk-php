<?php

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Helpers\TextHelper;

class ModelBinder
{
    public function bindModel($modelType, $data)
    {
        $model = new $modelType();
        $modelProperties = get_object_vars($model);
        

        if (is_string($data)) {
            $data = json_decode($data);
        }
        
        if (is_object($data)) {
            $dataProperties = get_object_vars($data);
        } else {
            // Assume it's an array
            $dataProperties = $data; 
        }
        
        foreach ($modelProperties as $modelProperty => $modelPropertyValue) {
            $dataProperty = $dataProperties[TextHelper::getInstance()->decamelize($modelProperty)];
            
            $model->$modelProperty = $dataProperty;
        }
        return $model;
    }
}