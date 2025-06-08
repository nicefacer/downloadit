<?php

class ML_Database_Model_Query_TableSchema{
    protected $sTable='';
    protected $aColumns=array();
    protected $aKeys=array();
    public function setTable($sTable){
        $this->sTable=$sTable;
        return $this;
    }
    public function setColumns($aColumns){
        foreach ($aColumns as $sKey=>$aValue){
            $this->aColumns[$sKey]=array(
                'Type'=>$aValue['Type'],
                'Null'=>$aValue['Null'],
                'Default'=>$aValue['Default'],
                'Extra'=>$aValue['Extra'],
                'Comment'=>$aValue['Comment']
            );
        }
        return $this;
    }
    public function setKeys($aKeys){
        foreach($aKeys as $sKey=>$aValue){
            $this->aKeys[$sKey]=array(
                'Non_unique'=>$aValue['Non_unique'],
                'Column_name'=>$aValue['Column_name'],
            );
        }
        return $this;
    }
    public function update(){
        if(MLDatabase::getDbInstance()->tableExists($this->sTable)){
            $this->alterTable();
        }else{
            $this->createTable();
        }
        return $this;
    }
    protected function alterTable(){
        $sSql="ALTER TABLE `".$this->sTable."`\n";
        // 1. check columns
        $aCurrentColumns = array(); 
        foreach(MLDatabase::getDbInstance()->fetchArray("SHOW FULL COLUMNS FROM `".$this->sTable."`") as $aColumn){
            
            $aCurrentColumns[$aColumn['Field']]=array(
                'Type'=>$aColumn['Type'],
                'Null'=>$aColumn['Null'],
                'Default'=>$aColumn['Default'],
                'Extra'=>$aColumn['Extra'],
                'Comment'=>$aColumn['Comment']
            );
        }
        // 1.1 drop column
        foreach(array_keys($aCurrentColumns) as $sCurrent){
            if(!isset($this->aColumns[$sCurrent])){
                $sSql.="    DROP COLUMN `".$sCurrent."`,\n";
            }
        }
        // 1.2 add column, modify columns
        foreach($this->aColumns as $sColumn=>$aColumn){
            if(
                !isset($aCurrentColumns[$sColumn])
                ||count(array_diff_assoc($aColumn, $aCurrentColumns[$sColumn]))>0
            ){
                if(!isset($aCurrentColumns[$sColumn])){
                    $sSql.= "   ADD COLUMN";
                }else{
                    $sSql.= "   MODIFY COLUMN";
                }
                $sSql.=$this->buildColumn($sColumn, $aColumn).", \n";
            }
        }
        // 2. check keys
        $aCurrentKeys=array();
        foreach(MLDatabase::getDbInstance()->fetchArray("SHOW INDEX FROM `".$this->sTable."`") as $aKey){
            $aCurrentKeys[$aKey['Key_name']]=array(
                'Non_unique' => $aKey['Non_unique'],
                'Column_name' => isset($aCurrentKeys[$aKey['Key_name']]['Column_name'])
                    ?$aCurrentKeys[$aKey['Key_name']]['Column_name'].', '.$aKey['Column_name']
                    :$aKey['Column_name'],
            );
        }
        // 2.1 drop key
        foreach(array_keys($aCurrentKeys) as $sCurrent){
            if(!isset($this->aKeys[$sCurrent])){
                $sSql.="    DROP KEY `".$sCurrent."`,\n";
            }
        }
        
        // 1.2 drop changed key and add new or changed Key
        foreach($this->aKeys as $sKey=>$aKey){
            if(
                !isset($aCurrentKeys[$sKey])
                ||count(array_diff_assoc($aKey, $aCurrentKeys[$sKey]))>0
            ){
                if(isset($aCurrentKeys[$sKey])){
                    if($sKey=='PRIMARY'){
                        $sSql.="    DROP PRIMARY KEY,\n";
                    }else{
                        $sSql.="    DROP KEY `".$sKey."`,\n";
                    }
                }
                $sSql.="    ADD ".$this->buildKey($sKey, $aKey).",\n";
            }
        }
        if(strrpos($sSql, ',')!==false){
            $sSql=  substr($sSql, 0, strrpos($sSql, ','))."\n";
            MLDatabase::getDbInstance()->query($sSql);
            MLMessage::gi()->addDebug('Schema:<br />'.$sSql);
        }
    }
    protected function createTable(){
        $sSql=      "CREATE TABLE `".$this->sTable."`(\n";
        foreach($this->aColumns as $sColumn=>$aColumn){
            $sSql.="    ".$this->buildColumn($sColumn, $aColumn).",\n";
        }
        foreach($this->aKeys as $sKey=>$aKey){ 
            $sSql.="    ".$this->buildKey($sKey, $aKey).",\n";
        }
        $sSql=  substr($sSql, 0, strrpos($sSql, ','))."\n";
        $sSql.=     ");";
        MLDatabase::getDbInstance()->query($sSql);
        if (MLDatabase::getDbInstance()->tableExists($this->sTable, true)) {
            MLMessage::gi()->addDebug('Schema:<br />'.$sSql);
        } else {
            MLMessage::gi()->addDebug('Schema:<br />'.$sSql);
            MLMessage::gi()->addWarn('Schema:<br />'.MLDatabase::getDbInstance()->getLastError());
        }
    }
    protected function buildColumn($sColumn, $aColumn){
        return 
            "`".$sColumn."` ".
            $aColumn['Type']." ".
            ($aColumn['Null']=='NO'?"NOT":"")." Null".
            ($aColumn['Default']!==null?" default '".$aColumn['Default']."' ":"").
            ($aColumn['Extra']!=''?" ".$aColumn['Extra']:"").
            ' COMMENT '.($aColumn['Comment']!=''?" '".$aColumn['Comment']."' ":"''")
        ;
    }
    protected function buildKey($sKey, $aKey){
        if($sKey=='PRIMARY'){
            $sSql="PRIMARY KEY (";
        }elseif($aKey['Non_unique']=='0'){
            $sSql="UNIQUE KEY `".$sKey."` (";
        }else{
            $sSql="KEY `".$sKey."` (";
        }
        $sSql.=$aKey['Column_name'].")";
        return  $sSql;
    }
}
