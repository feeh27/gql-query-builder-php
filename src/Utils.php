<?php

namespace GQLQueryBuilder;

class Utils
{
    public static function operationOrAlias($operation)
    {
        if (is_string($operation)) {
            return $operation;
        }

        if (isset($operation["alias"]) && isset($operation["name"])) {
            return $operation["alias"] . " " . $operation["name"];
        }
    }

    public static function operationOrFragment($field)
    {
        return self::isFragment($field) ? $field["operation"] : self::operationOrAlias($field["operation"]);
    }

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
            if (isset($variable["list"])) {
                if ($variable["list"] === true) {
                    $type = "[$type]";
                } else if (is_array($variable["list"])) {
                    $type = "[$type" . ($variable["list"][0] ? "!" : "") . "]";
                }
            }

            if (isset($variable["required"]) && $variable["required"]) {
                $type .= "!";
            }
        }

        return $type;
    }

    public static function isFragment($field)
    {
        return is_array($field) && isset($field["fregment"]) && $field["fregment"] === true;
    }

    public static function getFragment($field): string
    {
        return self::isFragment($field) ? "... on" : "";
    }

    public static function queryNestedFieldMap($field)
    {
        return self::getFragment($field) . self::operationOrFragment($field) . " " .
            (self::isFragment($field) ? "" : self::queryDataNameAndArgumentMap($field["variables"])) . " " .
            ($field["fields"] ? " { " . self::queryFieldsMap($field["fields"]) . " } " : "");
    }


    static function queryFieldsMap(array $fields): string
    {
        $ret = [];

        foreach ($fields as $name => $field) {

            if (self::isNestedField($field)) {
                $ret[] = self::queryNestedFieldMap($field);
                continue;
            }

            if (is_array($field)) {

                if (count($field) === 0) {
                    $ret[] = $name;
                    continue;
                }

                $ret[] = "$name { " . self::queryFieldsMap($field) . " }";
                continue;
            }

            if (is_string($field)) {
                $ret[] = $field;
                continue;
            }
        }

        return implode(", ", $ret);
    }

    static function getDeepestVariables(array $innerFields, array $variables)
    {

        if ($innerFields) {
            foreach ($innerFields as $field) {
                if (Utils::isNestedField($field)) {

                    $variables = array_merge(
                        $variables,
                        $field['variables'],
                        self::getDeepestVariables($field['fields'], $variables)
                    );
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

            if (isset($operation['variables'])) {
                $ret = array_merge($ret, $operation['variables']);
            }

            if (isset($operation['fields'])) {
                $ret = array_merge($ret, self::getNestedVariables($operation['fields']));
            }
        }

        return $ret;
    }


    // Convert object to name and argument map. eg: (id: $id)
    public static function queryDataNameAndArgumentMap(array $variables)
    {
        if ($variables) {
            $dataString = [];
            foreach ($variables as $key => $value) {
                $s = $value &&  isset($value['name']) ? $value['name'] : $key;
                $s .= ': $' . $key;

                $dataString[] = $s;
            }
            return '(' . implode(", ", $dataString) . ')';
        }
        return '';
    }
}
