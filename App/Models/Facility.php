<?php

namespace App\Models;

use DateTime;

class Facility
{
    private int $facility_id;
    private string $name;
    private DateTime $creation_date;
    private Location $location;
}