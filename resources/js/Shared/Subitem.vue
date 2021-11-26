<template>
    <tr class="flex items-center mt-4">
        <td v-for="format in item.formats" :style="formatCell(format)">
            <div :class="setPrefixClass(format)" :style="setPrefixStyle(format)">
                <button v-if="format.name === 'Name'"
                        class="flex-shrink-0"
                        style="height: 50px; width: 20px;"
                        :style="{'background-color': group.color}"
                ></button>

                <div>
                    <component
                        :is="setCellType(format)"
                        v-html="setCellValue(format)"
                        :ref="getRef(format)"
                        v-on:mouseover="itemAction(format, ['mouseover', ''], $event)"
                        v-on:mouseleave="itemAction(format, ['mouseleave', ''], $event)"
                        @click="() => itemAction(format,['click', 'buttonTrigger'])"
                        @ondblclick="() => itemAction(format,['dblclick', 'buttonTrigger'])"
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
                            <div v-for="(choice, index) in popupContent.choices">
                                <button :style="choice.format"
                                        @click="chooseStateMenu(popupContent.targetField, index, 'buttonTrigger')">
                                    {{ choice.name }}
                                </button>
                            </div>

                        </div>
                    </Popup>

                    <Hint
                        v-if="hintTriggers.mouseTrigger"
                        :ToggleHint="() => ToggleHint('mouseTrigger')"
                        :style="hintObj.format">
                        <div>
                            <div>
                                {{ hintObj.content }}
                            </div>
                        </div>
                    </Hint>
                </div>
            </div>
        </td>
    </tr>
</template>

<script>
/**
 *
 * @typedef {Array} group.statuses
 * @typedef {Array} group.priorities
 * @typedef {Date} str.$date
 * @typedef {Number} date.$numberLong
 *
 */

import {ref} from "vue";
import Popup from "./Popup";
import Hint from "./Hint";

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

function parseStringDate(str) {
    let $d = new Date(parseInt(str));
    return $d.toLocaleDateString('en-GB');
}

