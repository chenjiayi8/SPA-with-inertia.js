<template>
    <tr class="flex items-center mt-4">
        <td v-for="format in group.formats" :style="formatCell(format)">
            <div :class="format.name == 'Name' ?'flex justify-between space-x-4':''">
                <button v-if="format.name == 'Name'"
                     class="flex-shrink-0"
                     style="height: 50px; width: 20px;"
                     :style="{'background-color': group.color}"
                ></button>
                <div>
                    <component
                        :is="setCellType(format)"
                        :href="setCellValue(format)"
                        v-html="setCellValue(format)"
                        :ref="setTextAreaRef(format)"
                        @focus="styleTextArea(setTextAreaRef(format), 'in')"
                        @blur="styleTextArea(setTextAreaRef(format), 'out')"
                        :style="formatContent(format)"
                        class="px-1"
                    />
                </div>
            </div>
            <!--            </div>-->
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
    return $d.toLocaleDateString('en-GB');
}

function parseStringDateTime($s) {
    let $d = new Date(parseInt($s));
    return $d.toLocaleDateString('en-GB') + '\n ' + zeroPad($d.getHours(), 2) + ":" + zeroPad($d.getMinutes(), 2) + ":" + zeroPad($d.getSeconds(), 2);
}

export default {
    components: {Subitem},
    props: {
        item: Object,
        group: Object,
    },
    methods: {
        setWidth($format) {
            return {
                'width': toPercent($format['width']),
                'vertical-align': 'top',
                'text-align': 'left',
            }
        },
        formatCell($format) {
            switch ($format.type) {
                case 'datetime':
                    return {
                        'width': toPercent($format['width']),
                        'vertical-align': 'top',
                        'text-align': 'center',
                        'white-space': 'normal',
                    };
                case 'status':
                    return {
                        'width': toPercent($format['width']),
                        'vertical-align': 'top',
                        'text-align': 'center',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        'color': 'white',
                        'font-size': 'small',
                        'background-color': this.group.statuses[parseInt(this.item[$format.name])]['color'],
                    }
                case 'priority':
                    return {
                        'width': toPercent($format['width']),
                        'vertical-align': 'top',
                        'text-align': 'center',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        'color': 'white',
                        'font-size': 'small',
                        'background-color': this.group.priorities[parseInt(this.item[$format.name])]['color'],
                    }
                case 'longtext':
                    return {
                        'width': toPercent($format['width']),
                        'text-align': 'left',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        // 'white-space': 'normal',
                        // 'overflow-wrap': 'normal',

                    }
                // case 'subitem':
                //     return {
                //         'width': toPercent($format['width']),
                //     }
                default: {
                    return {
                        'width': toPercent($format['width']),
                        'vertical-align': 'top',
                        'text-align': 'center',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                    }
                }

            }


        },
        formatContent($format) {
            switch ($format.type) {
                case 'longtext':
                    return {
                        'zIndex': '0',
                    }
                case 'subitem':
                    return {
                        'vertical-align': 'top',
                        'text-align': 'center',
                    }
                default: {
                    return {}
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
        setCellValue($format) {
            switch ($format['type']) {
                case 'text':
                    return this.item[$format.name];
                case 'person':
                    return this.item[$format.name];
                case 'url':
                    return this.item[$format.name];
                case 'status':
                    return this.group.statuses[parseInt(this.item[$format.name])]['name'];
                case 'priority':
                    return this.group.priorities[parseInt(this.item[$format.name])]['name'];
                case 'subitem':
                    return this.item[$format.name].length;
                case 'longtext':
                    return this.item[$format.name];
                case 'tracking':
                    return this.item[$format.name]['total'];
                case 'datetime':
                    return parseStringDateTime(this.item[$format.name].$date.$numberLong);
                case 'date':
                    return parseStringDate(this.item[$format.name].$date.$numberLong);
            }
        },

        moveBackToInitial($ref) {
            let $obj = this.$refs[$ref]
            console.log('initial:');
            console.log($obj.rect_initial);
            if (!$obj.hasOwnProperty('rect_initial')) {
                $obj.style.x = $obj.rect_initial.x;
                $obj.style.y = $obj.rect_initial.y;
                $obj.style.width = $obj.rect_initial.width;
                $obj.style.height = $obj.rect_initial.height;
                $obj.style.top = $obj.rect_initial.top;
                $obj.style.right = $obj.rect_initial.right;
                $obj.style.bottom = $obj.rect_initial.bottom;
                $obj.style.left = $obj.rect_initial.left;
            }
        },

        styleTextArea($ref, $option) {
            let $obj = this.$refs[$ref]
            const $rect = $obj.getBoundingClientRect();
            // console.log('before:')
            // console.log($obj.getBoundingClientRect());
            if (!$obj.hasOwnProperty('rect_initial')) {
                // console.log($obj.style.zIndex);
                $obj.rect_initial = $rect;
                $obj.rect_initial.rows = $obj.rows;
                $obj.rect_initial.cols = $obj.cols;
                // $obj.style.position = 'absolute';
                // $obj.style.x = $obj.rect_initial.x;
                // $obj.style.y = $obj.rect_initial.y;
                // $obj.style.width = $obj.rect_initial.width;
                // $obj.style.height = $obj.rect_initial.height;
                // $obj.style.top = $obj.rect_initial.top;
                // $obj.style.right = $obj.rect_initial.right;
                // $obj.style.bottom = $obj.rect_initial.bottom;
                // $obj.style.left = $obj.rect_initial.left;

            }
            if ($option === 'in') {
                $obj.style.position = 'absolute';
                if ($ref.toLowerCase().includes('longtext')) {
                    $obj.rows = 10;
                    $obj.cols = 40;
                } else {
                    $obj.rows = 2;
                    $obj.cols = 20;
                }
                $obj.style.zIndex = '1';
                // $obj.style.top = $obj.rect_initial.top - 50;
                // $obj.style.x = $obj.rect_initial.x - 100;
                // $obj.style.y = $obj.rect_initial.y - 100;

            } else {
                $obj.style.position = 'relative';
                $obj.rows = $obj.rect_initial.rows;
                $obj.cols = $obj.rect_initial.cols
                $obj.style.zIndex = '0';
            }
        },

        setTextAreaRef($format) {
            if ($format['type'] === 'text') {
                return 'editText' + this.item.id
            }
            if ($format['type'] === 'longtext') {
                return 'editLongText' + this.item.id
            }
        },
    }
}
</script>

<style scoped>

</style>
