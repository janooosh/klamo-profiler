<?php

namespace Klamo\ProfilingSystem;

use Illuminate\Support\ServiceProvider;
use Klamo\ProfilingSystem\Console\KlamoProfilerConsumerProfileSetup;
use Klamo\ProfilingSystem\Console\KlamoProfilerGenericConsumerProfileSetup;
use Klamo\ProfilingSystem\Console\KlamoProfilerInit;
use Klamo\ProfilingSystem\Console\KlamoProfilerMigrate;
use Klamo\ProfilingSystem\Console\KlamoProfilerProcess;
use Klamo\ProfilingSystem\Console\KlamoProfilerProcessGeneric;
use Klamo\ProfilingSystem\Console\KlamoProfilerProductProfileSetup;
use Klamo\ProfilingSystem\Console\KlamoProfilerPublishCommands;
use Klamo\ProfilingSystem\Console\KlamoProfilerPublishMigrations;
use Klamo\ProfilingSystem\Console\KlamoProfilerSeed;
use Klamo\ProfilingSystem\Console\KlamoProfilerSetup;
use Klamo\ProfilingSystem\Console\KlamoProfilerTagSetup;
use Klamo\ProfilingSystem\Console\KlamoProfilerTagTypeSetup;
use Klamo\ProfilingSystem\Console\KlamoProfilerUpdateProductProfile;
use Klamo\ProfilingSystem\DataGatheringSubSystem\ProfilingDataGathering;
use Klamo\ProfilingSystem\DataGatheringSubSystem\ProfilingDataGatheringInterface;
use Klamo\ProfilingSystem\DataProcessingSubSystem\ProfilingDataProcessing;
use Klamo\ProfilingSystem\DataProcessingSubSystem\ProfilingDataProcessingInterface;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ProfilingTagType;
use Klamo\ProfilingSystem\Observers\ConsumerProfileObserver;
use Klamo\ProfilingSystem\Observers\GenericConsumerProfileObserver;
use Klamo\ProfilingSystem\Observers\ProductProfileObserver;
use Klamo\ProfilingSystem\Observers\ProfilingTagObserver;
use Klamo\ProfilingSystem\Observers\ProfilingTagTypeObserver;
use Klamo\ProfilingSystem\Providers\EventServiceProvider;
use Klamo\ProfilingSystem\Repositories\ConsumerAction\ConsumerActionRepository;
use Klamo\ProfilingSystem\Repositories\ConsumerAction\IConsumerActionRepository;
use Klamo\ProfilingSystem\Repositories\ConsumerProfile\ConsumerProfileRepository;
use Klamo\ProfilingSystem\Repositories\ConsumerProfile\IConsumerProfileRepository;
use Klamo\ProfilingSystem\Repositories\GenericConsumerProfile\GenericConsumerProfileRepository;
use Klamo\ProfilingSystem\Repositories\GenericConsumerProfile\IGenericConsumerProfileRepository;
use Klamo\ProfilingSystem\Repositories\ProductProfile\IProductProfileRepository;
use Klamo\ProfilingSystem\Repositories\ProductProfile\ProductProfileRepository;
use Klamo\ProfilingSystem\Repositories\ProfilingTag\IProfilingTagRepository;
use Klamo\ProfilingSystem\Repositories\ProfilingTag\ProfilingTagRepository;
use Klamo\ProfilingSystem\Repositories\ProfilingTagType\IProfilingTagTypeRepository;
use Klamo\ProfilingSystem\Repositories\ProfilingTagType\ProfilingTagTypeRepository;
use Klamo\ProfilingSystem\SetupSubSystem\ProfilingSetup;
use Klamo\ProfilingSystem\SetupSubSystem\ProfilingSetupInterface;

class ProfilingServiceProvider extends ServiceProvider{
    
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        
        $this->app->bind('klamoProfiler', function($app) {
            return new KlamoProfiler();
        });

        $this->app->bind(ProfilingSetupInterface::class, ProfilingSetup::class);
        $this->app->bind(ProfilingDataGatheringInterface::class, ProfilingDataGathering::class);
        $this->app->bind(ProfilingDataProcessingInterface::class, ProfilingDataProcessing::class);

