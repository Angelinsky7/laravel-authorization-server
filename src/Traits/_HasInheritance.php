<?php

namespace Darkink\AuthorizationServer\Traits;

use Darkink\AuthorizationServer\Database\InheritanceBuilder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\callback;

trait _HasInheritance
{

    protected $entity_fields = [];
    // protected $child_classes = [];

    protected function filterAttributes($attributes)
    {
        $model = new self();
        $fields = empty($this->entity_fields) ? Schema::getColumnListing($model->table) : $this->entity_fields;
        return array_intersect_key($attributes, array_fill_keys($fields, null));
    }

    protected function getFields($entity_fields, $table)
    {
        $fields = empty($entity_fields) ? Schema::getColumnListing($table) : $entity_fields;
        $fields = array_map(function ($p) use ($table) {
            return "$table.$p as $table-$p";
        }, $fields);
        return $fields;
    }

    protected function getChildClasses(){
        $result = $this->child_classes;

        // if(empty($this->child_classes)){
        //     $lst = [];
        //     foreach ($this->getModels() as $class) {
        //         var_dump($class);
        //         if (is_subclass_of($class, __CLASS__)) {
        //             $lst[] = $class;
        //         }
        //     }
        //     var_dump('AAAAAAAAAAA');
        //     var_dump($lst);
        //     var_dump('AAAAAAAAAAA');
        //     exit;
        //     $result = $lst;
        // }

        return $result;
    }

    public static function queryAll()
    {
        return (new static)->newQueryAll();
    }

    public function newQueryAll()
    {
        $query = $this->newQuery();

        $parent = $this->newSelfInstance();
        $parent_table = $parent->table;
        $parent_key = $parent->getKeyName();

        $query = $query->addSelect("$parent_table.*");

        foreach ($this->getChildClasses() as $childClass) {
            $child = new $childClass();
            $child_table = $child->table;
            $child_key = $child->getKeyName();

            $fields = $this->getFields($child->entity_fields, $child_table);

            $query = $query
                ->leftJoin($child_table, "$child_table.$child_key", '=', "$parent_table.$parent_key")
                ->addSelect($fields);
        }

        return $query;
    }

    public function newEloquentBuilder($query)
    {
        return new InheritanceBuilder($query, $this->getChildClasses());
    }

    protected function newSelfInstance($attributes = [], $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new self((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $model->getConnectionName()
        );

        $model->setTable($model->getTable());

        $model->mergeCasts($model->casts);

        return $model;
    }

    protected function newSelfInstanceUnguarded($attributes = [], $exists = false)
    {
        return $this->unguarded(function () use ($attributes, $exists) {
            return $this->newSelfInstance($attributes, $exists);
        });
    }

    protected function untimestamps(callable $callback)
    {
        if (!$this->timestamps) {
            return $callback();
        }

        $oldValue = $this->timestamps;
        $this->timestamps = false;

        try {
            return $callback();
        } finally {
            $this->timestamps =  $oldValue;
        }
    }

    protected function inheritanceInsertAndSetId(Builder $query, $attributes)
    {
        $keyName = $this->getKeyName();
        unset($attributes[$keyName]);
        $parent_attributes = $this->filterAttributes($attributes);
        $parent = $this->newSelfInstanceUnguarded($parent_attributes, false);
        $parent->save();
        $id = $parent->id;
        $this->setAttribute($keyName, $id);
        $result = array_diff_key($attributes, $parent_attributes);
        $result[$keyName] = $id;
        return $result;
    }

    protected function inheritancePerformInsert(Builder $query)
    {
        DB::beginTransaction();
        try {
            $result = parent::performInsert($query);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        return $result;
    }

    protected function inheritancePerformUpdate(Builder $query)
    {
        DB::beginTransaction();

        try {
            if ($this->fireModelEvent('updating') === false) {
                return false;
            }

            if ($this->usesTimestamps()) {
                $this->updateTimestamps();
            }

            $dirty = $this->getDirty();
            $parent_dirty = $this->filterAttributes($dirty);
            $dirty = array_diff_key($dirty, $parent_dirty);
            $updated = false;

            if (count($parent_dirty) > 0) {
                $parent = $this->newSelfInstanceUnguarded($this->getAttributes(), true);
                $parent_query = $parent->newModelQuery();
                $parent->setKeysForSaveQuery($parent_query)->update($parent_dirty);
                $parent->syncChanges();
                $updated = true;
            }

            if (count($dirty) > 0) {
                $this->untimestamps(function () use ($query, $dirty) {
                    $this->setKeysForSaveQuery($query)->update($dirty);
                });
                $this->syncChanges();
                $updated = true;
            }

            if ($updated) {
                $this->fireModelEvent('updated', false);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return true;
    }
}
