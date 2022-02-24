<?php

namespace Darkink\AuthorizationServer\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait _HasParent
{

    protected function getFields($entity_fields, $table)
    {
        $fields = empty($entity_fields) ? Schema::getColumnListing($table) : $entity_fields;
        $fields = array_map(function ($p) use ($table) {
            return "$table.$p";
        }, $fields);
        return $fields;
    }

    public function newQuery()
    {
        $query = parent::newQuery();

        $parent = $this->newSelfInstance();
        $child_table = $this->table;
        $child_key = $this->getKeyName();
        $parent_table = $parent->table;
        $parent_key = $parent->getKeyName();

        $fields = array_merge(
            $this->getFields($parent->entity_fields, $parent_table),
            ["$child_table.*",]
        );

        $query = $query
            ->join($parent_table, "$child_table.$child_key", '=', "$parent_table.$parent_key")
            ->addSelect($fields);

        return $query;
    }

    protected function insertAndSetId(Builder $query, $attributes)
    {
        $attributes = $this->inheritanceInsertAndSetId($query, $attributes);
        parent::insertAndSetId($query, $attributes);
    }

    protected function performInsert(Builder $query)
    {
        $this->inheritancePerformInsert($query);
    }

    protected function performUpdate(Builder $query)
    {
        $this->inheritancePerformUpdate($query);
    }
}
