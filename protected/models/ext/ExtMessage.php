<?php
class ExtMessage extends Message
{
    
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    public function getMessage($currLang)
    {
      $arrMessages = array();
      
      $sql = "SELECT message.text,message_trl.translation FROM message
                JOIN message_trl,languages
                    WHERE message.id = message_trl.message_id
                    AND languages.id = message_trl.language_id
                    AND languages.prefix = :prefix";
                    
        $params[':prefix'] = $currLang;
        
        $con = $this->dbConnection;
        $data=$con->createCommand($sql)->queryAll(true,$params);
        
        foreach($data as $row){
            $arrMessages[$row['text']] = $row['translation'];
        }
        
        return $arrMessages;              
    }//getMessage
    
    /* By Maxim */

    public function getMessages($lng,$cond=array()){
        $sql = "SELECT t1.id,t2.text,t1.translation, t2.id AS message_id
            FROM message_trl t1
            JOIN message t2 ON t1.message_id = t2.id
            JOIN languages t3 ON t1.language_id = t3.id
          WHERE t3.prefix = :prefix";

        $param = array();
        
        if(!empty($cond['search_label'])){
             $sql .= " AND t2.text LIKE :label";
             $param[':label'] = "%{$cond['search_label']}%";

        }
        
        //add order
        $sql .= " ORDER BY t1.message_id DESC"; 
        
        
        $param[':prefix'] = $lng;
        $con = $this->dbConnection;        
        $retData = $con->createCommand($sql)->queryAll(true,$param);
        return $retData;        
    }//getMessages
}