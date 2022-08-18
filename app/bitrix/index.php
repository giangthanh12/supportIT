<?php
require_once('crest.php');
require_once('settings.php');

// CRest::call(
//    'crm.lead.add',
//    		[
//       'fields' =>[
//       'TITLE' => 'Lead Name',//Title*[string]
//       'NAME' => 'Name',//Name[string]
//       'LAST_NAME' => 'Last name',//Last name[string]
//       ]
// 	  ]);
// CRest::call(
//    'im.notify.personal.add',
//    array(
//    'USER_ID' => 945,
//    'MESSAGE' => 'Ticket của bạn vừa được cập nhật',
//    'MESSAGE_OUT' => 'Personal notification text for email',
//    'TAG' => 'TEST',
//    'SUB_TAG' => 'SUB|TEST',
//    'ATTACH' => ''
//    )
// );
echo $_REQUEST['AUTH_ID'];
echo $_REQUEST['DOMAIN'];
echo $_REQUEST['REFRESH_ID'];
echo $_REQUEST['APP_SID'];


return;
$a = CRest::call(
   'im.user.get',
   array(
   'id' => 945,
   )
);
echo "<pre>";
print_r($a);
echo "</pre>";

//"AUTH_ID":"86dfd462005c8e3d0053bf39000003b1403807ae3424c834e5614560f7bff0aab7f2b3"
//"member_id":"6ba73a4db2058d1778bf620b2f0bb3c8"
// CRest::call(
//    'im.notify.system.add',
//    array(
//    'USER_ID' => 945,
//    'MESSAGE' => 'Ticket của bạn vừa được cập nhật',
//    'MESSAGE_OUT' => 'Personal notification text for email',
//    'TAG' => 'TEST',
//    'SUB_TAG' => 'SUB|TEST',
//    'ATTACH' => ''
//    )
// );
//  CRest::getAppSettings();
?>


