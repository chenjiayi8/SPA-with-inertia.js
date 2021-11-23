<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200"
                           :class="group.id ?'mt-20':''">
                        <thead class="bg-gray-50">
                        <tr class="flex items-center">
                            <th v-for="format in group.items_format" scope="col" :style="formatTitle(format, group)">
                                <div
                                    class="px-6 py-3 text-left tracking-wider"
                                >
                                    {{ setTitle(format, group) }}
                                </div>
                            </th>

                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <Item v-for="item in group.items"
                              v-bind:item="item"
                              v-bind:formats="group.items_format"
                              v-bind:statuses="group.statuses"
                              v-bind:priorities="group.priorities"/>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Item from "./Item";

function toPercent($number, $float = 0) {
    return parseFloat($number).toFixed($float) + "%";
}

function ucwords($str) {
    return ($str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

export default {
    components: {Item},
    props: {
        group: Object,
    },

    methods: {
        formatTitle($format, $group) {
            if ($format.name === 'Name') {
                console.log($group.color);
                return {
                    'color': $group.color,
                    'width': toPercent($format['width']),
                    'font-size': 'large',
                }
            } else {
                return {
                    'width': toPercent($format['width']),
                    'font-size': 'medium',
                    'color': 'gray',
                }
            }
        },
        setTitle($format, $group) {
            if ($format.name === 'Name') {
                return ucwords($group.name);
            } else {
                return $format.name;
            }
        }

    }
}

</script>

<style scoped>

</style>