function parseStringDateTime(str) {
    let $d = new Date(parseInt(str));
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
            choices: Array,
            format: '',
            targetField: '',
        });

        const hintTriggers = ref({
            mouseTrigger: false,
        });

        const hintContent = ref('Hello world!');

        const hintFormat = ref();

        const clickTimer = ref({
            result: [],
            delay: 3000,
            clicks: 0,
            timer: null
        });

        const textAreaZoomed = ref(false);

        const hintObj = ref({
            show: false,
            x: 0,
            y: 0,
            content: '',
            format: {},
        })

        const TogglePopup = (trigger) => {
            popupTriggers.value[trigger] = !popupTriggers.value[trigger]
        };

        const ToggleHint = (trigger) => {
            hintTriggers.value[trigger] = !hintTriggers.value[trigger]
        };

        return {
            Popup,
            popupTriggers,
            TogglePopup,
            popupContent,
            hintTriggers,
            ToggleHint,
            hintContent,
            hintFormat,
            clickTimer,
            textAreaZoomed,
            hintObj,
        }
    },
    components: {Popup, Hint},


    props: {
        subitem: Object,
        item: Object,
        group: Object,
    },

    methods: {
        chooseStateMenu(targetField, index, trigger) {
            let itemField = targetField === 'Status' ? 'statuses' : 'priorities';
            let ref = targetField.toLowerCase() + this.subitem.id;
            let obj = this.$refs[ref];
            obj.innerHTML = this.group[itemField][index].name;
            this.subitem[targetField] = index;
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
            }
        },

        setPrefixClass(format) {
            switch (format.name) {
                case 'Name': {
                    return 'flex justify-between space-x-4'
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
                        'background-color': this.group.statuses[parseInt(this.subitem[$format.name])]['color'],
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
                case 'a':
                case 'url':
                    return {
                        'width': toPercent($format['width']),
                        'text-align': 'left',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        'color': 'blue',
                        'margin-right': '20px',

                        // 'white-space': 'normal',
                        // 'overflow-wrap': 'normal',

                    }
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
                case 'url':
                    return {
                        'margin-left': '10px',
                        'margin-right': '10px',
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
                    return 'a';
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
                    return this.subitem[$format.name];
                case 'person':
                    return this.subitem[$format.name];
                case 'url':
                    return this.subitem[$format.name];
                case 'status':
                    return this.group.statuses[parseInt(this.item[$format.name])]['name'];
                case 'priority':
                    return this.group.priorities[parseInt(this.item[$format.name])]['name'];
                case 'longtext':
                    return this.subitem[$format.name];
                case 'datetime':
                    return parseStringDateTime(this.subitem[$format.name].$date.$numberLong);
                case 'date':
                    return parseStringDate(this.subitem[$format.name].$date.$numberLong);
            }
        },

        stateAction(format, options) {
            let trigger = options[1]
            console.log('stateAction', format, trigger);
            const default_format = 'height: 20px; width: 100px; color: white;'
            switch (format.name) {
                case 'Status': {
                    this.popupContent.name = 'Status';
                    let choices = this.group['statuses'];
                    choices.forEach(e => e.format = default_format + 'background-color:' + e.color);
                    this.popupContent.choices = choices;
                    this.popupContent.targetField = 'Status';
                    break;
                }
                case 'Priority':
                    this.popupContent.name = 'Priority'
                    let choices = this.group['priorities'];
                    choices.forEach(e => e.format = default_format + 'background-color:' + e.color);
                    this.popupContent.choices = choices;
                    this.popupContent.targetField = 'Priority';
                    break;
                default: {
                    console.log('stateAction: unmatched choice')
                }
            }
            this.TogglePopup(trigger);
        },

        itemAction(format, options, event) {
            let key = format.name + '.' + options[0];
            // console.log(key, event)

            switch (key) {
                case 'Note.mouseleave':
                case 'Note.mouseover': {
                    this.textAreaHintAction(format, options, event)
                    break;
                }

                case 'Name.focus':
                case 'Note.focus' : {
                    this.textAreaZoomAction(format, options);
                    break;
                }

                case 'Status.click': {
                    this.stateAction(format, options)
                    break;
                }

                case 'URL.click' : {
                    console.log('URL clicked');
                    this.urlAction(format, options);
                    break;
                }

                case 'URL.mouseleave':
                case 'URL.mouseover' : {
                    // console.log('wait URL mouse over action')
                    this.urlAction(format, options);
                    break;
                }

                default : {
                }
            }
        },

        urlAction(format, options) {
            let key = format.name + '.' + options[0];
            switch (key) {
                case 'URL.mouseover' : {
                    let obj = this.$refs[this.setRef(format)];
                    obj.style.textDecoration = 'underline';
                    obj.target = '_blank';
                    obj.href = obj.innerHTML;
                    break;
                }
                case 'URL.mouseleave' : {
                    let obj = this.$refs[this.setRef(format)];
                    obj.style.textDecoration = 'none';
                    break;
                }
            }
        },

        textAreaHintAction(format, options, event) {
            // console.log('textAreaHintAction')
            let key = format.name + '.' + options[0];
            // console.log(key);
            switch (key) {
                case 'Note.mouseover': {
                    let obj = this.$refs[this.setRef(format)]
                    if (this.textAreaZoomed) {
                        break;
                    }
                    // console.log('hint show')
                    this.hintObj.x = event.clientX;
                    this.hintObj.y = event.clientY;
                    this.hintTriggers['mouseTrigger'] = true;
                    this.hintObj.show = true;
                    this.hintObj.content = obj.innerHTML;
                    this.hintObj.format = {
                        'top': (this.hintObj.y - 40) + 'px',
                        'left': (this.hintObj.x + 40) + 'px',
                    }
                    break;
                }
                case 'Note.mouseleave': {
                    // console.log('hint close')
                    this.hintTriggers['mouseTrigger'] = false;
                    this.hintObj.show = false;
                    break;
                }
            }

        },

        textAreaZoomAction(format, options) {
            console.log('textAreaZoomAction', format, options);
            let $ref = this.setRef(format)
            console.log(format, options)
            let $obj = this.$refs[$ref]
            const $rect = $obj.getBoundingClientRect();
            if (!$obj.hasOwnProperty('rect_initial')) {
                $obj.rect_initial = $rect;
                $obj.rect_initial.rows = $obj.rows;
                $obj.rect_initial.cols = $obj.cols;
            }
            if (options[1] === 'in') {
                $obj.style.position = 'absolute';
                if ($ref.toLowerCase().includes('longtext')) {
                    $obj.rows = 10;
                    $obj.cols = 40;
                } else {
                    $obj.rows = 2;
                    $obj.cols = 20;
                }
                $obj.style.zIndex = '1';
                this.textAreaZoomed = true;
                //close hint when zoomed
                this.textAreaHintAction(format, ['mouseleave']);
            } else {
                $obj.style.position = 'relative';
                $obj.rows = $obj.rect_initial.rows;
                $obj.cols = $obj.rect_initial.cols
                $obj.style.zIndex = '0';
                this.textAreaZoomed = false;
            }
        },

        getRef(format, option = null) {
            // console.log('getRef')
            // console.log(format, option)
            // console.log(ref)
            return this.setRef(format, option);
        },

        setRef($format) {
            if ($format['type'] === 'text') {
                return 'editText' + this.subitem.id
            }
            if ($format['type'] === 'longtext') {
                return 'editLongText' + this.subitem.id
            }
            if ($format['type'] === 'url') {
                return 'url' + this.subitem.id
            }
            if ($format['name'] === 'Status') {
                return 'status' + this.subitem.id
            }
            if ($format['name'] === 'Priority') {
                return 'priority' + this.subitem.id
            }

        },
    }
}
</script>

<style scoped>

</style>
