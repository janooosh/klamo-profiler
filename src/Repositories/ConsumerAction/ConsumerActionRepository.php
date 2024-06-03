<?php

namespace Klamo\ProfilingSystem\Repositories\ConsumerAction;

use Klamo\ProfilingSystem\Models\ConsumerAction;

class ConsumerActionRepository implements IConsumerActionRepository{

    /**
     * Create
     * 
     * Given:
     *  - a name
     *  - a weight
     * 
     * Create a consumer action if it doesn't exist or update the existing one.
     * Return the consumer action
     * 
     * @param consumer_action_name
     * @param consumer_action_weight
     * 
     * @return Klamo\ProfilingSystem\Models\ConsumerAction
     */
    public function create(?String $consumer_action_name, ?Int $consumer_action_weight)
    {   
        //If either parameters is null, then return null
        if(($consumer_action_name === null) || ($consumer_action_weight === null)){
            return null;
        }

        //Update or create a consumer action based on name and weight
        $consumer_action = ConsumerAction::updateOrCreate([
            'name' => $consumer_action_name,
            'weight' => $consumer_action_weight,
        ]);

        return $consumer_action;
    }

    /**
     * Read
     * 
     * Given a consumer action name, return the consumer action in the system
     * 
     * @param consumer_action_name
     * 
     * @return Klamo\ProfilingSystem\Models\ConsumerAction
     */
    public function read(?String $consumer_action_name){

        //If consumer action name is null, then return null
        if(($consumer_action_name === null)){
            return null;
        }

        return ConsumerAction::where('name', $consumer_action_name)->first();
    }

    /**
     * Update
     * 
     * Given:
     *  - a name
     *  - a weight
     * 
     * Update a consumer action with a new weight.
     * Return the consumer action
     * 
     * @param consumer_action_name
     * @param consumer_action_weight
     * 
     * @return Klamo\ProfilingSystem\Models\ConsumerAction
     */
    public function update(?String $consumer_action_name, ?Int $consumer_action_weight)
    {   
        //If either parameters is null, then return null
        if(($consumer_action_name === null) || ($consumer_action_weight === null)){
            return null;
        }

        //Grab consumer action and if it doesn't exist return null
        $consumer_action = $this->read($consumer_action_name);
        if($consumer_action === null){
            return null;
        }

        //Update a consumer action based on name
        $consumer_action->update([
            'weight' => $consumer_action_weight,
        ]);

        return $consumer_action;
    }

    /**
     * Delete
     * 
     * Given:
     *  - a name
     * 
     * Delete a consumer action based on a name.
     * Return the consumer action
     * 
     * @param consumer_action_name
     * 
     * @return boolean
     */
    public function delete(?String $consumer_action_name)
    {   
        //Grab consumer action based on consumer action name and return false if it doesn't exist
        $consumer_action = $this->read($consumer_action_name);
        if($consumer_action === null){
            return false;
        }
        
        //Attempt to delete the consumer action and return the result
        return $consumer_action->delete();
    }
}