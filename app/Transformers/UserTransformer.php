<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array [$originalVal| ObjectModel, ]
     */
    public function transform(User $user)
    {
        return [
            'identifier'    => (int)$user->id,
            'name'          => (string)$user->name,
            'email'         => (string)$user->email,
            'isVerified'    => (int)$user->verified,
            'isAdmin'       => ($user->admin === 'true'),
            'creationDate'  => (string)$user->created_at,
            'lastChange'    => (string)$user->updated_at,
            'deletedDate'   => isset($user->deleted_at) ? (string)$user->deleted_at : null,
        ];
    }

    /*
        3:53 PM 01-31-2019
        Mapping the original value from fractal name into the model property name
    */
    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier'    => 'id',
            'name'          => 'name',
            'email'         => 'email',
            'isVerified'    => 'verified',
            'isAdmin'       => 'admin',
            'creationDate'  => 'created_at',
            'lastChange'    => 'updated_at',
            'deletedDate'   => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /*
        23:17 05-08-2020
        Mapping model field to fractal(response) field.
    */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id'         => 'identifier',
            'name'       => 'name',
            'email'      => 'email',
            'verified'    => 'isVerified',
            'admin'      => 'isAdmin',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
