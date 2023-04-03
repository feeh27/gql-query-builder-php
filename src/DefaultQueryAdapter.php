<?php

namespace GQLQueryBuilder;

class DefaultQueryAdapter
{

    private $variables;
    private $fields;
    private $operation;


    public function __construct(array $options)
    {

        if (array_is_list($options)) {
            $this->variables = Utils::resolveVariables($options);
        } else {
            if (isset($options['variables'])) {
                $this->variables = $options['variables'];
            }

            $this->fields = $options['fields'] ?? [];
            $this->operation = $options['operation'];
        }
    }

    // kicks off building for a single query
    public function queryBuilder()
    {
        return $this->operationWrapperTemplate(
            $this->operationTemplate($this->variables)
        );
    }

    // if we have an array of options, call this
    public function queriesBuilder($queries)
    {
        $tmp = [];

        foreach ($queries as $query) {
            if ($query) {
                $this->operation = $query['operation'];
                if (isset($query['fields'])) {
                    $this->fields = $query['fields'];
                }

                if (isset($query["variables"])) {
                    $this->variables = $query['variables'];
                }


                $tmp[] = $this->operationTemplate();
            }
        }

        return $this->operationWrapperTemplate(implode(", ", $tmp));
    }

    private function operationWrapperTemplate(string $content)
    {
        $query = "query";
        $query .= $this->queryDataArgumentAndTypeMap() . " { " . $content . "}";

        return [
            "query" => $query,
            "variables" => Utils::queryVariablesMap($this->variables, $this->fields),
        ];
    }

    // Convert object to argument and type map. eg: ($id: Int)
    private function  queryDataArgumentAndTypeMap(): string
    {
        $variablesUsed = $this->variables ?? [];


        if ($this->fields && is_array($this->fields)) {
            $variablesUsed = array_merge($variablesUsed, Utils::getNestedVariables($this->fields));
        }
        if (count($variablesUsed) > 0) {

            $s = [];
            foreach ($variablesUsed as $key => $value) {
                $s[] = '$' . $key . ': ' . Utils::queryDataType($value);
            }
            return '(' . implode(', ', $s) . ')';
        } else {
            return '';
        }
    }

    // query

    private function operationTemplate(?array $variables = null)
    {
        $operation = is_string($this->operation) ? $this->operation : $this->operation['alias'] . ': ' . $this->operation['name'];

        return $operation . ($variables ? Utils::queryDataNameAndArgumentMap($variables) : '') . ($this->fields && (count($this->fields) > 0) ? ' { ' . Utils::queryFieldsMap($this->fields) . ' } ' : '');
    }
}
