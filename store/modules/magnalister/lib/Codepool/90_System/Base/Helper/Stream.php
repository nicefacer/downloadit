<?php
class ML_Base_Helper_Stream{
    protected $iLength=175;
    protected static $iDeph=0;
    protected static $blOut=false;
    public function activateOutput(){
        self::$blOut=microtime(true);
        return $this;
    }
    public function stream($sIn,$blTime=true){
        $aArray=explode("\n",$sIn);
        $iDeph=self::$iDeph*2;
        $iDeph = max (0, $iDeph);
        $iLength=$this->iLength-$iDeph;
        $sOut='';
        foreach ($aArray as $iKey => $sString) {
            $sOut.=str_repeat(' ', $iDeph) . '## ' . $sString;
            if ($iKey == 0 && $blTime !== false) {
                if ($blTime===true) {
                    $fTime = (string) microtime(true);
                    $fTime = substr($fTime, -(strlen($fTime) - strrpos($fTime, '.') - 1));
                    if (strlen($fTime) < 4) {
                        $fTime.=str_repeat('0', 4 - strlen($fTime));
                    }
                    $sDate = date('Y-m-d H:i:s') . '.' . $fTime;
                } else {
                    $sDate = $blTime;
                }
                $sOut.=str_repeat(' ', $iLength - strlen($sDate) - strlen($sString)) . " " . $sDate;
            }
            $sOut.="\n";
        }
        $this->out($sOut);
        return $this;
    }
    public function streamCommand($aArray){
        $sString=json_encode($aArray);
        $this->stream($sString,'{#'.base64_encode($sString).'#}');
        return $this;
    }
    public function deeper($sMessage=''){
        $this->stream($sMessage.'{',false);
        ++self::$iDeph;
        return $this;
    }
    public function higher($sMessage=''){
        --self::$iDeph;
        $this->stream('}'.$sMessage,false);
        return $this;
    }
    protected function out($sOut){
        if(self::$blOut){
            echo $sOut,
            flush();
        }
        return $this;
    }
}