<?php
/**
 *
 * @author al
 *
 */
class Tools_Profiler
{
    private static $_off = false;
    public static $instances;
    protected $sTimeStart;
    protected $sTime;
    public static $timeStartCommon;
    public static $timeFinCommon;
    const FORMAT_PLAIN = 'text';
    const FORMAT_HTML = 'html';
    protected static $_prefix;
    protected static $_prefixType;
    protected static $_logfile;
    private function __construct($key)
    {
        try
        {
            if(isset(self::$instances[$key]) && self::$instances[$key] instanceof self )
            {
                throw new InvalidArgumentException("Key {$key} is current in use, this can occur with repeated calls to profiler, choose another key ident please.");
            }
        }
        catch (InvalidArgumentException $e)
        {
        	$l = 0;
        	if(self::$_logfile) {$l = 3;}
            error_log(getmygid().', '.$e->getMessage().PHP_EOL.$e->getTraceAsString().PHP_EOL.PHP_EOL,$l,self::$_logfile);
        }
    }
    public static function setLogFile($file)
    {
        if(is_file($file) && is_writable($file))
        {
        	self::$_logfile = $file;
        }
    }
    public static function off()
    {
        self::$_off = true;
    }
    public static function start($key)
    {
        if(self::$_off) return;
        self::$instances[$key] = new self($key);
        self::$instances[$key]->sTimeStart = microtime(1);
        if(!self::$timeStartCommon){self::$timeStartCommon = self::$instances[$key]->sTimeStart;}
    }
    public static function finish($key)
    {
        if(self::$_off) return;
        self::$instances[$key]->sTime = microtime(1);
        self::$timeFinCommon = self::$instances[$key]->sTime;
    }
    public static function show()
    {
        if(self::$_off) return;
        print self::formatout(self::FORMAT_HTML);
    }
    public static function showPlain()
    {
        if(self::$_off) return;
        ini_set('html_errors',false);
        print self::formatout(self::FORMAT_PLAIN);
    }
    private static function formatout($type)
    {
    	if(empty(self::$instances))
    	{
    	   return;
    	}
        $print = ((self::FORMAT_HTML == $type) ? '<div style="position:absolute; bottom: -1000px;">'.PHP_EOL:PHP_EOL);
        foreach (self::$instances as $key=>$profiler)
        {
            if(isset($profiler->sTime) && isset($profiler->sTimeStart))
            {
                $t = self::calc($profiler->sTimeStart, $profiler->sTime);
                if(self::FORMAT_PLAIN == $type)
                {
                    $sl = strlen($key);
                    if($sl < 14){$tab = "\t\t";}
                    else {$tab = "\t";}
                    $print .= self::getPrefix() ." " .round($t,5). "\t[{$key}] Wrapped code uses sec.{$tab}[MemUsage] " . (memory_get_usage(1)/1024) ." KB.".PHP_EOL;
                }
                else if(self::FORMAT_HTML == $type)
                {
                    $print .= "<p class='xdebug-var-dump' dir='ltr'><small>".self::getPrefix() ."[<b>".$key."</b>] Wrapped code uses </small>".PHP_EOL ;
                    $print .= "<font color='#f57900'>$t</font> <small>sec.</small></p>".PHP_EOL;
                }
            }
            else
            {
                if(self::FORMAT_PLAIN == $type)
                {
                    $print .= self::getPrefix()."Started profiler $key was not started or is unfinished.".PHP_EOL;
                }
                else if(self::FORMAT_HTML == $type )
                {
                    $print .= '<p>'.self::getPrefix().'Started profiler '. $key . ' is unfinished.</p>'.PHP_EOL;
                }
            }
        }
        $tf = self::calc(self::$timeStartCommon, self::$timeFinCommon) ;
        if(self::FORMAT_PLAIN == $type)
        {
            $print .= self::getPrefix() ."\t[The time from first profiler start to last profiler finish] $tf sec. [MemUsagePeak] " . (memory_get_peak_usage(1)/1024) . " KB.".PHP_EOL;//JLo
        }
        else if(self::FORMAT_HTML == $type)
        {
            $print .= "<p class='xdebug-var-dump' dir='ltr'><small>".self::getPrefix() ."[<b>The time from first profiler start to last profiler finish</b>]</small>".PHP_EOL;
            $print .= "<font color='#f57900'>$tf</font> <small>sec.</small></p>".PHP_EOL;
        }
        return $print .((self::FORMAT_HTML == $type)?'</div>':'');
    }
    public static function write($fileName)
    {
        if(self::$_off) return;
        ob_start();
        self::showPlain();
        //$c = ob_get_clean();
        file_put_contents($fileName, ob_get_clean(), FILE_APPEND);
//        $fp = fopen($fileName,'a');
//        fwrite($fp,$c);
//        fclose($fp);
    }
    public static function calc($timestart, $timeend)
    {
        return $timeend - $timestart;
    }
    public static function setFormatOutPrefix()
    {
    
    }
    public static function setFormatOutPrefixTimestamp()
    {
        self::$_prefixType = "D";
        self::$_prefix = date('Y-m-d H:i:s') . ', ';
    }
    public static function setFormatOutPrefixTimestampPid()
    {
    	self::$_prefixType = "DP";
        self::$_prefix = date('Y-m-d H:i:s') . ', '. getmypid() .', ';
    }
    public static function setFormatOutPrefixTimestampPidSessId()
    {
        //self::$_prefix = date('Y-m-d H:i:s') . ', PID:'. getmypid() .', SESS:'.var_export(session_id(),1). ', ';
        self::$_prefixType = "DPS";
        self::$_prefix =  date('Y-m-d H:i:s') . ', PID:'. getmypid() .', SESS:%s, ';
    }
    public static function setFormatOutPrefixTimestampPidSessIdSessContent()
    {
        self::$_prefixType = "DPS";
        self::$_prefix =  date('Y-m-d H:i:s') . ', PID:'. getmypid() .', SESS:%s, '. json_encode($_SESSION);
    }
    public static function getPrefix()
    {
        switch (self::$_prefixType)
        {
        	case 'DPS':return sprintf(self::$_prefix,session_id());
        	case 'DP':return self::$_prefix;
        	case 'D':return self::$_prefix;
        	default: return "";
        }
        
    }
    
}
