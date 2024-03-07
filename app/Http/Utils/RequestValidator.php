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
            'searchExternalGifts' => [
                'query' => 'required|string|max:40',
                'limit' => 'nullable|integer',
                'offset' => 'nullable|integer',
            ],
            'searchExternalGift' => [
                'id' => 'required'
            ],
            'searchInternalGifts' => [
                'query' => 'required|string|max:40',
                'limit' => 'nullable|integer',
                'offset' => 'nullable|integer',
            ],
            'setFavouriteGift' => [
                'id' => 'required|integer|exists:cat_gifts,id',
                'alias' => 'required|string',
                'user_id' => 'required|integer|exists:prex_user,id',
            ]
        ];

        $messages = [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'max' => 'The :attribute must not be more than :max characters.',
            'unique' => 'The :attribute has already been taken.',
            'min' => 'The :attribute must be at least :min characters.',
            'numeric' => 'The :attribute must be a number.',
            'integer' => 'The :attribute must be an integer.',
            'string' => 'The :attribute must be a string.',
            'digits' => 'The :attribute must have :digits digits.',
            'date' => 'The :attribute must be a valid date.',
            'date_format' => 'The :attribute must follow the agreed format.',
            'array' => 'The :attribute must be a valid array.',
            'in' => 'The :attribute must be one of the following values.',
            'required_with' => 'The :attribute is required when :other is present.',
            'required_if' => 'The :attribute is required when :other is :value.',
            'exists' => 'The selected :attribute is invalid.',
            'regex' => 'The :attribute format is invalid.',
        ];

        $attributes = [
            'email' => 'email',
            'password' => 'password',
            'query' => 'query',
            'limit' => 'limit',
            'offset' => 'offset',
            'id' => 'gift id',
            'alias' => 'alias',
            'user_id' => 'user id',
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

