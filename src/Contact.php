<?php

namespace RapidWeb\GooglePeopleAPI;

class Contact
{
    private $googlePeople;

    public function __construct(GooglePeople $googlePeople)
    {
        $this->googlePeople = $googlePeople;
    }

    public function save()
    {
        $updatedContact = $this->googlePeople->save($this);
        
        foreach($updatedContact as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    public function delete()
    {
        return $this->googlePeople->delete($this);
    }
}