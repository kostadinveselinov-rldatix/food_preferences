<?php

namespace App\Validator;

class Validator
{
    private $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function checkForErrors():bool
    {
        return count($this->errors) > 0;
    }

    public function validate(array $data, array $rules): bool
    {
         foreach ($rules as $field => $ruleString) {
            if(!isset($data[$field])){
                $this->errors[$field][] = "$field is missing from request.";
                continue;
            }

            $rulesArray = explode('|', $ruleString);
            foreach ($rulesArray as $rule) {
                if($rule == "empty" && empty($data[$field])){
                    break; // don't check other rules if field can be left empty
                }

                if ($rule == 'required' && empty($data[$field])) {
                    $this->errors[$field][] = "$field is required.";
                }

                if ($rule == 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "$field must be a valid email.";
                }

                if($rule == 'string' && !is_string($data[$field])) {
                    $this->errors[$field][] = "$field must be a string.";
                }

                if($rule == 'array') {
                    if(!is_array($data[$field])){
                        $this->errors[$field][] = "$field must be numeric.";
                    }else if(count($data[$field]) == 0){
                        $this->errors[$field][] = "$field array cannot be empty.";
                    }
                }

                if($rule == "numeric" && !is_numeric($data[$field])) {
                    $this->errors[$field][] = "$field must be numeric.";
                }

                if($rule == "numeric_array") {
                    if(!is_array($data[$field])){
                        $this->errors[$field][] = "$field must be an array.";
                    }else if(!$this->validateIntegerValuesInArray($data[$field]) || count($data[$field]) == 0){
                        $this->errors[$field][] = "$field must be an array of numeric values.";
                    }
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = explode(':', $rule)[1];
                    if (strlen($data[$field]) < $min) {
                        $this->errors[$field][] = "$field must be at least $min characters.";
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = explode(':', $rule)[1];
                    if (strlen($data[$field]) > $max) {
                        $this->errors[$field][] = "$field must be smaller than $max characters.";
                    }
                }
            }
        }

        return $this->checkForErrors();
    }   

    private function validateIntegerValuesInArray(array $data):bool
    {
        foreach($data as $value){
            if(!is_numeric($value)){
               return false;
            }
        }
        return true;
    }

    public function getOnlyErrorMessages():array
    {
        $errorMessages = [];
        foreach($this->errors as $field => $messages){
            foreach($messages as $message){
                $errorMessages[] = $message;
            }
        }
        return $errorMessages;
    }

    ///
}