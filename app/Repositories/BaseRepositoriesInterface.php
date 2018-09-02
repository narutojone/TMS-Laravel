<?php
 
namespace App\Repositories;
 
/**
 * The contract for the base eloquent repo
 */
interface BaseRepositoriesInterface
{
    function all();

    function get($id);

    function create(array $attributes);

    function update($id, array $attributes);
 
    function delete($id);
}