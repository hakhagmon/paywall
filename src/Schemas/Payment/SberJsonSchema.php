<?php

namespace App\Schemas\Payment;

class SberJsonSchema
{

    const POST_REQUEST = <<< 'JSON'
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "object",
  "required": [
    "token",
    "status",
    "order_id",
    "amount",
    "currency",
    "error_code",
    "pan",
    "user_id",
    "language_code"
  ],
  "properties": {
    "token": {
      "type": "string",
      "format": "uuid",
      "description": "Уникальный идентификатор транзакции"
    },
    "status": {
      "type": "string",
      "enum": ["authorized", "confirmed", "rejected", "refunded"],
      "description": "Статус операции"
    },
    "order_id": {
      "type": "integer",
      "minimum": 1,
      "description": "Идентификатор заказа"
    },
    "amount": {
      "type": "integer",
      "minimum": 0,
      "description": "Сумма операции в минимальных единицах валюты"
    },
    "currency": {
      "type": "string",
      "enum": ["RUB", "USD", "EUR"],
      "description": "Валюта операции"
    },
    "error_code": {
      "type": ["integer", "null"],
      "description": "Код ошибки или null при успешной операции"
    },
    "pan": {
      "type": "string",
      "pattern": "^[0-9*]+$",
      "description": "Маскированный номер карты"
    },
    "user_id": {
      "type": "string",
      "description": "Идентификатор пользователя Telegram"
    },
    "language_code": {
      "type": "string",
      "enum": ["ru", "en"],
      "description": "Код языка"
    }
  },
  "additionalProperties": false
}
JSON;
}
