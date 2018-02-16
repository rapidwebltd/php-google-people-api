<?php

namespace RapidWeb\GooglePeopleAPI;

class Contact
{
    private $googlePeople;
    private $values = [];
    private $changedKeys = [];

    public function __construct(GooglePeople $googlePeople)
    {
        $this->googlePeople = $googlePeople;
    }

    public function __get($key)
    {
        return $this->values[$key];
    }

    public function __set($key, $value)
    {
        $this->values[$key] = $value;
        $this->markAsChanged($key);
        return $this;
    }

    private function markAsChanged($key)
    {
        if (!in_array($key, $changedKeys)) {
            $this->changedKeys[] = $key;
        }
    }

    public function save()
    {
        $this->googlePeople->save($this);
    }
}