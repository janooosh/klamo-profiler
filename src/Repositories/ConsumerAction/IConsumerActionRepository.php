<?php

namespace Klamo\ProfilingSystem\Repositories\ConsumerAction;

interface IConsumerActionRepository{

    public function create(?String $consumer_action_name, ?Int $consumer_action_weight);
    public function read(?String $consumer_action_name);
    public function update(?String $consumer_action_name, ?Int $consumer_action_weight);
    public function delete(?String $consumer_action_name);
}