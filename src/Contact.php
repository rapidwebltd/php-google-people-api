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
        $this->googlePeople->save($this);
    }
}