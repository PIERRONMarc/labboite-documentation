<?php

namespace App\Service;

use App\Entity\Tool;

/**
 * Header utility class 
 */
class HeaderHelper {

    /**
     * Return all non empty section of the tool header as an array
     *
     * @return array
     */
    public function getToolHeader(Tool $tool) {
        $header = ['information'];

        if ($tool->getNotice()) {
            array_push($header, 'notice');
        }

        if ($tool->getConsumable()->count()) {
            array_push($header, 'consumable');
        }

        if ($tool->getQuestion()->count()) {
            array_push($header, 'question');
        }

        if ($tool->getTips()->count()) {
            array_push($header, 'tip');
        } 

        return $header;
    }
}