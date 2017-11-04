<?php

$postData = json_decode($rapBackupFile,TRUE);
$postData['modelJSON'] = str_replace("\'",'\"',$postData['modelJSON']);
$data = json_decode($postData['modelJSON'],TRUE);
if(json_last_error() !== JSON_ERROR_NONE) {echo 'Json Parse Error';die;}

$postMan = [];
$postMan['info']['name'] = $data['name'];
$postMan['info']['_postman_id'] = md5(microtime(TRUE));
$postMan['info']['schema'] = "https://schema.getpostman.com/json/collection/v2.0.0/collection.json";
$postMan['item'] = [];
foreach ($data['moduleList'] as &$module) {
    foreach ($module['pageList'] as &$page) {
        $p = [];
        $p['name'] = $page['name'];
        foreach ($page['actionList'] as &$action) {
            $a = [];
            $a['name'] = $action['name'];
	    # test测试内容
	    $a['event'] = [['listen'=>'test','script'=>['type'=>'text/javascript','exec'=>['tests["Status code is 200"] = responseCode.code === 200;','var jsonData = JSON.parse(responseBody);','tests["data code is 200?"] = jsonData.code === 200;']]]];
            $a['request']['url'] = '{{url}}/'.trim($action['requestUrl'],'/');
            $a['request']['method'] = ($action['requestType']==1) ? 'GET' : 'POST';
            $a['request']['header']['token'] = '{{token}}';
            $a['request']['body']['mode'] = 'urlencoded';
            foreach ($action['requestParameterList'] as &$requestParameter) {
                $r['key'] = $requestParameter['identifier'];
                $r['value'] = "";
                $r['type'] = "text";
                $r['enabled'] = TRUE;
                $a['request']['body']['urlencoded'][] = $r;
            }
            $p['item'][] = $a;
        }
        $postMan['item'][] = $p;
    }
}

#        echo json_encode($postMan);die;
$outputFileName = "postman/postman.json";
file_put_contents($outputFileName,json_encode($postMan));
$html = "<a href='$outputFileName' download='importPostman.json'>下载postman文件</a>";
echo $html;

