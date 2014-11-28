<?php 
$I = new ApiTester($scenario);
$I->wantTo('get a existing Contact with id=1 at com_contacts');
//$I->amHttpAuthenticated('service_user', '123456');
//$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('index.php', ['option' => 'contact', 'api' => 'Hal', 'id' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('"id":"1"');
?>