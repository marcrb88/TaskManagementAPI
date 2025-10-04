<?php

namespace App\Application\Service;

use DateTime;

class DateFormatValidator
{
    public function validate(array $datesToValidate): bool
    {
        foreach ($datesToValidate as $dateField) {
            $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $dateField);
            if ($date === false) {
                return false;
            }
        }
        return true;
    }
}
