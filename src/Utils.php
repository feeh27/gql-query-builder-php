<?php

namespace GQLQueryBuilder;

class Utils
{

    public static function isNestedField($object)
    {
        return (isset($object["operation"]) && isset($object["fields"]) && isset($object["variables"]))
            || (isset($object["operation"]) && isset($object["fragment"]) && isset($object["fields"]));
    }

    public static function queryVariablesMap(?array $variables, array $fields = []): array
    {
        $variablesMapped = [];
        $update = function ($vars) use (&$variablesMapped) {
            if ($vars) {
                foreach ($vars as $key => $value) {
                    $variablesMapped[$key] = is_array($value) ? $value["value"] : $value;
                }
            }
        };

        $update($variables);
        if ($fields && is_array($fields)) {
            $update(self::getNestedVariables($fields));
        }
        return $variablesMapped;
    }

    public static function queryDataType($variable)
    {
        $type = "String";

        $value = is_array($variable) ? $variable["value"] : $variable;

        if (isset($variable["type"])) {
            $type = $variable["type"];
        } else {
            $candidateValue = is_array($value) ? $value[0] : $value;
            switch (gettype($candidateValue)) {
                case "object":
                    $type = "Object";
                    break;

                case "boolean":
                    $type = "Boolean";
                    break;

                case "integer":
                    $type = is_int($candidateValue) ? "Int" : "Float";
                    break;
            }
        }

        // set object based variable properties
        if (is_array($variable)) {
            if ($variable["list"] === true) {
                $type = "[$type]";
            } else if (is_array($variable["list"])) {
                $type = "[$type" . ($variable["list"][0] ? "!" : "") . "]";
            }

            if ($variable["required"]) {
                $type .= "!";
            }
        }

        return $type;
    }


    static function  queryFieldsMap(array $fields): string
    {
        $query = '';

        foreach ($fields as $name => $field) {

            if (is_string($field)) {
                $query .= $field . ' ';
                continue;
            }

            if (is_array($field)) {
                $query .= "$name { " . self::queryFieldsMap($field) . "} ";
                continue;
            }
        }

        return $query;
    }

    static function getDeepestVariables(array $innerFields, array $variables)
    {

        if ($innerFields) {
            foreach ($innerFields as $field) {
                if (Utils::isNestedField($field)) {
                    $variables = [
                        ...$variables,
                        ...$field['variables'],
                        ...self::getDeepestVariables($field['fields'], $variables)
                    ];
                } else {
                    if (is_array($field)) {
                        $variables = self::getDeepestVariables($field, $variables);
                    }
                }
            }
        }

        return $variables;
    }


    static function getNestedVariables(array $fields): array
    {
        $variables = [];
        return self::getDeepestVariables($fields, $variables);
    }

    static function resolveVariables(array $operations)
    {
        $ret = [];

        foreach ($operations as $operation) {
            $ret = [
                ...$ret,
                ...$operation['variables'],
                ...$operation['fields'] ? self::getNestedVariables($operation['fields']) : []
            ];
        }

        return $ret;
    }


    // Convert object to name and argument map. eg: (id: $id)
    public static function queryDataNameAndArgumentMap(array $variables)
    {
        if ($variables) {
            $dataString = '';
            foreach ($variables as $key => $value) {
                $dataString .= $value &&  isset($value['name']) ? $value['name'] : $key;
                $dataString .= ': $' . $key;
            }
            return '(' . $dataString . ')';
        }
        return '';
    }
}