        $this->app->bind(IProfilingTagTypeRepository::class, ProfilingTagTypeRepository::class);
        $this->app->bind(IProfilingTagRepository::class, ProfilingTagRepository::class);
        $this->app->bind(IProductProfileRepository::class, ProductProfileRepository::class);
        $this->app->bind(IConsumerProfileRepository::class, ConsumerProfileRepository::class);
        $this->app->bind(IGenericConsumerProfileRepository::class, GenericConsumerProfileRepository::class);
        $this->app->bind(IConsumerActionRepository::class, ConsumerActionRepository::class);
    }

    public function boot()
    {
        // Check if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            
            //Export migration stubs
            $this->publishMigrations();

            //Export job migrations stub
            $this->publishJobMigration();

            //Export command stubs
            $this->publishCommands();

            //Export commands
            $this->commands([
                KlamoProfilerConsumerProfileSetup::class,
                KlamoProfilerGenericConsumerProfileSetup::class,
                KlamoProfilerInit::class,
                KlamoProfilerMigrate::class,
                KlamoProfilerProcess::class,
                KlamoProfilerProcessGeneric::class,
                KlamoProfilerProductProfileSetup::class,
                KlamoProfilerPublishCommands::class,
                KlamoProfilerPublishMigrations::class,
                KlamoProfilerSeed::class,
                KlamoProfilerSetup::class,
                KlamoProfilerTagSetup::class,
                KlamoProfilerTagTypeSetup::class,
                KlamoProfilerUpdateProductProfile::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/KlamoProfilingSystem.php' => config_path('ProfilingSystem.php'),
            ], 'config');
        }
        
        //Set up observers
        $this->observerSetup();
    }

    private function publishMigrations()
    {
        $this->publishes([
        // you can add any number of migrations here
            __DIR__ . '/../database/stubs/create_profiling_tag_types_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1111' . '_create_profiling_tag_types_table.php'),
            __DIR__ . '/../database/stubs/create_profiling_tags_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1112' . '_create_profiling_tags_table.php'),
            __DIR__ . '/../database/stubs/create_consumer_profiles_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1113' . '_create_consumer_profiles_table.php'),
            __DIR__ . '/../database/stubs/create_generic_consumer_profiles_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1114' . '_create_generic_consumer_profiles_table.php'),
            __DIR__ . '/../database/stubs/create_product_profiles_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1115' . '_create_product_profiles_table.php'),
            __DIR__ . '/../database/stubs/create_consumer_profile_profiling_tag_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1116' . '_create_consumer_profile_profiling_tag_table.php'),
            __DIR__ . '/../database/stubs/create_consumer_profile_product_profile_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1117' . '_create_consumer_profile_product_profile_table.php'),
            __DIR__ . '/../database/stubs/create_generic_consumer_profile_profiling_tag_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1118' . '_create_generic_consumer_profile_profiling_tag_table.php'),
            __DIR__ . '/../database/stubs/create_generic_consumer_profile_product_profile_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1119' . '_create_generic_consumer_profile_product_profile_table.php'),
            __DIR__ . '/../database/stubs/create_consumer_actions_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1120' . '_create_consumer_actions_table.php'),
            __DIR__ . '/../database/stubs/create_product_profile_profiling_tag_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1121' . '_create_product_profile_profiling_tag_table.php'),
            __DIR__ . '/../database/stubs/add_weightfactor_to_profiling_tags.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_12_13_110000' . '_add_weightfactor_to_profiling_tags.php'),
        ], 'migrations');
   }

   private function publishJobMigration()
   {
       $this->publishes([
            __DIR__ . '/../database/stubs/create_job_batches_table.php.stub' => database_path('migrations/ProfilingSystem/' . '2021_11_11_1122' . '_create_job_batches_table.php'),
       ], 'job-migration');
   }

   private function publishCommands()
   {
    $this->publishes([
        __DIR__ . '/Console/stubs/KlamoProfilerConsumerProfileSetup.php.stub' => app_path('Console/Commands/'.'KlamoProfilerConsumerProfileSetup.php'),
        __DIR__ . '/Console/stubs/KlamoProfilerGenericConsumerProfileSetup.php.stub' => app_path('Console/Commands/'.'KlamoProfilerGenericConsumerProfileSetup.php'),
        __DIR__ . '/Console/stubs/KlamoProfilerProductProfileSetup.php.stub' => app_path('Console/Commands/'.'KlamoProfilerProductProfileSetup.php'),
        __DIR__ . '/Console/stubs/KlamoProfilerTagSetup.php.stub' => app_path('Console/Commands/'.'KlamoProfilerTagSetup.php'),
        __DIR__ . '/Console/stubs/KlamoProfilerTagTypeSetup.php.stub' => app_path('Console/Commands/'.'KlamoProfilerTagTypeSetup.php'),
        __DIR__ . '/Console/stubs/KlamoProfilerUpdateProductProfile.php.stub' => app_path('Console/Commands/'.'KlamoProfilerUpdateProductProfile.php'),
        __DIR__ . '/Console/stubs/KlamoProfilerProcess.php.stub' => app_path('Console/Commands/'.'KlamoProfilerProcess.php'),
   ], 'commands');
   }

   private function observerSetup()
   {
    ProfilingTagType::observe(ProfilingTagTypeObserver::class);
    ProfilingTag::observe(ProfilingTagObserver::class);
    ProductProfile::observe(ProductProfileObserver::class);
    ConsumerProfile::observe(ConsumerProfileObserver::class);
    GenericConsumerProfile::observe(GenericConsumerProfileObserver::class);
   }
}