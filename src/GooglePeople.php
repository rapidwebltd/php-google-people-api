<?php

namespace RapidWeb\GooglePeopleAPI;

class GooglePeople
{
    private $googleOAuth2Handler;

    const PERSON_FIELDS = 'addresses,ageRanges,biographies,birthdays,braggingRights,coverPhotos,emailAddresses,events,genders,imClients,interests,locales,memberships,metadata,names,nicknames,occupations,organizations,phoneNumbers,photos,relations,relationshipInterests,relationshipStatuses,residences,skills,taglines,urls';
    const PEOPLE_PEOPLE_CONNECTIONS_LIST_URL = 'https://people.googleapis.com/v1/people/me/connections?personFields='.self::PERSON_FIELDS;

    public function __construct(GoogleOAuth2Handler $googleOAuth2Handler)
    {
        $this->googleOAuth2Handler = $googleOAuth2Handler;
    }

    public function getByResourceName($resourceName)
    {

    }

    public function all()
    {
        $response = $this->googleOAuth2Handler->performGetRequest(self::PEOPLE_PEOPLE_CONNECTIONS_LIST_URL);

        var_dump((string) $response);
        die;
    }

    public function me()
    {

    }

    public function save(Contact $contact)
    {

    }
}