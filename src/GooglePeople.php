<?php

namespace RapidWeb\GooglePeopleAPI;

class GooglePeople
{
    private $googleOAuth2Handler;

    const PERSON_FIELDS = ['addresses', 'ageRanges', 'biographies', 'birthdays', 'braggingRights', 'coverPhotos', 'emailAddresses', 'events', 'genders', 'imClients', 'interests', 'locales', 'memberships', 'metadata', 'names', 'nicknames', 'occupations', 'organizations', 'phoneNumbers', 'photos', 'relations', 'relationshipInterests', 'relationshipStatuses', 'residences', 'skills', 'taglines', 'urls'];
    const PEOPLE_BASE_URL = 'https://people.googleapis.com/v1/';

    public function __construct(GoogleOAuth2Handler $googleOAuth2Handler)
    {
        $this->googleOAuth2Handler = $googleOAuth2Handler;
    }

    private function convertResponseConnectionToContact($connection)
    {
        $contact = new Contact($this);
        $contact->resourceName = $connection->resourceName;
        $contact->etag = $connection->etag;
        $contact->metadata = $connection->metadata;

        foreach(self::PERSON_FIELDS as $personField) {
            if (isset($connection->$personField)) {
                $contact->$personField = $connection->$personField;
            } else {
                $contact->$personField = [];
            }
        }

        return $contact;
    }

    public function getByResourceName($resourceName)
    {
        $url = self::PEOPLE_BASE_URL.$resourceName.'?personFields='.implode(',', self::PERSON_FIELDS);

        $response = $this->googleOAuth2Handler->performGetRequest($url);
        $body = (string) $response;

        $contact = json_decode($body);

        return $this->convertResponseConnectionToContact($contact);
    }

    public function all()
    {
        $url = self::PEOPLE_BASE_URL.'people/me/connections?personFields='.implode(',', self::PERSON_FIELDS);

        $response = $this->googleOAuth2Handler->performGetRequest($url);
        $body = (string) $response;

        $responseObj = json_decode($body);

        $contacts = [];

        foreach($responseObj->connections as $connection) {
            $contacts[] = $this->convertResponseConnectionToContact($connection);
        }

        return $contacts;
    }

    public function me()
    {
        return $this->getByResourceName('people/me');
    }

    public function save(Contact $contact)
    {

    }
}