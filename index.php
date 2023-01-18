<?php
//include "vendor/autoload.php";

header('Content-Type: application/json');
$c = hrtime(true);


// read md 1 line at a time
$file = fopen('Spells.md', 'r');

$spells = [];
$current_spell = [];
while (($line = fgets($file)) !== FALSE){
    $exploded = explode(" ",$line);

    $element = array_shift($exploded);
    if($element =="##"){
        if(!empty($current_spell)){
            if(str_contains($current_spell['details'][count($current_spell['details'])-1], "[[#Tier]]")){
                $current_spell['higher cast'] = array_pop($current_spell['details']);
                
            }
            $current_spell['details'] = implode("\n", $current_spell['details']);

            $spells[] = $current_spell;
        }
        
        $current_spell = [];
        $current_spell['title'] = trim(implode(" ",$exploded));
    }elseif($element == "*"){

       
        $first = array_shift($exploded);
        if($first == "**Cantrip**"){
            $current_spell['tier'] = 'cantrip';
        }elseif($first == "**Tier"){
            $current_spell['tier'] = trim($exploded[0],"*");
        }
        elseif($first =="**Casting"){
            array_shift($exploded);
            $current_spell['casting time'] = trim(implode(" ",$exploded));
        }
        elseif($first == "**Target**"){
            $target = explode(", ", trim(implode(" ",$exploded)));
            
            $current_spell['target'] = $target[0];
            if(isset($target[1])){
                $current_spell['defense'] = $target[1];
            }
        }
        
    }
    elseif($element !== "#"){ 
        if(trim($element) != ""){
            array_unshift($exploded, $element);
            $current_spell['details'][] = trim(implode(" ",$exploded));
        }
    }
}
$spells[] = $current_spell;
$spells['executed'] = (hrtime(true) - $c)/1000;
echo json_encode($spells);

