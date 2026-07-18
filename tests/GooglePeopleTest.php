<?php

namespace RapidWeb\GooglePeopleAPI\Tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler;
use RapidWeb\GooglePeopleAPI\Contact;
use RapidWeb\GooglePeopleAPI\GooglePeople;

class GooglePeopleTest extends TestCase
{
    public function testAllFollowsPaginationWhenFirstPageIsEmpty()
    {
        $urls = [];
        $oauth = $this->oauthMock();
        $oauth->expects($this->exactly(2))
            ->method('performRequest')
            ->willReturnCallback(function ($method, $url) use (&$urls) {
                $urls[] = $url;

                if (count($urls) === 1) {
                    return new Response(200, [], json_encode(['nextPageToken' => 'next page']));
                }

                return new Response(200, [], json_encode([
                    'connections' => [['resourceName' => 'people/123', 'etag' => 'etag']],
                ]));
            });

        $contacts = (new GooglePeople($oauth))->all();

        $this->assertCount(1, $contacts);
        $this->assertSame('people/123', $contacts[0]->resourceName);
        $this->assertStringContainsString('pageSize=1000', $urls[0]);
        $this->assertStringContainsString('pageToken=next%20page', $urls[1]);
    }

    public function testMeRetrievesTheAuthenticatedPerson()
    {
        $oauth = $this->oauthMock();
        $oauth->expects($this->once())
            ->method('performRequest')
            ->with('GET', $this->stringStartsWith(GooglePeople::PEOPLE_BASE_URL.'people/me?personFields='))
            ->willReturn(new Response(200, [], json_encode(['resourceName' => 'people/me'])));

        $contact = (new GooglePeople($oauth))->me();

        $this->assertSame('people/me', $contact->resourceName);
    }

    public function testSaveIncludesMembershipsAndJsonHeaders()
    {
        $oauth = $this->oauthMock();
        $oauth->expects($this->once())
            ->method('performRequest')
            ->with(
                'POST',
                GooglePeople::PEOPLE_BASE_URL.'people:createContact',
                $this->callback(function ($body) {
                    $data = json_decode($body, true);

                    return $data['memberships'][0]['contactGroupMembership']['contactGroupResourceName'] === 'contactGroups/friends';
                }),
                ['headers' => ['Content-Type' => 'application/json']]
            )
            ->willReturn(new Response(200, [], json_encode(['resourceName' => 'people/456'])));

        $contact = new Contact(new GooglePeople($oauth));
        $contact->memberships = [[
            'contactGroupMembership' => [
                'contactGroupResourceName' => 'contactGroups/friends',
            ],
        ]];

        $this->assertTrue($contact->save());
        $this->assertSame('people/456', $contact->resourceName);
    }

    private function oauthMock()
    {
        return $this->getMockBuilder(GoogleOAuth2Handler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['performRequest'])
            ->getMock();
    }
}
