# PHP Google People API

This package provides a user friendly way of interacting with Google Contacts via the Google People API.

## Installation

PHP Google People API can be easily installed using Composer. Just run the following command from the root of your project.

```
composer require rapidwebltd/php-google-people-api
```

If you have never used the Composer dependency manager before, head to the [Composer website](https://getcomposer.org/) for more information on how to get started.

## Setup

*TODO*

## Usage

### Retrieve all contacts

```php
// Retrieval all contacts
foreach($people->all() as $contact) {
    echo $contact->resourceName.' - ';
    if ($contact->names) {
        echo $contact->names[0]->displayName;
    }
    echo PHP_EOL;
}
```

### Retrieve a single contact

```php
// Retrieve single contact (by resource name)
$contact = $people->get('people/c8055020007701654287');
```

### Create a new contact

```php
// Create new contact
$contact = new Contact($people);
$contact->names[0] = new stdClass;
$contact->names[0]->givenName = 'Testy';
$contact->names[0]->familyName = 'McTest Test';
$contact->save();
```

### Update a contact

```php
// Update contact
$contact->names[0]->familyName = 'McTest';
$contact->save();
```

### Delete a contact

```php
// Delete contact
$contact->delete();
```