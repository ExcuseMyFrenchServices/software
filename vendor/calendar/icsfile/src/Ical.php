<?php
/* 
 * Copyright (C) 2014 luc Sanchez.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Ical;

class Ical
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var datetime
     */
    private $timezoneICal = 'Europe/Paris';
    
    /**
     *
     * @var string
     * @abstract the starting date (in seconds since unix epoch)
     */
    private $dateStart;
    
    /**
     *
     * @var string
     * @abstract text title of the event
     */
    private $summary;
    
    /**
     *
     * @var datetime
     * @abstract the ending date (in seconds since unix epoch)
     */
    private $dateEnd;
    /**
     *
     * @var string 
     * @abstract the name of this file for saving (e.g. my-event-name.ics)
     */
    private $filename;
    
    /**
     *
     * @var string
     * @abstract the event's address
     */
    private $address;
    
    /**
     *
     * @var string text description of the event
     */
    private $description;

    /**
     *
     * @var bool default false 
     */
    private $alarm        = false;

    /**
     *
     * @var bool default false 
     */
    private $repeat       = false;

    public function getName() {
        return $this->name;
    }

    public function getTimezoneICal() {
        return $this->timezoneICal;
    }

    public function getDateStart() {
        return $this->dateStart;
    }

    public function getSummary() {
        return $this->summary;
    }

    public function getDateEnd() {
        return $this->dateEnd;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getAlarm() {
        return $this->alarm;
    }

    public function getRepeat() {
        return $this->repeat;
    }

    /**
     * 
     * @param type $dateEventStart
     * @return \ical\ical
     */
    public function setDateEventStart($dateEventStart) {
        $this->dateEventStart = $dateEventStart;
        return $this;
    }

    /**
     * 
     * @param type $dateEventEnd
     * @return \ical\ical
     */
    public function setDateEventEnd($dateEventEnd) {
        $this->dateEventEnd = $dateEventEnd;
        return $this;
    }

    /**
     * 
     * @param type $name
     * @return \ical\ical
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param type $timezoneICal
     * @return \ical\ical
     */
    public function setTimezoneICal($timezoneICal) {
        $this->timezoneICal = $timezoneICal;
        return $this;
    }

    /**
     * 
     * @param \DateTime $dateStart
     * @return \ical\ical
     */
    public function setDateStart(\DateTime $dateStart) {
        $this->dateStart = $this->dateToCal($this->getHumanToUnix($dateStart->format('Y/m/d H:i:s')));
        return $this;
    }

    /**
     * 
     * @param type $summary
     * @return \ical\ical
     */
    public function setSummary($summary) {
        $this->summary = $summary;
        return $this;
    }

    /**
     * 
     * @param \DateTime $dateEnd
     * @return \ical\ical
     */
    public function setDateEnd(\DateTime $dateEnd) {
        $this->dateEnd = $this->dateToCal($this->getHumanToUnix($dateEnd->format('Y/m/d H:i:s')));
        return $this;
    }

    /**
     * 
     * @param type $filename
     * @return \ical\ical
     */
    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    /**
     * 
     * @param type $address
     * @return \ical\ical
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    /**
     * 
     * @param type $description
     * @return \ical\ical
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * 
     * @param type $alarm
     * @return \ical\ical
     * @throws \Exception
     */
    public function setAlarm($alarm) {

        if (is_int($alarm)) {
            $this->alarm = $alarm;
            return $this;
        } else {
            throw new \Exception(__CLASS__ . " : It's not an integer", 500);
        }
    }

    /**
     * 
     * @param type $repeat
     * @return \ical\ical
     */
    public function setRepeat($repeat) {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * @name getICAL()
     * @access public
     * @return string $iCal
     */
    public function getICAL() {

        $iCal = "BEGIN:VCALENDAR" . "\r\n";
        $iCal .= 'VERSION:2.0' . "\r\n";
        $iCal .= "PRODID:" . $this->getName() . "\r\n";
        $iCal .= "CALSCALE:GREGORIAN " . "\r\n";
        $iCal .= "BEGIN:VEVENT" . "\r\n";
        $iCal .= "DTSTART:" . $this->getDateStart() . "\r\n";
        $iCal .= "DTEND:" . $this->getDateEnd() . "\r\n";
        $iCal .= "SUMMARY:" . $this->escapeString($this->getSummary()) . "\r\n";
        $iCal .= 'UID:' . uniqid() . "\r\n";
        $iCal .= 'LOCATION: ' . $this->escapeString($this->getAddress()) . "\r\n";
        $iCal .= 'DESCRIPTION:' . $this->escapeString($this->getDescription()) . "\r\n";

        if ($this->getAlarm()) {
            $iCal .= 'BEGIN:VALARM' . "\r\n";
            $iCal .= 'ACTION:DISPLAY' . "\r\n";
            $iCal .= 'DESCRIPTION:Reminder' . "\r\n";
            $iCal .= 'TRIGGER:-PT' . $this->getAlarm() . 'M' . "\r\n";
            if ($this->getRepeat()) {
                $iCal .= 'REPEAT:' . $this->getRepeat() . "\r\n";
            }
            $iCal .= "END:VALARM" . "\r\n";
        }

        $iCal .= 'END:VEVENT' . "\r\n";
        $iCal .= 'END:VCALENDAR' . "\r\n";
        return $iCal;
    }

    /**
     * @name dateToCal()
     * @access private
     * @param \DateTime $timestamp
     * @return string
     */
    private function dateToCal(\DateTime $timestamp) {

        return $timestamp->format('Ymd\THis\Z');
    }

    /**
     * @name escapeString()
     * @abstract Escapes a string of characters
     * @param string $string
     * @return string
     */
    private function escapeString($string) {
        return preg_replace('/([\,;])/', '\\\$1', $string);
    }

    /**
     * @name $addHeader();
     * @return headers and file
     */
    public function addHeader() {
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $this->getFilename() . '.ics');
    }

    /**
     * @name humanToUnix()
     * @param string $datestr like Y-m-d
     * @return bool
     * @return integer
     */
    private function getHumanToUnix($datestr = '') {
        if ($datestr === '') {
            return false;
        }
        $datestr = preg_replace('/\040+/', ' ', trim($datestr));

        sscanf($datestr, '%d/%d/%d %s %s', $year, $month, $day, $time, $ampm);

        sscanf($time, '%d:%d:%d', $hour, $min, $sec);
        isset($sec) or $sec = 0;
        if (isset($ampm)) {
            $ampm = strtolower($ampm);
            if ($ampm[0] === 'p' && $hour < 12) {
                $hour += 12;
            } elseif ($ampm[0] === 'a' && $hour === 12) {
                $hour = 0;
            }
        }

        $return = new \DateTime();
        $return->setTimestamp(mktime($hour, $min, $sec, $month, $day, $year));
        return $return;
    }

}
