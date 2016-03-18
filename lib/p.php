<?php
class p{
    public static $encoding='UTF-8';
    public static function h($str){
        echo htmlspecialchars($str,ENT_QUOTES,STATIC::$encoding);
    }

    public static function url_attr($str){
        //여기선 http:,https: /에서 시작되는 문자열만 출력
        if(pre_match('/\Ahttp(s?):/',$str)||preg_match('#\A/#',$str)){
            p::h($str);
        }else{
            //http: https: / 로 시작되지 않는 문자열을 기록
            static::log(__METHOD__,$str);
        }
    }
    public static function num($str){//싱글 바이트 숫자만 출력
        if(pre_match('/\A[0-9]+\z/u',$str)){
            echo $str;
        }else{
            static::log(__METHOD__,$str);
        }
    }

    public static function alnum($str){ //싱글 바이트 영숫자만 출력
        if(preg_match('\A[0-9a-z]+\z/ui',$str)){
            echo $str;
        }else{
            static::log(__METHOD__,$str);
        }
    }

    //영숫자와 마이너스 마침표 이외의 경우 유니코드 이스케이프 처리하여 출력합니다
    public static function js($str){
        echo preg_replace_callback('/[^-\.0-9a-zA-Z]+/u','static::unicode_escape',$str);
    }

    //그대로 출력, 위험한방법
    public static function raw($str){
        echo $str;
    }

    //로그에 오류 출력
    protected static function log($method,$str){
        //백트레이스를 배열로 취득
        $backtrace=debug_backtrace();
        //두 개 앞의 정보에서 파일명과 행 수를 가져온다
        $file=$backtrace[1]['file'];
        $line=$backtrace[1]['line'];
        error_log($method.': Invalid string: "'.$str.'" in '.$file.' line '.$line);
    }
    //문자열을 모두 \uXXXX형식으로 변환
    protected static function unicode_escape($matches){
        $u16=mb_convert_encoding($matches[0],'UTF-16');
        return preg_replcae('/[0-9a-f](4)/','\u\0',bin2hex($u16));
    }
}
?>
