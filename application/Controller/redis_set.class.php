<?php
/**
 *
 * @authors tiyee (tiyee@live.com)
 * @link http://www.tiyee.net
 * @date    2015-03-12 16:37:51
 * @version $Id$
 */
namespace Controller;
use \Core\base;
class  redis_set extends base {



    public function sReset() {
        $json = array('error' => 1,'msg'=>'error');
       $key = $this->request->post('key','trim');
        if(empty($key)) {
            $json['msg'] = 'the  key is empty!!';
            $this->return_json($json);
        }
        if(!$this->key_exists($key)) {
             $json['msg'] = 'the  key is not exists!!';
             $this->return_json($json);
        }

        if(!isset($this->request->post['value'])) {
            $json['msg'] = 'the  value is empty!!';
            $this->return_json($json);
        } else {
            $value = $this->request->post['value'];
        }
        if(!isset($this->request->post['nValue'])) {
            $json['msg'] = 'the  nValue is empty!!';
            $this->return_json($json);
        } else {
            $nValue = $this->request->post['nValue'];
        }



        $this->redis->sRem($key, $value);



        $result = $this->redis->sAdd($key, $nValue);
        switch ($result) {
            case 1:
                $json['error'] = 0;
                $json['msg'] = ' the new value is setted';
                break;

            case 0:
                $json['error'] = 1;
                $json['msg'] = ' false ';
                break;


            default:
                $json['error'] = 1;
                $json['msg'] = 'unkown error.';
                break;
        }
         $this->return_json($json);

    }
    public function sRem() {
        $json = array('error' => 1,'msg'=>'error');
        $key = $this->request->post('key','trim');
        if(empty($key)) {
            $json['msg'] = 'the  key is empty!!';
            $this->return_json($json);
        }
        if(!$this->key_exists($key)) {
             $json['msg'] = 'the  key is not exists!!';
             $this->return_json($json);
        }
        $value = $this->request->post('value','trim');
        if(empty($value)) {
            $json['msg'] = 'the value is empty!!';
            $this->return_json($json);
        }


        if(0 == $this->redis->sRem($key, $value)) {
            $json['msg'] = ' delete failed';
        } else {
             $json['error'] = 0;
             $json['msg'] = ' delete the score success';
        }
        $this->return_json($json);
    }







    public function getInfo() {
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'set';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);

        $info['size']   = $this->redis->sSize($info['key']);
        $info['current'] = 1;
        $info['limit'] = 20;
        $it = isset($this->request->post['it'])?$this->request->post('it',1):-1;
        $info['pre_it'] = $it;
        $pattern = empty($this->request->post('keyword','trim'))?'':$this->request->post('keyword','trim');
        $this->getLimit($it,$info,$pattern);




        return $this->return_json($info);

    }
    public function search() {
        $keyword = $this->request->post('keyword','trim');
         $pattern = empty($keyword)?'':$keyword;
        $info = array();
        $info['exists'] = 1;
        $info['key'] = $this->getKey();
        $info['type'] = 'set';
        if(false == $this->key_exists($info['key'])) {
            $info['exists'] = 0;
        }
        $info['ttl'] = $this->redis->ttl($info['key']);

        $info['size']   = $this->redis->sSize($info['key']);
        $info['current'] = 1;
        $info['limit'] = 20;

        $it = isset($this->request->post['it'])?$this->request->post('it',1):-1;

        $this->getLimit($it,$info,$pattern);
    }


    public function getLimit($it,$info,$pattern='') {
            if($it < 0 ) {
                $it = NULL;
            }
            $limit = 5;
            $arr = array();
            $this->redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
            while($limit > 0 && ($arr_mems = $this->redis->sscan($info['key'], $it,'*'.$pattern.'*')) ) {
                foreach($arr_mems as $str_mem) {
                    //echo "Member: $str_mem\n";
                    $info['values'][] = array(
                        'value' => $str_mem,
                        'isEdit' => true,
                        'nValue' => $str_mem

                     );
                }
                $limit--;
            }

            $info['it'] = $it;
            $info['pattern'] = $pattern;
             return $this->return_json($info);

    }



}
