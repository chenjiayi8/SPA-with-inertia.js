<template>
    <tr class="flex items-center mt-4">
        <td v-for="format in formats" :style="formatCell(format)">
            <component
                :is="setCellType(format)"
                :href="setCellValue(item, statuses, priorities, format)"
                v-html="setCellValue(item, statuses, priorities, format)"
                class="px-1"
            />
        </td>
    </tr>
</template>

<script>
import Subitem from "./Subitem";

function toPercent($number, $float = 0) {
    return parseFloat($number).toFixed($float) + "%";
}

function randomText() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

function zeroPad(num, places) {
    var zero = places - num.toString().length + 1;
    return Array(+(zero > 0 && zero)).join("0") + num;
}

function parseStringDate($s) {
    let $d = new Date(parseInt($s));
    return $d.toLocaleDateString('en-GB') + '\n ' + zeroPad($d.getHours(), 2) + ":" + zeroPad($d.getMinutes(), 2) + ":" + zeroPad($d.getSeconds(), 2);
}

export default {
    components: {Subitem},
    props: {
        formats: Object,
        item: Object,
        statuses: Array,
        priorities: Array,
    },
    methods: {
        setWidth($format) {
            return {
                'width': toPercent($format['width']),
                'vertical-align': 'top',
                'text-align': 'left',
                'border-width': '1px',
            }
        },
        formatCell($format) {
            if ($format.type === 'datetime') {
                return {
                    'width': toPercent($format['width']),
                    'vertical-align': 'top',
                    'text-align': 'left',
                    'white-space': 'normal',
                    'overflow-wrap': 'anywhere',
                }
            } else {
                return {
                    'width': toPercent($format['width']),
                    'vertical-align': 'top',
                    'text-align': 'left',
                    'white-space': 'nowrap',
                    'overflow': 'hidden',
                }
            }

        },
        setCellType($format) {
            switch ($format['type']) {
                case 'text':
                    return 'textarea';
                case 'person':
                    return 'span';
                case 'url':
                    return 'url';
                case 'status':
                    return 'span';
                case 'priority':
                    return 'span';
                case 'subitem':
                    return 'span';
                case 'longtext':
                    return 'textarea';
                case 'tracking':
                    return 'span';
                case 'datetime':
                    return 'span';
                case 'date':
                    return 'span';
            }
        },
        setCellValue($item, $statuses, $priorities, $format) {
            switch ($format['type']) {
                case 'text':
                    return $item[$format.name];
                case 'person':
                    return $item[$format.name];
                case 'url':
                    return $item[$format.name];
                case 'status':
                    return $statuses[parseInt($item[$format.name])]['name'];
                case 'priority':
                    // return $item[$format.name];
                    return $priorities[parseInt($item[$format.name])]['name'];
                case 'subitem':
                    return $item[$format.name].length;
                case 'longtext':
                    return $item[$format.name];
                case 'tracking':
                    return $item[$format.name]['total'];
                case 'datetime':
                    return parseStringDate($item[$format.name].$date.$numberLong);
                case 'date':
                    return $item[$format.name];
            }
        }

    }
}
</script>

<style scoped>

</style>
