<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

function query($sql, $data=[], $check = false) {
    global $conn;
    $result = false;
    try {
        $statement = $conn -> prepare($sql);

        if(!empty($data)) {
            $result = $statement -> execute($data);
        }
        else { 
            $result = $statement -> execute();
        }
    }
    catch (Exception $exp) {
        echo $exp -> getMessage().'<br>';
        echo 'File: '. $exp -> getFile().'<br>';
        echo 'Line: '. $exp -> getLine();
        die();
    }

    if ($check) {
        return $statement;
    }

    return $result;
}

function insert($table, $data) {
    $key = array_keys($data);
    $field = implode(',', $key);
    $valuetb = ':'.implode(',:', $key);

    $sql = 'INSERT INTO ' . $table . ' ('.$field.')'. ' VALUES('.$valuetb.')';

    $rs = query($sql, $data);
    return $rs;
}


function update($table, $data, $condition='') { 
    $update = '';
    foreach($data as $key => $value){
        $update .= $key .'= :' . $key .',';
    }
    $update = trim($update, ',');
    if (!empty($condition)) {
        $sql = 'UPDATE ' . $table . ' SET ' .$update .' WHERE ' . $condition;
    }
    else {
        $sql = 'UPDATE ' . $table . ' SET ' .$update;
    }
    $rs = query($sql, $data);
    return $rs;
}


function delete($table, $condition='') { 
    if (empty($condition)) {
        $sql = 'DELETE FROM ' . $table;
    }
    else {
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $condition;
    }
    $rs = query($sql);
    return $rs;
}


function getAll($sql) {
    $rs = query($sql, '', true);
    if(is_object($rs)) { 
        $dataFetch = $rs -> fetchAll(PDO::FETCH_ASSOC);
    }
    return $dataFetch;
}

function getRow($sql) {
    $rs = query($sql, '', true);
    if(is_object($rs)) { 
        $dataFetch = $rs -> fetch(PDO::FETCH_ASSOC);
    }
    return $dataFetch;
}

function countRows($sql) {
    $rs = query($sql, '', true);
    if(!empty($rs)) { 
        return $rs -> rowCount();
    }
}