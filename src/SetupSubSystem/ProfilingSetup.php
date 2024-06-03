<?php

namespace Klamo\ProfilingSystem\SetupSubSystem;

use Klamo\ProfilingSystem\Repositories\ConsumerAction\IConsumerActionRepository;
use Klamo\ProfilingSystem\Repositories\ConsumerProfile\IConsumerProfileRepository;
use Klamo\ProfilingSystem\Repositories\GenericConsumerProfile\IGenericConsumerProfileRepository;
use Klamo\ProfilingSystem\Repositories\ProductProfile\IProductProfileRepository;
use Klamo\ProfilingSystem\Repositories\ProfilingTag\IProfilingTagRepository;
use Klamo\ProfilingSystem\Repositories\ProfilingTagType\IProfilingTagTypeRepository;

class ProfilingSetup implements ProfilingSetupInterface{

    private $profiling_tag_type_repository;
    private $profiling_tag_repository;
    private $product_profile_repository;
    private $consumer_profile_repository;
    private $generic_consumer_profile_repository;
    private $consumer_action_repository;

    public function __construct(IProfilingTagTypeRepository $profiling_tag_type_repository, 
                                IProfilingTagRepository $profiling_tag_repository,
                                IProductProfileRepository $product_profile_repository,
                                IConsumerProfileRepository $consumer_profile_repository,
                                IGenericConsumerProfileRepository $generic_consumer_profile_repository,
                                IConsumerActionRepository $consumer_action_repository)
    {
        $this->profiling_tag_type_repository = $profiling_tag_type_repository;
        $this->profiling_tag_repository = $profiling_tag_repository;
        $this->product_profile_repository = $product_profile_repository;
        $this->consumer_profile_repository = $consumer_profile_repository;
        $this->generic_consumer_profile_repository = $generic_consumer_profile_repository;
        $this->consumer_action_repository = $consumer_action_repository;
    }

    public function ConsumerAction()
    {
        return $this->consumer_action_repository;
    }

    public function ProfilingTagType()
    {
        return $this->profiling_tag_type_repository;;
    }

    public function ProfilingTag()
    {
        return $this->profiling_tag_repository;
    }

    public function ProductProfile()
    {
        return $this->product_profile_repository;
    }

    public function ConsumerProfile()
    {
        return $this->consumer_profile_repository;
    }

    public function GenericConsumerProfile()
    {
        return $this->generic_consumer_profile_repository;
    }
}