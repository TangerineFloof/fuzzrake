<?php

declare(strict_types=1);

namespace App\Utils\Data\Validator;

use App\DataDefinitions\Fields\Field;

class GenericValidator implements ValidatorInterface
{
    public function isValid(Field $field, $subject): bool
    {
        return $field->validationPattern()->test($subject);
    }
}
