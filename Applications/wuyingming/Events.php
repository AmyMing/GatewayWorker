<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 * udp协议 没有onConnect 事件
 */
class Events
{ 
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message) {
    //把数据存起来  
    //file_put_contents('./device_list.txt', $message."\r\n",FILE_APPEND);
    //存到数据库中
    $db =  Db::instance('db');
    $data = json_decode($message,true);

    $cmd = $data['cmd'];

    if($cmd == 'flow-getDeviceList'){
      //获取设备列表
      $info = $db->select('*')->from('device_list')->where("userId= '".$data['userId']."' ")->query();
      $test = Gateway::isOnline($client_id);
      var_dump($test);
      var_dump($_SERVER);
     

    }
    //先去查询是否绑定过
    //$info = $db->select('*')->from('device_list')->where("sid= '".$data['sid']."' ")->row();
    /*
    if($info){
      //之前绑定过
      //下放命令,比如打开只能插座
    }
    else{
      //没有绑定过
      $insert_id = $db->insert('device_list')->cols(array('userId'=>$data['userId'], 'model'=>$data['model'], 'sid'=>$data['sid'], 'short_id'=>$data['short_id'],'data'=>$data['data']))->query();
    }
    */
    //解析网关发来的心跳包
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
       // 向所有人发送 
       GateWay::sendToAll("$client_id logout");
   }
}
