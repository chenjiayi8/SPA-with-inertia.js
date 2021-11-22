<?php

namespace Database\Factories;

use App\Models\Management;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class ManagementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function createGroup()
    {
        $date = new UTCDateTime;
        $date->toDateTime();
        $group = json_decode('');
        $group['id'] = (string)new ObjectId;
        $group['created_at'] = $date;
        $group['name'] = $this->faker->word();
        $group['color'] = $this->faker->hexColor();
        $group['statuses'] = $this->fakeStatuses(5);
        $group['priorities'] = $this->fakePriorities(5);
        $group['items'] = $this->fakeItems(2);
        $group['LastUpdated'] = $date;
        return $group;
    }

    public function fakeItems($numItems)
    {
        $items = [];
        for ($i = 0; $i <= $numItems; $i++) {
            $item = $this->fakeItem();
            $item['id'] = $i;
            array_push($items, $item);
        }
        return $items;
    }

    public function fakeItem()
    {
        $user = User::first();
        $date = new UTCDateTime;
        $item = json_decode('');
        $item['id'] = 0;
        $item['created_at'] = $date;
        $item['Name'] = $this->faker->word();
        $item['Person'] = [$user->id];
        $item['Time Tracking'] = $this->fakerTimeTrackingRecords(3);
        $item['Time hours'] = $this->setTimeHours($item['Time Tracking']);
        $item['Subitems'] = $this->fakeSubitems(2);
        $item['Status'] = rand(0, 4);
        $item['Priority'] = rand(0, 4);
        $item['Due Date'] = $this->faker->date();
        $item['Note'] = $this->faker->paragraph();
        $item['LastUpdated'] = $date;
        return $item;
    }

    public function fakeSubitems($numSubitems)
    {
        $subItems = [];
        for ($i = 0; $i <= $numSubitems; $i++) {
            $subItem = $this->fakeSubItem();
            $subItem['id'] = $i;
            array_push($subItems, $subItem);
        }
        return $subItems;
    }

    public function fakeSubItem()
    {
        $user = User::first();
        $date = new UTCDateTime;
        $subItem = json_decode('');
        $subItem['id'] = 0;
        $subItem['created_at'] = $date;
        $subItem['Name'] = $this->faker->word();
        $subItem['Person'] = [$user->id];
        $subItem['URL'] = $this->faker->url();
        $subItem['Status'] = rand(0, 4);
        $subItem['Note'] = $this->faker->paragraph();
        $subItem['LastUpdated'] = $date;
        return $subItem;
    }

    public function fakeStatuses($num)
    {
        $statuses = [];
        for ($i = 0; $i < $num; $i++) {
            $status = json_decode('');
            $status['name'] = $this->faker->word();
            $status['color'] = $this->faker->hexColor();
            array_push($statuses, $status);
        }
        return $statuses;
    }

    public function fakePriorities($num)
    {
        $priorities = [];
        for ($i = 0; $i < $num; $i++) {
            $priority = json_decode('');
            $priority['name'] = $this->faker->word();
            $priority['color'] = $this->faker->hexColor();
            array_push($priorities, $priority);
        }
        return $priorities;
    }

    public function fakerTimeTrackingRecords($numRecords)
    {
        $records = [];
        for ($i = 0; $i <= $numRecords; $i++) {
            $record = $this->fakerTimeTrackingRecord();
            $record['id'] = $i;
            array_push($records, $record);
        }
        return $records;
    }

    public function fakerTimeTrackingRecord()
    {
        $record = json_decode('');
        $date = new UTCDateTime;
        $record['id'] = 0;
        $record['Start time'] = $date;
        $add_hours = rand(0, 2);
        $add_minutes = 1 + 60 * (mt_rand() / mt_getrandmax());
        $add_time_string = '+ ';
        $add_time_string .= $add_hours ? sprintf('%d hour ', $add_hours) : '';
        $add_time_string .= $add_minutes ? sprintf('%d minute ', $add_minutes) : '';
        $date_new = $date->toDateTime();
        $date_new->modify($add_time_string);
        $record['End time'] = new UTCDateTime($date_new);
        $record['Remark'] = '';
        $record['Work hour'] = round($add_hours + $add_minutes / 60, 2);
        return $record;
    }


    public function setTimeHours($records)
    {
        $hours = 0.0;
        foreach ($records as $record) {
            $hours += $record['Work hour'];
        }
        return round($hours, 2);
    }

    public function appendGroups($numGroups)
    {
        $collection = \App\Models\Management::first();
        $groups = $collection->groups;
        for ($i = 0; $i <= $numGroups; $i++) {
            array_push($groups, $this->createGroup());
        }
        $collection->groups = $groups;
        $collection->save();
    }

    public function definition()
    {
        $user_id = User::factory();
        return [
            'user_id' => $user_id,
            'workspace' => $this->faker->word(),
            'groups' => [$this->createGroup()],
        ];
    }
}
