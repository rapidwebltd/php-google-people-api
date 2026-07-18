<?php

namespace RapidWeb\GooglePeopleAPI;

use Exception;
use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler;

class GooglePeople
{
    private $googleOAuth2Handler;

    const PERSON_FIELDS = ['addresses', 'ageRanges', 'biographies', 'birthdays', 'calendarUrls', 'clientData', 'coverPhotos', 'emailAddresses', 'events', 'externalIds', 'genders', 'imClients', 'interests', 'locales', 'locations', 'memberships', 'metadata', 'miscKeywords', 'names', 'nicknames', 'occupations', 'organizations', 'phoneNumbers', 'photos', 'relations', 'sipAddresses', 'skills', 'urls', 'userDefined'];
    const UPDATE_PERSON_FIELDS = ['addresses', 'biographies', 'birthdays', 'calendarUrls', 'clientData', 'emailAddresses', 'events', 'externalIds', 'genders', 'imClients', 'interests', 'locales', 'locations', 'memberships', 'miscKeywords', 'names', 'nicknames', 'occupations', 'organizations', 'phoneNumbers', 'relations', 'sipAddresses', 'urls', 'userDefined'];
    const PEOPLE_BASE_URL = 'https://people.googleapis.com/v1/';

    public function __construct(GoogleOAuth2Handler $googleOAuth2Handler)
    {
        $this->googleOAuth2Handler = $googleOAuth2Handler;
    }

    private function convertResponseConnectionToContact($connection)
    {
        $contact = new Contact($this);
        $contact->resourceName = isset($connection->resourceName) ? $connection->resourceName : null;
        $contact->etag = isset($connection->etag) ? $connection->etag : null;
        $contact->metadata = isset($connection->metadata) ? $connection->metadata : null;

        foreach (self::PERSON_FIELDS as $personField) {
            if (isset($connection->$personField)) {
                $contact->$personField = $connection->$personField;
            } else {
                $contact->$personField = [];
            }
        }

        return $contact;
    }

    public function get($resourceName)
    {
        $url = self::PEOPLE_BASE_URL.$resourceName.'?personFields='.implode(',', self::PERSON_FIELDS);

        $response = $this->googleOAuth2Handler->performRequest('GET', $url);
        $body = (string) $response->getBody();

        if ($response->getStatusCode()!=200) {
            throw new Exception($body);
        }

        $contact = json_decode($body);

        if (!is_object($contact)) {
            throw new Exception('Google returned an invalid contact response.');
        }

        return $this->convertResponseConnectionToContact($contact);
    }

    public function all()
    {
        $url = self::PEOPLE_BASE_URL.'people/me/connections?personFields='.implode(',', self::PERSON_FIELDS).'&pageSize=1000';

        $response = $this->googleOAuth2Handler->performRequest('GET', $url);
        $body = (string) $response->getBody();

        if ($response->getStatusCode()!=200) {
            throw new Exception($body);
        }

        $responseObj = json_decode($body);

        if (!is_object($responseObj)) {
            throw new Exception('Google returned an invalid contacts response.');
        }

        $contacts = [];

        foreach (isset($responseObj->connections) && is_array($responseObj->connections) ? $responseObj->connections : [] as $connection) {
            $contacts[] = $this->convertResponseConnectionToContact($connection);
        }

        while (isset($responseObj->nextPageToken)) {

            $url = self::PEOPLE_BASE_URL.'people/me/connections?personFields='.implode(',', self::PERSON_FIELDS).'&pageSize=1000&pageToken='.rawurlencode($responseObj->nextPageToken);

            $response = $this->googleOAuth2Handler->performRequest('GET', $url);
            $body = (string) $response->getBody();

            if ($response->getStatusCode()!=200) {
                throw new Exception($body);
            }

            $responseObj = json_decode($body);

            if (!is_object($responseObj)) {
                throw new Exception('Google returned an invalid contacts response.');
            }

            foreach (isset($responseObj->connections) && is_array($responseObj->connections) ? $responseObj->connections : [] as $connection) {
                $contacts[] = $this->convertResponseConnectionToContact($connection);
            }
        }

        return $contacts;
    }

    public function me()
    {
        return $this->get('people/me');
    }

    public function save(Contact $contact)
    {
        $requestObj = new \stdClass();

        if (isset($contact->resourceName)) {

            // If resource name exists, update the contact.
            $method = 'PATCH';
            $url = self::PEOPLE_BASE_URL.$contact->resourceName.':updateContact?updatePersonFields='.implode(',', self::UPDATE_PERSON_FIELDS);
            $requestObj->etag = $contact->etag;
            $requestObj->metadata = $contact->metadata;

        } else {

            // If resource name does not exist, create new contact.
            $method = 'POST';
            $url = self::PEOPLE_BASE_URL.'people:createContact';

        }

        foreach (self::UPDATE_PERSON_FIELDS as $personField) {
            if (isset($contact->$personField)) {
                $requestObj->$personField = $contact->$personField;
            }
        }

        $requestBody = json_encode($requestObj);

        if ($requestBody === false) {
            throw new Exception('Contact data could not be encoded as JSON.');
        }

        $response = $this->googleOAuth2Handler->performRequest($method, $url, $requestBody, [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $body = (string) $response->getBody();

        if ($response->getStatusCode()!=200) {
            throw new Exception($body);
        }

        $responseObj = json_decode($body);

        if (!is_object($responseObj)) {
            throw new Exception('Google returned an invalid contact response.');
        }

        return $this->convertResponseConnectionToContact($responseObj);
    }

    public function delete(Contact $contact)
    {
        $url = self::PEOPLE_BASE_URL.$contact->resourceName.':deleteContact';

        $response = $this->googleOAuth2Handler->performRequest('DELETE', $url);
        $body = (string) $response->getBody();

        if ($response->getStatusCode()!=200) {
            throw new Exception($body);
        }

        return true;
    }
}
