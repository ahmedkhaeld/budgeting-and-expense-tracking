<?php

declare(strict_types = 1);

function getTransactionFiles(string $dirPath):array {
     $files=[];
    foreach(scandir($dirPath) as $file) {
        if(is_dir($file)){
            continue;
        }
        $files[]=$dirPath. $file;
        
    }
    return $files; 
}

function getTransactions(string $fileName, ? callable $transactionHandler=null): array {
    if(!file_exists($fileName)){
        trigger_error('file"'.$fileName.'" does not exist.', E_USER_ERROR);
    }
    $file=fopen($fileName,'r');
    // read the first line and then discard it, which is the header of the transactions 
    fgetcsv($file);
    $transactions=[];
    while(($transaction=fgetcsv($file))!==false){
        if($transactionHandler!==null){
            $transaction=$transactionHandler($transaction);
        }
        $transactions[]=$transaction;
    }
    return $transactions;
}

// remove the $ and comma signs from amount to make data consistent
function extractTransaction(array $transactionRow): array{
    [$date,$checkNumber,$description, $amount]=$transactionRow;
    $amount=(float)str_replace(['$',','], '',$amount);
    return [
        'date'=>$date,
        'checkNumber'=>$checkNumber,
        'description'=>$description,
        'amount'=>$amount,
    ];
}