<?php

namespace App\Http\Utils;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RequestValidator
{
    protected $request;
    protected $path;

    public function __construct(Request $request, string $path)
    {
        $this->request = $request;
        $this->path = $path;

        $this->validate();
    }

    public function validate()
    {
        $validationRules = [
            'createSession' => [
                'email' => 'required|email|max:40',
                'password' => 'required|string'
            ],
            'searchGifts' => [
                'query' => 'required|string|max:40',
                'limit' => 'nullable|integer',
                'offset' => 'nullable|integer',
            ],
            'searchGift' => [
                'id' => 'required'
            ]
        ];

        $messages = [
            'required' => 'El valor :attribute es obligatorio.',
            'email' => 'El valor :attribute debe ser una dirección de correo electrónico válida.',
            'max' => 'El valor :attribute no debe tener más de :max caracteres.',
            'unique' => 'El valor :attribute ya está registrado.',
            'min' => 'El valor :attribute debe tener al menos :min caracteres.',
            'numeric' => 'El valor :attribute debe ser numérico.',
            'integer' => 'El valor :attribute debe ser un entero.',
            'string' => 'El valor :attribute debe ser una cadena de texto.',
            'digits' => 'El valor :attribute debe tener :digits dígitos.',
            'date' => 'La :attribute debe ser una fecha válida.',
            'date_format' => 'La :attribute debe seguir el formato acordado.',
            'array' => 'Las :attribute deben ser un arreglo valido.',
            'in' => 'El valor :attribute debe estar entre los valores acordados.',
            'required_with' => 'El valor :attribute es requerido.',
            'required_if' => 'El valor :attribute es requerido.',
            'exists' => 'El valor :attribute no existe en nuestros registros.',
            'regex' => 'El valor :attribute debe cumplir con los requisitos mínimos.',
        ];

        $attributes = [
            'email' => 'email',
            'password' => 'contraseña',
            'query' => 'query',
            'limit' => 'limit',
            'offset' => 'offset',
            'id' => 'id',
        ];

        $validator = Validator::make($this->request->all(), $validationRules[$this->path], $messages, $attributes);

        if($validator->fails()){
            $errors = collect($validator->errors())->flatMap(function ($messages, $field) {

                $error = is_array($messages) ? $messages[0] : $messages;

                if (strpos($field, ".") == true) {
                    $fieldParts = explode('.', $field);
                    $field = end($fieldParts);
                }

                return [
                    [
                        'field' => $field,
                        'error' => $error
                    ]
                ];
            })->toArray();

            throw new Exception(json_encode($errors), 400);
        }
    }
}

