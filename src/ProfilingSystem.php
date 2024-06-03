<?php 

namespace Klamo\ProfilingSystem;

use Klamo\ProfilingSystem\DataGatheringSubSystem\ProfilingDataGatheringInterface;
use Klamo\ProfilingSystem\DataProcessingSubSystem\ProfilingDataProcessingInterface;
use Klamo\ProfilingSystem\SetupSubSystem\ProfilingSetupInterface;

class ProfilingSystem{

    private $setup;
    private $data_gathering;
    private $data_processing;

    public function __construct(ProfilingSetupInterface $setup, ProfilingDataGatheringInterface $data_gathering, ProfilingDataProcessingInterface $data_processing)
    {
        $this->setup = $setup;
        $this->data_gathering = $data_gathering;
        $this->data_processing = $data_processing;
    }

    public function setup()
    {
        return $this->setup;
    }

    public function gather()
    {
        return $this->data_gathering;
    }

    public function process()
    {
        return $this->data_processing;
    }
}