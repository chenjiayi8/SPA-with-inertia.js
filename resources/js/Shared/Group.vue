<template>
    <div class="flex flex-col relative">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200"
                           :class="group.id ?'mt-40':''">
                        <thead class="bg-gray-50">
                        <tr class="flex items-center">
                            <th v-for="format in group.formats" scope="col" :style="formatTitle(format)">
                                <div
                                    class="px-6 py-3 text-center tracking-wider"
                                >
                                    {{ setTitle(format) }}
                                </div>
                            </th>

                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <Item v-for="item in group.items"
                              v-bind:item="item"
                              v-bind:group="group"
                        />
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
        formatTitle($format) {
            if ($format.name === 'Name') {
                return {
                    'color': this.group.color,
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
        setTitle($format) {
            if ($format.name === 'Name') {
                return ucwords(this.group.name);

            } else if ($format.name === 'subitems')
            {
                return 'Subitems';
            }
            else {
                return $format.name;
            }
        }

    }
}

</script>

<style scoped>

</style>
