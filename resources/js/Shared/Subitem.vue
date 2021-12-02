<template>
    <tr class="flex items-center mt-4 space-x-2">
        <td v-for="format in item.formats" :style="formatCell(format)">
            <div :class="setPrefixClass(format)" :style="setPrefixStyle(format)">
                <button v-if="format.name === 'Name'"
                        class="flex-shrink-0"
                        style="height: 50px; width: 20px;"
                        :style="{'background-color': group.color}"
                ></button>

                <div v-if="format.name === 'Person'" class="namesHolder flex items-center"
                     :ref="'namesHolder'+this.subitem.id"
                     v-on:mouseover="personAction('mouseover', $event)"
                     v-on:mouseleave="personAction('mouseleave', $event)"
                     v-on:change="itemAction(format, ['change', ''], $event)"
                     :style="{'width': ((1+getNames().length)*30)+'px'}">
                    <button v-show="this.personObj.buttonShown"
                            :ref="'personsEdit'+this.subitem.id" class="circledButton"
                            style="position: absolute; left: 0; background-color: blue"
                            v-on:click="personAction('click', $event)"

                    >+
                    </button>
                    <div v-for="name in getNames()" class="circle" :style="name.format">
                        {{ name.value }}
                    </div>
                </div>

                <button v-show="format.name === 'URL' && urlObj.buttonShown"
                        class="text-gray-500 px-2 align-middle"
                        style="height: 20px; width: 50px;"
                        :ref="'urlEditBtn'+this.subitem.id"
                        v-on:click="urlEditAction('urlEdit')"
                >Edit
                </button>


                <div>
                    <component
                        :is="setCellType(format)"
                        v-html="setCellValue(format)"
                        :ref="getRef(format)"
                        v-on:mouseover="itemAction(format, ['mouseover', ''], $event)"
                        v-on:mouseleave="itemAction(format, ['mouseleave', ''], $event)"
                        v-on:change="itemAction(format, ['change', ''], $event)"
                        @click="itemAction(format,['click', 'buttonTrigger'])"
                        @focus="itemAction(format, ['focus','in'])"
                        @blur="itemAction(format, ['focus','out'])"
                        :style="formatContent(format)"
                        class="px-1"
                    />

                    <!--   menu-->
                    <Popup
                        v-if="popupMenuTriggers.buttonTrigger"
                        :TogglePopupMenu="() => TogglePopupMenu('buttonTrigger')">
                        <h2>{{ popupMenuContent.name }}</h2>
                        <div>
                            <div v-for="(choice, index) in popupMenuContent.choices">
                                <button :style="choice.format"
                                        @click="chooseStateMenu(popupMenuContent.targetField, index, 'buttonTrigger')">
                                    {{ choice.name }}
                                </button>
                            </div>

                        </div>
                    </Popup>

                    <!--   inputs -->
                    <Hint
                        v-if="popupInputObj.show"
                        :ToggleHint="() => popupInputObj.show = !popupInputObj.show"
                        class="flex items-center"
                        :style="popupInputObj.format">
                        <div class="bg-white rounded-xl px-2 py-2">
                            <div>
                                <div class="flex content-center">
                                <textarea class="text-gray-500 m-2 p-2"
                                          :ref="'urlEditArea'+this.subitem.id"
                                          :value="popupInputObj.value"
                                          style="width : 400px">
                                </textarea>
                                </div>
                                <div v-if="popupInputObj.error.length !==0" class="text-center text-red-300">
                                    {{ popupInputObj.error }}
                                </div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="getPopupInput('confirm')"
                                >
                                    Confirm
                                </button>
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="getPopupInput('remove')"
                                >
                                    Remove
                                </button>
                                <button
                                    class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                    @click="getPopupInput('cancel')"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </Hint>


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

    <!--    last item: add row -->
    <tr v-if="subitem.id === this.item.subitems.length-1" class="flex items-center mt-4">
        <td style="height: 100%; width: 100%">
            <div class="flex align-left">
                <button
                    class="flex-shrink-0"
                    style="height: 50px; width: 20px;"
                    :style="{'background-color': getAddColor(group.color)}"
                />
                <input
                    placeholder="+ Add"
                    :ref="'addSubItem'+item.id"
                    v-on:change="addRowAction(['add'], $event)"
                    style="line-height: 100%; width: 100%"
                    class="ml-4"
                />
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

