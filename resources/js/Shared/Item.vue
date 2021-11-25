<template>
    <tr class="flex items-center mt-4">
        <td v-for="format in group.formats" :style="formatCell(format)">
            <div :class="setPrefixClass(format)" :style="setPrefixStyle(format)">
                <button v-if="format.name === 'Name'"
                        class="flex-shrink-0"
                        style="height: 50px; width: 20px;"
                        :style="{'background-color': group.color}"
                ></button>
                <div v-if="format.name === 'subitems'" class="flex space-x-2">
                    <i class="fas fa-1x fa-chevron-right"
                       :class=" subItemOpen ? 'transform rotate-90 ':''"></i>
                    <i class="fas fa-list"></i>
                </div>

                <div>
                    <component
                        :is="setCellType(format)"
                        :href="setCellValue(format)"
                        v-html="setCellValue(format)"
                        :ref="setTextAreaRef(format)"
                        @click="itemAction(format,['click', 'buttonTrigger'])"
                        @focus="itemAction(format, ['focus','in'])"
                        @blur="itemAction(format, ['focus','out'])"
                        :style="formatContent(format)"
                        class="px-1"
                    />

                    <Popup
                        v-if="popupTriggers.buttonTrigger"
                        :TogglePopup="() => TogglePopup('buttonTrigger')">
                        <h2>{{ popupContent.name }}</h2>
                        <div>
                            <div v-for="choice in popupContent.choices">
                                <button :style="choice.format"
                                        @click="chooseMenu(choice, 'buttonTrigger')">
                                    {{ choice.name }}
                                </button>
                            </div>

                        </div>
                    </Popup>
                </div>
            </div>
        </td>
    </tr>

    <div v-if='subItemOpen' class="mt-5 ml-5 mb-10 ">
        <table class="divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr class="flex items-center">
                <th v-for="format in item.formats" scope="col" :style="formatTitle(format)">
                    <div
                        class="px-6 py-3 text-center tracking-wider"
                    >
                        {{ format.name === 'Name' ? 'Subitems' : format.name }}
                    </div>
                </th>

            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <Subitem v-for="subitem in item.subitems"
                     v-bind:subitem="subitem"
                     v-bind:item="item"
                     v-bind:group="group"
            />
            </tbody>
        </table>
    </div>
</template>

<script>
import Subitem from "./Subitem";
import {ref} from 'vue';
import Popup from './Popup.vue'


function toPercent($number, $float = 0) {
    return parseFloat($number).toFixed($float) + "%";
}

function randomText() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

function zeroPad(num, places) {
    let zero = places - num.toString().length + 1;
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
    setup() {
        const popupTriggers = ref({
            buttonTrigger: false,
            timedTrigger: false
        });

        const popupContent = ref({
            name: '',
            choices: [],
            format: '',
        });

        const subItemOpen = ref(false);

        const subItemContent = ref({
            groupColor: '',
            data:[],
            formats:[],
        });

        const TogglePopup = (trigger) => {
            popupTriggers.value[trigger] = !popupTriggers.value[trigger]
        };
        return {
            Popup,
            popupTriggers,
            TogglePopup,
            popupContent,
            subItemOpen,
            subItemContent,
        }
    },

    components: {Subitem, Popup},

    props: {
        item: Object,
        group: Object,
    },

    methods: {
        chooseMenu(item, trigger) {
            console.log(item);
            this.TogglePopup(trigger);
        },

        setWidth($format) {
            return {
                'width': toPercent($format['width']),
                'vertical-align': 'top',
                'text-align': 'left',
            }
        },

        setPrefixStyle(format) {
            switch (format.name) {
                case 'Name': {
                    return {
                        'align-items': 'center'
                    }
                }
                case 'subitems': {
                    return {
                        'align-items': 'center'
                    }
                }
            }
        },

        setPrefixClass(format) {
            switch (format.name) {
                case 'Name': {
                    return 'flex justify-between space-x-4'
                }
                case 'subitems': {
                    return 'flex space-x-2'
                }
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
                        'margin-left': '10px',
                        'margin-right': '10px',
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
                    return 'button';
                case 'priority':
                    return 'button';
                case 'subitem':
                    return 'button';
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

        setPopupContent(format, trigger) {
            console.log('setPopupContent', format, trigger);
            const default_format = 'height: 20px; width: 100px; color: white;'
            switch (format.name) {
                case 'Status': {
                    this.popupContent.name = 'Status';
                    let choices = this.group['statuses'];
                    choices.forEach(e => e.format = default_format + 'background-color:' + e.color);
                    this.popupContent.choices = choices;
                    break;
                }
                case 'Priority':
                    this.popupContent.name = 'Priority'
                    let choices = this.group['priorities'];
                    choices.forEach(e => e.format = default_format + 'background-color:' + e.color);
                    this.popupContent.choices = choices;
                    break;
                default: {
                    console.log('setPopupContent: unmatched choice')
                }
            }
            this.TogglePopup(trigger);
        },

        itemAction(format, option) {
            let key = format.name + '.' + option[0];
            console.log(key)
            switch (key) {
                case 'Name.focus':
                case 'Note.focus' : {
                    this.zoomTextArea(format, option[1]);
                    break;
                }

                case 'Priority.click':
                case 'Status.click': {
                    this.setPopupContent(format, option[1])
                    break;
                }

                case 'subitems.click': {
                    this.openSubitems(format, option[1]);
                    break;
                }

                default : {
                }

            }
        },

        openSubitems(format, option) {
            console.log('format', format, option)
            this.subItemOpen = !this.subItemOpen;
            this.subItemContent.groupColor = this.group.color;
            this.subItemContent.data = this.item.subitem
        },

        formatTitle($format) {
            if ($format.name === 'Name') {
                return {
                    'color': 'black',
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

        zoomTextArea(format, $option) {
            console.log('ZoomTextArea', format, $option);
            let $ref = this.setTextAreaRef(format)
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
            if ($format['type'] === 'subitem') {
                return 'subitem' + this.item.id
            }
        },
    }
}
</script>

<style scoped>

</style>
