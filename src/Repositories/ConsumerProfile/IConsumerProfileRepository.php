<?php

namespace Klamo\ProfilingSystem\Repositories\ConsumerProfile;

interface IConsumerProfileRepository{

    public function generate($consumers);
    public function create(?Int $consumer_id);
    public function read(?Int $consumer_id);
    public function flagForProcess(?Int $consumer_id, Bool $should_process);
    public function getFlaggedForProcessing();
    public function getRecommendations(?Int $consumer_id);
    public function delete(?Int $consumer_id);
}