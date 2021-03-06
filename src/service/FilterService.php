<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service;

/**
 * Description of FilterController
 *
 * 
 */
class FilterService{
    
    //Fonctions de filtre
    //========================
    public static function trimValue(&$value)
    {
        //on enlève les espace en début et fin de la valeur
        $value = trim($value);
    }
    
    public function filterPostString($string)
    {
        $filteredString = trim(filter_input(INPUT_POST, $string, FILTER_SANITIZE_STRING));
        return $filteredString;
    }
    
    public function filterPostInt($int)
    {
        $filteredInt = trim(filter_input(INPUT_POST, trim($int), FILTER_SANITIZE_NUMBER_INT));
        return $filteredInt;
    }
    
    public function filterPostEmail($email)
    {
        $filteredEmail = trim(filter_input(INPUT_POST, trim($email), FILTER_SANITIZE_EMAIL));
        return $filteredEmail;
    }
    
    public function filterPostUrl($url)
    {
        $filteredUrl = trim(filter_input(INPUT_POST, trim($url), FILTER_SANITIZE_URL));
        return $filteredUrl;
    }
    
    public function filterPostDatetime($datetime)
    {
       // $datetime = trim(preg_replace("([^0-9/])", "", $_POST[$datetime]));
        setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
        date_default_timezone_set('Europe/Paris');
        //$datetime = new \DateTime();
        $datetime = trim(filter_input(INPUT_POST, $datetime, FILTER_SANITIZE_STRING));
        return $datetime;
    }
        
    //Not tested
    public function filterPostData()
    {
        array_filter($_POST, self::trimValue());    // the data in $_POST is trimmed
         // set up the filters to be used with the trimmed post array
        $postfilter = array(
            'user_tasks'  =>    array('filter' => FILTER_SANITIZE_STRING,
                'flags' => !FILTER_FLAG_STRIP_LOW),    // removes tags. formatting code is encoded -- add nl2br() when displaying
            'username'    =>    array('filter' => FILTER_SANITIZE_ENCODED,
                'flags' => FILTER_FLAG_STRIP_LOW),    // we are using this in the url
            'mod_title'   =>    array('filter' => FILTER_SANITIZE_ENCODED,
                'flags' => FILTER_FLAG_STRIP_LOW),    // we are using this in the url
            );
    }        
}
