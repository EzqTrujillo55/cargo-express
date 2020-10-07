<?php
class InstallHelper
{
		
	public static function addIndex($table='',$index_name='')
	{
		$DbExt=new DbExt;
		$prefix=Yii::app()->db->tablePrefix;		
		
		$table=$prefix.$table;
		
		$stmt="
		SHOW INDEX FROM $table
		";		
		$found=false;
		if ( $res=$DbExt->rst($stmt)){
			foreach ($res as $val) {				
				if ( $val['Key_name']==$index_name){
					$found=true;
					break;
				}
			}
		} 
		
		if ($found==false){
			echo "create index<br>";
			$stmt_index="ALTER TABLE $table ADD INDEX ( $index_name ) ";
			dump($stmt_index);
			$DbExt->qry($stmt_index);
			echo "Creating Index $index_name on $table <br/>";		
            echo "(Done)<br/>";		
		} else echo 'index exist<br>';
	}
	
	public static function alterTable($table='',$new_field='')
	{
		$DbExt=new DbExt;
		$prefix=Yii::app()->db->tablePrefix;		
		$existing_field='';
		if ( $res = self::checkTableStructure($table)){
			foreach ($res as $val) {								
				$existing_field[$val['Field']]=$val['Field'];
			}			
			foreach ($new_field as $key_new=>$val_new) {				
				if (!in_array($key_new,$existing_field)){
					echo "Creating field $key_new <br/>";
					$stmt_alter="ALTER TABLE ".$prefix."$table ADD $key_new ".$new_field[$key_new];
					dump($stmt_alter);
				    if ($DbExt->qry($stmt_alter)){
					   echo "(Done)<br/>";
				   } else echo "(Failed)<br/>";
				} else echo "Field $key_new already exist<br/>";
			}
		}
	}
	
    public static function checkTableStructure($table_name='')
    {
    	$db_ext=new DbExt;
    	$stmt=" SHOW COLUMNS FROM {{{$table_name}}}";	    	
    	if ($res=$db_ext->rst($stmt)){    		
    		return $res;
    	}
    	return false;    
    }      
    
    public static function isTableExist($table_name='')
    {
    	$db_ext=new DbExt;
    	$stmt="SHOW TABLE STATUS LIKE '{{{$table_name}}}'";	
    	if ($res=$db_ext->rst($stmt)){
    		return true;
    	}
    	return false;    
    }            
    
    public static function dump($data='')
    {
    	echo '<pre>';
    	print_r($data);
    	echo '</pre>';
    }
    
	public static function createFile($filename_path,$content='')
	{
		$myfile = fopen($filename_path, "w") or die("Unable to open file!".$filename_path);    
	    fwrite($myfile, $content);        
	    fclose($myfile);
	    return false;
	}    
    	
} /*end class*/