function hexToRGB(hex) {
    let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function isValidUrl(url) {
    try {
        new URL(url);
    } catch (e) {
        console.error(e);
        return false;
    }
    return true;
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
        const popupMenuTriggers = ref({
            buttonTrigger: false,
            timedTrigger: false
        });

        const popupMenuContent = ref({
            name: '',
            choices: Array,
            format: '',
            targetField: '',
        });

        const TogglePopupMenu = (trigger) => {
            popupMenuTriggers.value[trigger] = !popupMenuTriggers.value[trigger]
        };

        const popupInputObj = ref({
            show: false,
            name: '',
            format: '',
            targetField: '',
            error: '',
            value: '',
        });

        const personObj = ref({
            buttonShown: false,
        });

        const urlObj = ref({
            buttonShown: true,
            content: '',
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


        const ToggleHint = (trigger) => {
            hintTriggers.value[trigger] = !hintTriggers.value[trigger]
        };

        const defaultStateFormat = 'height: 20px; width: 100px; color: white; '

        return {
            Popup,
            popupMenuTriggers,
            popupMenuContent,
            TogglePopupMenu,
            popupInputObj,
            personObj,
            urlObj,
            hintTriggers,
            ToggleHint,
            hintContent,
            hintFormat,
            clickTimer,
            textAreaZoomed,
            hintObj,
            defaultStateFormat,
        }
    },
    components: {Popup, Hint},


    props: {
        subitem: Object,
        item: Object,
        group: Object,
        workspace: Object,
    },

    methods: {
        getAddColor() {
            let rgb = hexToRGB(this.group.color);
            return "rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + ",0.5)";
        },

        addRowAction(options, event) {
            console.log(options, event);
            if (options[0] === 'add') {
                let obj = this.$refs['addSubItem' + this.item.id]
                let subItem = JSON.parse(JSON.stringify(this.item.subitems[0]));
                console.log("value", obj.value)
                subItem['Name'] = obj.value;
                subItem['id'] = this.item.subitems.length;
                subItem['Note'] = '';
                subItem['URL'] = '';
                subItem['Status'] = null;
                subItem['created_at'] = new Date().getTime();
                subItem['LastUpdated'] = new Date().getTime();
                console.log('add new subItem', subItem);
                this.item.subitems.push(subItem)
                obj.value = ''
            }
        },

        setLastUpdateTime() {
            this.subitem['LastUpdated'] = new Date().getTime();
        },

        chooseStateMenu(targetField, index, trigger) {
            let itemField = targetField === 'Status' ? 'statuses' : 'priorities';
            let ref = targetField.toLowerCase() + this.subitem.id;
            let obj = this.$refs[ref];
            if (index < this.group[itemField].length) {
                this.subitem[targetField] = index;
            } else {
                this.subitem[targetField] = null;
            }
            this.TogglePopupMenu(trigger);
            this.setLastUpdateTime()
        },

        getPopupInput(action) {
            switch (action) {
                case 'confirm':
                    let obj = this.$refs['urlEditArea' + this.subitem.id]
                    if (isValidUrl(obj.value)) {
                        this.subitem[this.popupInputObj.targetField] = obj.value;
                        this.popupInputObj.show = false;
                        this.setLastUpdateTime();
                    } else {
                        this.popupInputObj.error = 'Invalided URL';
                    }
                    break;
                case 'cancel':
                    this.popupInputObj.show = false;
                    break;
                case 'remove':
                    this.subitem[this.popupInputObj.targetField] = null;
                    this.popupInputObj.show = false;
                    break;
            }
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
                case 'Person': {
                    return {
                        'display': 'flex',
                        'text-align': 'left',
                        'justify-content': 'center',
                    }
                }
            }
        },

        setPrefixClass(format) {
            switch (format.name) {
                case 'Name': {
                    return 'flex justify-between space-x-4'
                }
                case 'Person': {
                    return 'flex'
                }

                case 'URL': {
                    return 'flex justify-start items-center mx-2 truncate'

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
                        'width': '100%',
                        'max-width': '400px',
                        'max-height': '100px',
                        'vertical-align': 'top',
                        'justify-content': 'left',
                        'flex-direction': 'column',
                        'white-space': 'pre-wrap',
                        // 'width': 'max-content',
                    }
                case 'url':
                    return {
                        'text-align': 'left',
                        // 'overflow': 'hidden',
                        // 'width': '100%',
                    }
                case 'person':
                    return {
                        'vertical-align': 'top',
                        'text-align': 'center',
                    }
                case 'status':
                    return {
                        'vertical-align': 'center',
                        'height': '100%',
                        'width': '100%',
                        'text-align': 'center',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        'color': 'white',
                        'font-size': 'small',
                        'background-color': this.subitem[$format.name] !== null ? this.group.statuses[parseInt(this.subitem[$format.name])]['color'] : 'rgb(197, 197, 197)',
                    }
                case 'priority':
                    return {
                        'vertical-align': 'center',
                        'height': '100%',
                        'width': '100%',
                        'text-align': 'center',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        'color': 'white',
                        'font-size': 'small',
                        'background-color': this.subitem[$format.name] !== null ? this.group.priorities[parseInt(this.subitem[$format.name])]['color'] : 'rgb(197, 197, 197)',
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
                    return 'div';
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
            if (this.subitem[$format.name] === null) return '&ZeroWidthSpace;';
            switch ($format['type']) {
                case 'text':
                    return this.subitem[$format.name];
                case 'person':
                    return '';
                case 'url':
                    return this.subitem[$format.name];
                case 'status':
                    return this.group.statuses[parseInt(this.subitem[$format.name])]['name'];
                case 'priority':
                    return this.group.priorities[parseInt(this.subitem[$format.name])]['name'];
                case 'longtext':
                    return this.subitem[$format.name];
                case 'datetime':
                    return parseStringDateTime(this.subitem[$format.name]);
                case 'date':
                    return parseStringDate(this.subitem[$format.name]);
            }
        },

        getNames() {
            let nameIds = this.subitem['Person']
            let names = []
            for (let i = 0; i < nameIds.length; i++) {
                let name = Object()
                name.value = this.workspace.users[nameIds[i]]['firstName'][0]
                name.value += this.workspace.users[nameIds[i]]['lastName'][0]
                name.format = {
                    'position': 'absolute',
                    'background': this.workspace.users[nameIds[i]]['color'],
                    'left': (20 * (1 + i) + (i === 0 ? 2 : 0)) + 'px',
                    'zIndex': i,
                }
                names.push(name)
            }
            return names
        },

        urlEditAction() {
            this.popupInputObj.name = 'URL';
            this.popupInputObj.value = this.subitem['URL'];
            this.popupInputObj.show = true;
            this.popupInputObj.targetField = 'URL';
            this.popupInputObj.format = 'background-color:rgba(0, 0, 0, 0.1)';
            this.popupInputObj.error = '';
        },

        personAction(action) {
            let ref = ''
            switch (action) {
                case 'mouseover':
                    this.personObj.buttonShown = true;
                    break;
                case 'mouseleave':
                    this.personObj.buttonShown = false;
                    break;
                case 'click':
                    console.log('personAction clicked');
                    break;
            }
        },

        stateAction(format, options) {
            let trigger = options[1]
            let defaultState = Object()
            defaultState['format'] = this.defaultStateFormat + 'background-color: rgb(197, 197, 197)';
            defaultState['name'] = 'None'
            switch (format.name) {
                case 'Status': {
                    console.log('stateAction123', format.name, options);
                    this.popupMenuContent.name = 'Status';
                    let choices = JSON.parse(JSON.stringify(this.group['statuses']));
                    choices.forEach(e => e.format = this.defaultStateFormat + 'background-color:' + e.color);
                    choices.push(defaultState);
                    this.popupMenuContent.choices = choices;
                    this.popupMenuContent.targetField = 'Status';
                    break;
                }
                case 'Priority':
                    this.popupMenuContent.name = 'Priority'
                    let choices = JSON.parse(JSON.stringify(this.group['priorities']));
                    choices.forEach(e => e.format = this.defaultStateFormat + 'background-color:' + e.color);
                    choices.push(defaultState);
                    this.popupMenuContent.choices = choices;
                    this.popupMenuContent.targetField = 'Priority';
                    break;
                default: {
                    console.log('stateAction: unmatched choice')
                }
            }
            this.TogglePopupMenu(trigger);
        },

        itemAction(format, options, event) {
            let key = format.name + '.' + options[0];
            // console.log(key, event)
            if (!['mouseleave', 'mouseover'].includes(options[0])) {
                console.log(key)
            }
            if (options[0] === 'change') {
                this.setLastUpdateTime();
            }
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
                        'max-width': '400px',
                        'max-height': '100px',
                        'vertical-align': 'top',
                        'justify-content': 'left',
                        'flex-direction': 'column',
                        'white-space': 'pre-wrap',
                        'width': 'max-content',
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
                    // $obj.style.height= '200px';
                    $obj.style.width = '400px';
                } else {
                    $obj.rows = 2;
                    $obj.cols = 20;
                }
                $obj.style.zIndex = '10';
                this.textAreaZoomed = true;
                //close hint when zoomed
                this.textAreaHintAction(format, ['mouseleave']);
            } else {
                this.subitem[format.name] = $obj.value;
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
            if ($format['type'] === 'person') {
                return 'person' + this.subitem.id
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

.circle {
    width: 25px;
    height: 25px;
    border-radius: 12px;
    font-size: 15px;
    color: white;
    line-height: 25px;
    text-align: center;
}

.circledButton {
    width: 20px;
    height: 20px;
    border-radius: 10px;
    font-size: 20px;
    color: white;
    line-height: 20px;
    text-align: center;
}

.namesHolder {
    position: relative;
    text-align: center;
    height: 25px;
    border-radius: 12px;
    vertical-align: center;
}
</style>
