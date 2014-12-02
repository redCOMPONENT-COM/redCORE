<?php 
$I = new ApiTester($scenario);
$I->wantTo('GET via webservices an existing Contact with id=1 at com_contacts in HAL format');
$I->sendGET('index.php', ['option' => 'contact', 'api' => 'Hal', 'id' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('"id":"1"');
