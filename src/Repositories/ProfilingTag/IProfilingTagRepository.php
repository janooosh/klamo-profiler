<?php

namespace Klamo\ProfilingSystem\Repositories\ProfilingTag;

interface IProfilingTagRepository{

    public function generate(String $attribute_class,String $column_name,String $profiling_tag_type_name);
    public function create(?String $name,?String $profiling_tag_type_name);
    public function read(?String $profiling_tag_name, ?String $profiling_tag_type_name);
    public function delete(?String $profiling_tag_name, ?String $profiling_tag_type_name);
}