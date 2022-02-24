<?php

namespace Darkink\AuthorizationServer\Models;

use App\Models\User;
use Darkink\AuthorizationServer\Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property bool $system
 * @property Group[] $memberofs
 * @property Group[] $members
 */
class Group extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_groups';

    public function memberofs()
    {
        return $this->belongsToMany(Group::class, 'uma_group_member', 'member_group_id', 'group_id');
    }

    public function members()
    {
        return $this->group_members->merge($this->user_members);
    }

    public function group_members()
    {
        return $this->belongsToMany(Group::class, 'uma_group_member', 'group_id', 'member_group_id');
    }

    public function user_members()
    {
        return $this->belongsToMany(User::class, 'uma_group_member', 'group_id', 'member_user_id');
    }

    protected $searchable = [
        'name',
        'display_name',
        'description'
    ];

    public static function newFactory()
    {
        return GroupFactory::new();
    }
}
