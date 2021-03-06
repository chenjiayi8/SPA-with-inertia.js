<?php

namespace Database\Factories;

use App\Models\Management;
use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\UTCDateTime;

class ManagementFactory extends Factory
{
    public function appendGroups($numGroups)
    {
        $collection = Management::first();
        $groups = $collection->groups;
        for ($i = 0; $i <= $numGroups; $i++) {
            $group = $this->createGroup();
            $group['id'] = $i + 1;
            array_push($groups, $group);
        }
        $collection->groups = $groups;
        $collection->save();
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function createGroup(): array
    {
//        $date = new UTCDateTime;
//        $date->toDateTime();
        $group = json_decode('');
        $group['id'] = 0;
        $group['created_at'] = (int)((float)now()->format('Uu')/1000);
        $group['name'] = $this->faker->word();
        $group['color'] = $this->faker->hexColor();
        $group['statuses'] = $this->fakeStatuses(5);
        $group['priorities'] = $this->fakePriorities(5);
        $group['items'] = $this->fakeItems(2);
        $group['formats'] = [
            ['name' => 'Name', 'type' => 'text', 'width' => 15],
            ['name' => 'Person', 'type' => 'person', 'width' => 10],
            ['name' => 'Time Tracking', 'type' => 'tracking', 'width' => 15],
            ['name' => 'Note', 'type' => 'longtext', 'width' => 20],
            ['name' => 'subitems', 'type' => 'subitem', 'width' => 10],
            ['name' => 'Status', 'type' => 'status', 'width' => 10],
            ['name' => 'Priority', 'type' => 'priority', 'width' => 10],
            ['name' => 'Due Date', 'type' => 'date', 'width' => 10],
            ['name' => 'LastUpdated', 'type' => 'datetime', 'width' => 10],
        ];
        $group['LastUpdated'] = (int)((float)now()->format('Uu')/1000);
        return $group;
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

    public function fakeItems($numItems): array
    {
        $items = [];
        for ($i = 0; $i <= $numItems; $i++) {
            $item = $this->fakeItem();
            $item['id'] = $i;
            array_push($items, $item);
        }
        return $items;
    }

    public function getFakeDate(){
        return $this->faker->date();
    }

    public function fakeItem()
    {
        $item = json_decode('');
        $item['id'] = 0;
        $item['created_at'] = (int)((float)now()->format('Uu')/1000);
        $item['Name'] = $this->faker->word();
        $item['Person'] = ['0', '1'];
        $time_tracking = ['records' => $this->fakerTimeTrackingRecords(3), 'total' => 0.0];
        $item['Time Tracking'] = $time_tracking;
        $item['Time Tracking'] = $this->updateTrackingTime($item['Time Tracking']);
        $item['Note'] = $this->faker->paragraph();
        $item['subitems'] = $this->fakeSubitems(2);
        $item['Status'] = rand(0, 4);
        $item['Priority'] = rand(0, 4);
        $item['Due Date'] = (int)((float)(new DateTime($this->faker->date()))->format('Uu')/1000);
        $item['LastUpdated'] = (int)((float)now()->format('Uu')/1000);
        $item['formats'] = [
            ['name' => 'Name', 'type' => 'text', 'width' => 15],
            ['name' => 'Person', 'type' => 'person', 'width' => 10],
            ['name' => 'Note', 'type' => 'longtext', 'width' => 30],
            ['name' => 'URL', 'type' => 'url', 'width' => 20],
            ['name' => 'Status', 'type' => 'status', 'width' => 10],
            ['name' => 'LastUpdated', 'type' => 'datetime', 'width' => 15],
        ];
        return $item;
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
//        $date = new UTCDateTime;
        $record['id'] = 0;
        $record['Start time'] = (int)((float)now()->format('Uu')/1000);
        $add_seconds = rand(30, 86400);
        $add_time_string = sprintf('%d seconds ', $add_seconds);
        $date_new = now();
        $date_new->modify($add_time_string);
        $record['End time'] = (int)((float)now()->format('Uu')/1000);
        $record['Remark'] = '';
        $record['Work hour'] = $add_seconds;
        return $record;
    }

    public function updateTrackingTime($time_tracking)
    {
        $records = $time_tracking['records'];
        $seconds = 0.0;
        foreach ($records as $record) {
            $seconds += $record['Work hour'];
        }
        $time_tracking['total'] = round($seconds, 2);
        return $time_tracking;
    }

    public function fakeSubitems($numSubitems): array
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
//        $date = new UTCDateTime;
        $subItem = json_decode('');
        $subItem['id'] = 0;
        $subItem['created_at'] = (int)((float)now()->format('Uu')/1000);
        $subItem['Name'] = $this->faker->word();
        $subItem['Person'] = ['0'];
        $subItem['Note'] = $this->faker->paragraph();
        $subItem['URL'] = $this->faker->url();
        $subItem['Status'] = rand(0, 4);
        $subItem['LastUpdated'] = (int)((float)now()->format('Uu')/1000);
        return $subItem;
    }

    public function definition()
    {
        $user_id = User::factory();
        return [
            'user_id' => $user_id,
            'workspace' => $this->faker->word(),
            'groups' => [$this->createGroup()],
            'users' => [
                [
                    'firstName' => $this->faker->firstName(),
                    'lastName' => $this->faker->lastName(),
                    'color' => $this->faker->hexColor(),
                ],
                [
                    'firstName' => $this->faker->firstName(),
                    'lastName' => $this->faker->lastName(),
                    'color' => $this->faker->hexColor(),
                ]
            ],
        ];
    }
}
