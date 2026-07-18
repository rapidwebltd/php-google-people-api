<?php

namespace RapidWeb\GooglePeopleAPI;

class Contact
{
    private $googlePeople;

    public $resourceName;
    public $etag;
    public $metadata;
    public $addresses;
    public $ageRanges;
    public $biographies;
    public $birthdays;
    public $calendarUrls;
    public $clientData;
    public $coverPhotos;
    public $emailAddresses;
    public $events;
    public $externalIds;
    public $genders;
    public $imClients;
    public $interests;
    public $locales;
    public $locations;
    public $memberships;
    public $miscKeywords;
    public $names;
    public $nicknames;
    public $occupations;
    public $organizations;
    public $phoneNumbers;
    public $photos;
    public $relations;
    public $sipAddresses;
    public $skills;
    public $urls;
    public $userDefined;

    // Retained for backwards compatibility with older People API responses.
    public $braggingRights;
    public $relationshipInterests;
    public $relationshipStatuses;
    public $residences;
    public $taglines;

    public function __construct(GooglePeople $googlePeople)
    {
        $this->googlePeople = $googlePeople;
    }

    public function save()
    {
        $updatedContact = $this->googlePeople->save($this);
        
        foreach ($updatedContact as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    public function delete()
    {
        return $this->googlePeople->delete($this);
    }
}
