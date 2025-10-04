<?php

namespace App\Application\Service;

use DateTime;

class DateFormatValidator
{
    public function validate(array $datesToValidate): bool
    {
        foreach ($datesToValidate as $dateField) {
            try {
                new DateTime($dateField);
            } catch (\Exception $e) {
                return false; 
            }
        }
        return true;
    }
}
