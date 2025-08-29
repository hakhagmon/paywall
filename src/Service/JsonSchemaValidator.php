<?php

namespace App\Service;

use App\Payment\Exceptions\ValidateException;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;
use PHPUnit\Framework\Exception;

class JsonSchemaValidator
{
    /**
     * @param string $data
     * @param string $jsonSchema
     * @return void
     * @throws ValidateException
     */
    public function validate(string $data, string $jsonSchema): void
    {
        $jsonSchema = json_decode($jsonSchema);
        $data = json_decode($data);

        $validator = new Validator;
        $validator->validate($data, $jsonSchema);

        if (!$validator->isValid()) {
            $validateErrorData = [];
            foreach ($validator->getErrors() as $error) {
                $validateErrorData[] = [
                    'code' => 'error_input_data',
                    'text' => sprintf("[%s] %s\n", $error['property'], $error['message'])
                ];
            }

            throw new ValidateException($validateErrorData);
        }

    }
}
