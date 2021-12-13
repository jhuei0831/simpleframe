<?php

namespace App\Models;

abstract class Model
{
    protected $connection;

    protected $table;

    protected $primaryKey = 'id';

    public function all($columns = ['*'])
    {
        # code...
    }

    public function find($primaryKey = 'id')
    {
        # code...
    }

    public function insert($attributes)
    {
        # code...
    }

    public function update($attributes)
    {
        # code...
    }

    public function delete($primaryKey = 'id')
    {
        # code...
    }
}
