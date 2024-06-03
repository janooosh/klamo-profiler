<?php

namespace Klamo\ProfilingSystem\Repositories\ProfilingTagType;

interface IProfilingTagTypeRepository{

    public function create(?String $profiling_tag_type_name);
    public function read(?String $profiling_tag_type_name);
    public function updateWeight(?String $profiling_tag_type_name, ?Int $weight);
    public function delete(?String $profiling_tag_type_name);
}