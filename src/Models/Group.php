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
 * @property Group[] $memberOfs
 * @property Group[] $members
 */
class Group extends BaseModel
{
    use HasFactory;

    protected $table = 'uma_groups';

    public function memberOfs()
    {
        return $this->belongsToMany(Group::class, 'uma_group_member', 'member_group_id', 'group_id');
    }

    public function members()
    {
        //ddd($this->group_members()->get(), $this->user_members()->get(), $this->group_members()->get()->concat($this->user_members()->get()));
        return $this->group_members()->get()->concat($this->user_members()->get());
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
