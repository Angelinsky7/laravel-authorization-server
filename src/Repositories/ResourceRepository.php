<?php

namespace Darkink\AuthorizationServer\Repositories;

use Darkink\AuthorizationServer\Models\Resource;
use Darkink\AuthorizationServer\Models\Uri;
use Darkink\AuthorizationServer\Policy;
use Exception;
use Illuminate\Support\Facades\DB;

class ResourceRepository
{
    public function find(int $id): Resource
    {
        $resource = Policy::resource();
        return $resource->where($resource->getKeyName(), $id)->first();
    }

    public function gets()
    {
        return Policy::resource();
    }

    protected function uriMap(Uri | string $uri)
    {
        if (is_string($uri)) {
            return new Uri([
                'uri' => $uri
            ]);
        } else {
            return $uri;
        }
    }

    public function create(string $name, string $displayName, string | null $type, string | null $iconUri, array $uris, array $scopes): Resource
    {
        DB::beginTransaction();

        try {

            $resource = Policy::resource()->forceFill([
                'name' => $name,
                'display_name' => $displayName,
                'type' => $type,
                'icon_uri' => $iconUri,
            ]);
            $resource->save();

            $resource->uris()->createMany(array_map([$this, 'uriMap'], $uris));
            $resource->scopes()->attach($scopes);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $resource;
    }

    public function update(Resource $resource, string $name, string $displayName, string | null $type, string | null $iconUri, array $uris, array $scopes): Resource
    {
        DB::beginTransaction();

        try {

            $resource->forceFill([
                'name' => $name,
                'display_name' => $displayName,
                'type' => $type,
                'icon_uri' => $iconUri,
            ])->save();

            $resource->uris()->sync(array_column($uris, 'id'));
            $resource->scopes()->sync($scopes);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $resource;
    }

    public function delete(Resource $resource)
    {
        $resource->delete();
    }
}
