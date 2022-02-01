<?php

namespace Darkink\AuthorizationServer\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\VarDumper\VarDumper;

class InheritanceBuilder extends Builder
{

    protected array $child_classes = [];

    public function __construct(QueryBuilder $query, array $child_classes)
    {
        parent::__construct($query);
        $this->child_classes = $child_classes;
    }

    protected function getModelBuilders()
    {
        $result = [];

        foreach ($this->child_classes as $child_class) {
            $modelBuilder = new $child_class();
            $key = $modelBuilder->getTable() . '-' . $modelBuilder->getKeyName();
            $result[$key] = $modelBuilder;
        }

        return $result;
    }

    protected function getColumnListing($table)
    {
        return Schema::getColumnListing($table);
    }

    protected function getAttributePath($table, $column)
    {
        return $table . '-' . $column;
    }

    public function getModels($columns = ['*'])
    {
        $result = [];
        $modelBuilders = $this->getModelBuilders();

        $datas = $this->query->get($columns)->all();
        foreach ($datas as $data) {
            $builder = $this->model;

            foreach ($modelBuilders as $key => $modelBuilder) {
                $table = $modelBuilder->getTable();
                $primaryKey = $modelBuilder->getKeyName();
                $columns = $this->getColumnListing($table);

                if ($data->{$key} != null) {
                    $builder = $modelBuilder;

                    foreach ($columns as $column) {
                        $attributePath = $this->getAttributePath($table, $column);
                        if ($column != $primaryKey) {
                            $data->{$column} = $data->{$attributePath};
                        }
                    }
                }

                foreach ($columns as $column) {
                    $attributePath = $this->getAttributePath($table, $column);
                    unset($data->{$attributePath});
                }
            }

            $model = $builder->newFromBuilder($data);
            $result[] = $model;
        }

        return $result;
    }
}
