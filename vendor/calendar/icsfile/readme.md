# iCalendar generator
This simple class generate a *.ics file. require php >= 5.4.2.
## Install with composer
```
require "calendar/icsfile": "dev-master"
```
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/739b9b66-3fe3-4f5e-93a7-d7fe7d874707/big.png)](https://insight.sensiolabs.com/projects/739b9b66-3fe3-4f5e-93a7-d7fe7d874707)
## Usage 

```php

<?php  
    require_once 'vendor/autoload.php';

    use Ical\Ical;
try {
 
        $ical = (new Ical())->setAddress('Paris')
                ->setDateStart('2014-11-21 15:00:00')
                ->setDateEnd('2014-11-21 16:00:00')
                ->setDescription('wonder description')
                ->setSummary('Running')
                ->setFilename(uniqid());
        $ical->setHeader();
       
    echo $ical->getICAL();          
  
        } catch (\Exception $exc) {
            echo $exc->getMessage();
}
 

```
