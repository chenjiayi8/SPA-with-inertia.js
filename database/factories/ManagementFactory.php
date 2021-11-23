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
        $group['id'] = 0;
        $group['created_at'] = $date;
        $group['name'] = $this->faker->word();
        $group['color'] = $this->faker->hexColor();
        $group['statuses'] = $this->fakeStatuses(5);
        $group['priorities'] = $this->fakePriorities(5);
        $group['items'] = $this->fakeItems(2);
        $group['items_format'] = [
            ['name' => 'Name', 'type' => 'text', 'width' => 15],
            ['name' => 'Person', 'type' => 'person', 'width' => 10],
            ['name' => 'Time Tracking', 'type' => 'tracking', 'width' => 10],
            ['name' => 'Note', 'type' => 'longtext', 'width' => 20],
            ['name' => 'Subitems', 'type' => 'subitem', 'width' => 10],
            ['name' => 'Status', 'type' => 'status', 'width' => 10],
            ['name' => 'Priority', 'type' => 'priority', 'width' => 10],
            ['name' => 'Due Date', 'type' => 'date', 'width' => 10],
            ['name' => 'LastUpdated', 'type' => 'datetime', 'width' => 15],
        ];
        $group['LastUpdated'] = $date;
        return $group;
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
        $time_tracking = ['records' => $this->fakerTimeTrackingRecords(3), 'total' => 0.0];
        $item['Time Tracking'] = $time_tracking;
        $item['Time Tracking'] = $this->updateTimeHours($item['Time Tracking']);
        $item['Note'] = $this->faker->paragraph();
        $item['Subitems'] = $this->fakeSubitems(2);
        $item['Status'] = rand(0, 4);
        $item['Priority'] = rand(0, 4);
        $item['Due Date'] = $this->faker->date();
        $item['LastUpdated'] = $date;
        $item['subitems_format'] = [
            ['name' => 'Name', 'type' => 'text', 'width' => 15],
            ['name' => 'Person', 'type' => 'person', 'width' => 10],
            ['name' => 'URL', 'type' => 'url', 'width' => 20],
            ['name' => 'Status', 'type' => 'status', 'width' => 10],
            ['name' => 'Note', 'type' => 'longtext', 'width' => 20],
            ['name' => 'LastUpdated', 'type' => 'datetime', 'width' => 15],
        ];
        return $item;
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
        $subItem['Note'] = $this->faker->paragraph();
        $subItem['Status'] = rand(0, 4);
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


    public function updateTimeHours($time_tracking)
    {
        $records = $time_tracking['records'];
        $hours = 0.0;
        foreach ($records as $record) {
            $hours += $record['Work hour'];
        }
        $time_tracking['total'] = round($hours, 2);
        return $time_tracking;
    }

    public function appendGroups($numGroups)
    {
        $collection = Management::first();
        $groups = $collection->groups;
        for ($i = 0; $i <= $numGroups; $i++) {
            $group = $this->createGroup();
            $group['id'] = $i+1;
            array_push($groups, $group);
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
