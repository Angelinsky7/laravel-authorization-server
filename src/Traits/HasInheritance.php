<?php

namespace Darkink\AuthorizationServer\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait HasInheritance
{

    protected $entity_fields = [];

    protected function filterAttributes($attributes)
    {
        $model = new self();
        $fields = empty($this->entity_fields) ? Schema::getColumnListing($model->table) : $this->entity_fields;
        return array_intersect_key($attributes, array_fill_keys($fields, null));
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

    protected function inheritanceInsertAndSetId(Builder $query, $attributes)
    {
        $parent_attributes = $this->filterAttributes($attributes);
        $parent = $this->newSelfInstance($parent_attributes, false);
        $parent->save();
        $id = $parent->id;
        $keyName = $this->getKeyName();
        $this->setAttribute($keyName, $id);
        $result = array_diff_key($attributes, $parent_attributes);
        $result[$keyName] = $id;
        return $result;
    }

    protected function inheritancePerformUpdate(Builder $query)
    {
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
            $this->setKeysForSaveQuery($query)->update($dirty);
            $this->syncChanges();
            $updated = true;
        }

        if ($updated) {
            $this->fireModelEvent('updated', false);
        }

        return true;
    }
}
