<?php

namespace Klamo\ProfilingSystem\Repositories\GenericConsumerProfile;

interface IGenericConsumerProfileRepository{

    public function create();
    public function read(?Int $generic_consumer_profile_id);
    public function getLatest();
    public function delete(?Int $generic_consumer_profile_id);
}