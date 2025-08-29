<?php

namespace App\Service;

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;
use PHPUnit\Framework\Exception;

class JsonSchemaValidator
{
    public function validate(array $data, string $schema): true
    {
        $validator = new Validator();

        $schema = (object)json_decode($schema, true);

        $validator->validate($data, $schema, Constraint::CHECK_MODE_VALIDATE_SCHEMA);

        if ($validator->isValid()) {
            return true;
        }

        return throw new Exception('payment type not found', json_encode($validator->getErrors()));

    }
}
