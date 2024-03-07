<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

interface IFormater
{
    static function format(array $toFormat, string $path, array $allowedFields): array;
}


class ResponseFormater implements IFormater
{
    static function format(array $toFormat, string $path, array $allowedFields): array
    {
        $formattedData = [];

        foreach($toFormat[$path] as $k => $dataItem) {
            $formattedItem = [];

            foreach ($allowedFields as $originalField => $formattedField) {

                $needOriginalFieldName = is_numeric($originalField);

                if($needOriginalFieldName) {
                    $originalField = $formattedField;
                }

                $fieldPath = explode('->', $originalField);
                $currentData = $dataItem;

                foreach ($fieldPath as $pathPart) {
                    if (isset($currentData[$pathPart])) {
                        $currentData = $currentData[$pathPart];
                    } else {
                        $currentData = null;
                        break;
                    }
                }

                if ($currentData !== null) {
                    $formattedItem[$formattedField] = $currentData;
                }
            }

            $formattedData[] = $formattedItem;
        }

        return $formattedData;
    }
}
