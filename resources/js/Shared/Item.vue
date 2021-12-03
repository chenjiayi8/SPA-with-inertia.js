<template>
    <tr class="flex items-center mt-4 space-x-2">
        <td v-for="format in group.formats" :style="formatCell(format)">
            <div :class="setPrefixClass(format)" :style="setPrefixStyle(format)">
                <button v-if="format.name === 'Name'"
                        class="flex-shrink-0"
                        style="height: 50px; width: 20px;"
                        :style="{'background-color': group.color}"
                ></button>
                <div v-if="format.type === 'tracking'" class="flex space-x-2">
                    <button class="fa-2x" :ref="'trackingPlayBtn'+item.id"
                            :class="trackingObj.isTimeTracking ? 'far fa-stop-circle text-red-500':'fas fa-play-circle text-blue-500'"
                            @click="trackPlayAction('trackingPlayBtn'+item.id)"></button>
                </div>
                <div v-if="format.name === 'subitems'" class="flex space-x-2">
                    <i class="fas fa-1x fa-chevron-right"
                       :class="subItemOpen ? 'transform rotate-90 ':''"></i>
                    <i class="fas fa-list"></i>
                </div>
                <div v-if="format.name === 'Due Date' && dueObj.initialised" class="flex space-x-2"
                     :ref="'dueDate'+this.item.id"
                     v-on:mouseover="itemAction(format, ['mouseover', ''], $event)"
                     v-on:mouseleave="itemAction(format, ['mouseleave', ''], $event)">
                    <i class="fa-1x "
                       :class="dueObj.overdue ? 'fas fa-exclamation-circle text-red-500':'far fa-circle text-gray-400'"></i>
                </div>
                <button v-if="format.name === 'Due Date' && !dueObj.initialised" class="text-gray-300"
                        :ref="'dueDateNew'+this.item.id"
                        style="height: 100%; width: 100%"
                        @click="itemAction(format,['click', 'dueDateNew', 'buttonTrigger'], $event)">
                    Add
                </button>
                <button v-if="format.name === 'URL'" class="text-gray-300"
                        :ref="'urlEdit'+this.item.id"
                        style="height: 100%; width: 100%"
                        @click="itemAction(format,['click', 'urlEdit', 'buttonTrigger'], $event)">
                    Add
                </button>
                <div v-if="format.name === 'Person'" class="namesHolder flex items-center"
                     :ref="'namesHolder'+this.item.id"
                     v-on:mouseover="personAction('mouseover', $event)"
                     v-on:mouseleave="personAction('mouseleave', $event)"
                     v-on:change="itemAction(format, ['change', ''], $event)"
                     :style="{'width': ((1+getNames().length)*30)+'px'}">
                    <button v-show="this.personObj.buttonShown"
                            :ref="'personsEdit'+this.item.id" class="circledButton"
                            style="position: absolute; left: 0; background-color: blue"
                            v-on:click="personAction('click', $event)"

                    >+
                    </button>
                    <div v-for="name in getNames()" class="circle" :style="name.format">
                        {{ name.value }}
                    </div>
                </div>

                <div>
                    <component
                        :is="setCellType(format)"
                        v-html="setCellValue(format)"
                        :ref="getRef(format)"
                        v-on:mouseover="itemAction(format, ['mouseover', ''], $event)"
                        v-on:mouseleave="itemAction(format, ['mouseleave', ''], $event)"
                        v-on:change="itemAction(format, ['change', ''], $event)"
                        @click="itemAction(format,['click', 'buttonTrigger'], $event)"
                        @focus="itemAction(format, ['focus','in'])"
                        @blur="itemAction(format, ['focus','out'])"
                        :style="formatContent(format)"
                        class="px-1"
                    />

                    <!--popup choice-->
                    <Popup
                        v-if="popupContent.show"
                        :TogglePopup="() => popupContent.show = !popupContent.show"
                        :style="popupContent.format">
                        <div class="flex items-center flex-col px-4 py-6 bg-gray-500 ">
                            <div>
                                <h2>{{ popupContent.name }}</h2>
                            </div>
                            <div v-for="(choice, index) in popupContent.choices">
                                <button
                                    :style="choice.format"
                                    @click="chooseStateMenu(popupContent.targetField, index, 'buttonTrigger')">
                                    {{ choice.name }}
                                </button>
                            </div>
                            <div>
                                <button class="popup-close" @click="popupContent.show=false">
                                    Cancel
                                </button>
                            </div>

                        </div>
                    </Popup>

                    <Popup
                        v-if="hintObj.show"
                        :style="hintObj.format">
                        {{ hintObj.content }}
                    </Popup>

                    <Popup
                        v-if="dataPickerObj.show"
                        :TogglePopup="() => dataPickerObj.show = !dataPickerObj.show"
                        class="flex items-center"
                        :style="dataPickerObj.format">
                        <div class="bg-white rounded-xl px-2 py-2">
                            <div>
                                <DatePicker v-model="dataPickerObj.date"
                                            :value="dataPickerObj.date"
                                            :ref="'calendar'+this.item.id"
                                />
                            </div>
                            <div class="flex justify-between mt-2">
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="dueDatePickAction(format, ['lastYear'], $event)">
                                    Last year
                                </button>
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="dueDatePickAction(format, ['today'], $event)">
                                    Today
                                </button>
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="dueDatePickAction(format, ['nextYear'], $event)">
                                    Next year
                                </button>
                            </div>
                            <div class="flex justify-between mt-2">
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="dueDatePickAction(format, ['confirm'], $event)">
                                    Confirm
                                </button>
                                <button class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                        @click="dueDatePickAction(format, ['remove'], $event)">
                                    Remove
                                </button>
                                <button
                                    class="border rounded-2xl px-2 bg-blue-200 text-gray-500 font-medium"
                                    @click="dueDatePickAction(format, ['cancel'], $event)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </Popup>

                </div>
            </div>
        </td>
    </tr>

    <!--    last item: add row -->
    <tr v-if="item.id === this.group.items.length-1" class="flex items-center mt-4">
        <td style="height: 100%; width: 100%">
            <div class="flex align-left">
                <button
                    class="flex-shrink-0"
                    style="height: 50px; width: 20px;"
                    :style="{'background-color': getAddColor(group.color)}"
                />
                <input
                    placeholder="+ Add"
                    :ref="'addItem'+group.id"
                    v-on:change="addRowAction(['add'], $event)"
                    style="line-height: 100%; width: 100%"
                    class="ml-4"
                />
            </div>
        </td>
    </tr>

    <div v-if='subItemOpen' class="mt-5 ml-5 mb-10" style="width:100%">
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
                     :subitem="subitem"
                     :item="item"
                     :group="group"
                     :workspace="workspace"
            />
            </tbody>
        </table>
    </div>
</template>

<script>
import Subitem from "./Subitem";
import {ref} from 'vue';
import Popup from "./Popup";
import {now} from "lodash/date";
import {round} from "lodash/math";
import {DatePicker} from 'v-calendar';

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


function parseStringDate($s) {
    let $d = new Date(parseInt($s));
    return $d.toLocaleDateString('en-GB');
}

function parseStringDateTime($s) {
    let $d = new Date(parseInt($s));
    return $d.toLocaleDateString('en-GB') + '\n ' + zeroPad($d.getHours(), 2) + ":" + zeroPad($d.getMinutes(), 2) + ":" + zeroPad($d.getSeconds(), 2);
}

function convertDateToMillis(date) {
    return parseInt(date.getTime().toString());
}

function convertDateFromMillis(millis) {
    return new Date(parseInt(millis.toString()));
}

function deepCopyDate(date) {
    return convertDateFromMillis(convertDateToMillis(date));
}

export default {
    setup() {
        const dataPickerObj = ref({
            show: false,
            x: 0,
            y: 0,
            date: null,
            millis: 0,
            format: {
                'justify-content': 'center',
                'background-color': 'white',
            },
        });

        const hintObj = ref({
            show: false,
            x: 0,
            y: 0,
            content: '',
            format: '',
        })

        const popupContent = ref({
            show: false,
            name: '',
            choices: [],
            format: '',
            targetField: '',
        });

        const personObj = ref({
            buttonShown: false,
            search: '',
            users: Array,
            selections: Array,
        });

        const dueObj = ref({
            initialised: false,
            overdue: true,
            msg: '',
        });

        const subItemOpen = ref(false);

        const subItemContent = ref({
            groupColor: '',
            data: [],
            formats: [],
        });

        const textAreaZoomed = ref(false);

        const trackingObj = ref({
            isTimeTracking: false,
            timer: Date,
        });

        const defaultStateFormat = 'height: 20px; width: 100px; color: white; '

        return {
            dataPickerObj,
            hintObj,
            popupContent,
            personObj,
            dueObj,
            subItemOpen,
            subItemContent,
            textAreaZoomed,
            trackingObj,
            defaultStateFormat,
        }
    },

    components: {Subitem, Popup, DatePicker},

    props: {
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
                let obj = this.$refs['addItem' + this.group.id]
                let item = JSON.parse(JSON.stringify(this.group.items[0]));
                console.log("value", obj.value)
                item['Name'] = obj.value;
                item['id'] = this.group.items.length;
                item['Person'] = [];
                item['Note'] = '';
                item['Due Date'] = null;
                item['Status'] = null;
                item['Priority'] = null;
                item['created_at'] = new Date().getTime();
                item['LastUpdated'] = new Date().getTime();
                item['subitems'] = [item['subitems'][0]]; //keep one subitem
                // item['Time Tracking'] = Object();
                item['Time Tracking']['records'] = [];
                item['Time Tracking']['total'] = 0;
                console.log('add new item', item);
                this.group.items.push(item)
                obj.value = ''
            }
        },

        updateTimeTracking() {
            if (this.trackingObj.timer !== null) {
                let ref = 'trackingShowBtn' + this.item.id;
                let obj = this.$refs[ref];
                let startSecs = this.item['Time Tracking']['total'];
                let addedSeconds = (now() - new Date(this.trackingObj.timer)) / 1000;
                obj.innerHTML = this.parseTracking(startSecs + addedSeconds);
            }
        },
        parseTracking(sec) {
            let days = Math.floor(sec / 86400); // get days
            sec -= days * 86400;
            let hours = Math.floor(sec / 3600); // get hours
            sec -= hours * 3600;
            let minutes = Math.floor(sec / 60); // get minutes
            sec -= minutes * 60;
            let seconds = Math.floor(sec); //  get seconds
            let result = ''
            result += days > 0 ? days + 'd ' : ''
            result += hours > 0 ? hours + 'h ' : ''
            result += minutes > 0 ? minutes + 'm ' : ''
            result += seconds > 0 ? seconds + 's' : ''
            return result; // Return is 1d 3h 4m 5s
        },

        setLastUpdateTime() {
            this.item['LastUpdated'] = new Date().getTime();
        },

        chooseStateMenu(targetField, index, trigger) {
            let itemField = ''
            let ref = ''
            switch (targetField) {
                case 'Status':
                case 'Priority':
                    itemField = targetField === 'Status' ? 'statuses' : 'priorities';
                    ref = targetField.toLowerCase() + this.item.id;
                    let obj = this.$refs[ref];
                    if (index < this.group[itemField].length) {
                        this.item[targetField] = index;
                    } else {
                        this.item[targetField] = null;
                    }
                    this.setLastUpdateTime()
                    break;
                case 'Time Tracking':
                    this.trackEditAction('trackingShowBtn' + this.item.id);
                    this.setLastUpdateTime()
                    break;
                default:
                    console.log('chooseStateMenu', 'unmatched', targetField);
            }
            this.popupContent.show = false;
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
                case 'Name':
                case 'Time Tracking':
                case 'subitems':
                    return {
                        'align-items': 'center'
                    }
                case 'Person':
                case 'Due Date':
                    return {
                        'text-align': 'center',
                        'justify-content': 'center',
                    }
            }
        },

        setPrefixClass(format) {
            switch (format.name) {
                case 'Name': {
                    return 'flex justify-between space-x-4'
                }
                case 'Time Tracking': {
                    return 'flex space-x-2'
                }
                case 'subitems': {
                    return 'flex space-x-2'
                }
                case 'Person': {
                    return 'flex'
                }
                case 'Due Date': {
                    return 'flex items-center'
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
                        // 'margin-left': '10px',
                        // 'margin-right': '10px',
                        'width': '100%',
                        'max-width': '400px',
                        'max-height': '100px',
                        'vertical-align': 'top',
                        'justify-content': 'left',
                        'flex-direction': 'column',
                        'white-space': 'pre-wrap',
                        // 'width': 'max-content',
                    }
                case 'subitem':
                    return {
                        'vertical-align': 'top',
                        'text-align': 'center',
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
                        'background-color': this.item[$format.name] !== null ? this.group.statuses[parseInt(this.item[$format.name])]['color'] : 'rgb(197, 197, 197)',
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
                        'background-color': this.item[$format.name] !== null ? this.group.priorities[parseInt(this.item[$format.name])]['color'] : 'rgb(197, 197, 197)',
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
                    return 'button';
                case 'datetime':
                    return 'span';
                case 'date':
                    return 'button';
            }
        },

        setDueObj() {
            if (this.item['Due Date'] !== null) {
                this.dueObj.initialised = true;
                let diff = new Date(this.item['Due Date']).getTime() - new Date().getTime();
                let diffDays = Math.ceil(diff / (1000 * 3600 * 24));
                let dayOrDays = Math.abs(diffDays) > 1 ? ' days ' : ' day ';
                if (diffDays <= 0) {
                    this.dueObj.overdue = true;
                    this.dueObj.msg = Math.abs(diffDays) + dayOrDays + 'overdue';
                } else {
                    this.dueObj.overdue = false;
                    this.dueObj.msg = Math.abs(diffDays) + dayOrDays + 'left';
                }
            } else {
                this.dueObj.initialised = false;
            }


        },

        setCellValue($format) {
            if (this.item[$format.name] === null) return '&ZeroWidthSpace;';
            switch ($format['name']) {
                case 'Due Date':
                    this.setDueObj()
                    break;
            }
            switch ($format['type']) {
                case 'text':
                case 'url':
                case 'longtext':
                    return this.item[$format.name];
                case 'person':
                    return ''
                case 'status':
                    return this.group.statuses[parseInt(this.item[$format.name])]['name'];
                case 'priority':
                    return this.group.priorities[parseInt(this.item[$format.name])]['name'];
                case 'subitem':
                    return this.item[$format.name].length;
                case 'tracking':
                    return this.parseTracking(this.item[$format.name]['total']);
                case 'datetime':
                    return parseStringDateTime(this.item[$format.name]);
                case 'date':
                    return parseStringDate(this.item[$format.name]);
            }
        },

        getNames() {
            let nameIds = this.item['Person']
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

        personAction(action, event) {
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

        stateAction(format, options, event) {
            let trigger = options[1]
            let defaultState = Object()
            defaultState['format'] = this.defaultStateFormat + 'background-color: rgb(197, 197, 197)';
            defaultState['name'] = 'None'


            switch (format.name) {
                case 'Status':
                case 'Priority':{
                    console.log('stateAction123', format.name, options, event);
                    this.popupContent.name = format.name
                    let fieldName = format.name === 'Status' ? 'statuses' : 'priorities';
                    let choices = JSON.parse(JSON.stringify(this.group[fieldName]));
                    choices.forEach(e => e.format = this.defaultStateFormat + 'background-color:' + e.color);
                    choices.push(defaultState);
                    this.popupContent.choices = choices;
                    this.popupContent.targetField = format.name;
                    this.popupContent.x = event.clientX;
                    this.popupContent.y = event.clientY;
                    this.popupContent.show = true;
                    this.popupContent.format = {
                        'top': (this.popupContent.y - 40) + 'px',
                        'left': (this.popupContent.x + 40) + 'px',
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
                default: {
                    console.log('stateAction: unmatched choice')
                }
            }
        },

        itemAction(format, options, event) {
            let key = format.name + '.' + options[0];
            if (!['mouseleave', 'mouseover'].includes(options[0])) {
                console.log(key, event)
            }
            if (options[0] === 'change') {
                this.setLastUpdateTime()
            }
            switch (key) {
                case 'Note.mouseleave':
                case 'Note.mouseover': {
                    this.textAreaHintAction(format, options, event)
                    break;
                }

                case 'Due Date.mouseover':
                case 'Due Date.mouseleave': {
                    this.dueDateHintAction(format, options, event)
                    break;
                }

                case 'Due Date.click': {
                    this.dueDateEditAction(format, options, event);
                    break;
                }

                case 'Name.focus':
                case 'Note.focus' : {
                    this.textAreaZoomAction(format, options);
                    break;
                }

                case 'Priority.click':
                case 'Status.click': {
                    this.stateAction(format, options, event)
                    break;
                }

                case 'subitems.click': {
                    this.openSubitems(format, options[1]);
                    break;
                }

                case 'Time Tracking.click': {
                    this.trackEditAction('trackingShowBtn' + this.item.id)
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

        dueDateHintAction(format, options, event) {
            let key = format.name + '.' + options[0];
            // console.log(key);
            switch (key) {
                case 'Due Date.mouseover': {
                    // console.log('hint show')
                    this.hintObj.x = event.clientX;
                    this.hintObj.y = event.clientY;
                    this.hintObj.show = true;
                    this.hintObj.content = this.dueObj.msg;
                    this.hintObj.format = {
                        'top': (this.hintObj.y - 40) + 'px',
                        'left': (this.hintObj.x + 40) + 'px',
                        'width': '100%',
                        'height': '100%',
                        'max-width': '200px',
                        'max-height': '50px',
                        'vertical-align': 'top',
                        'text-align': 'left',
                        'background-color' : 'rgb(100, 100, 100)',
                    }
                    break;
                }
                case 'Due Date.mouseleave': {
                    // console.log('hint close')
                    this.hintObj.show = false;
                    break;
                }
            }

        },

        async dueDatePickAction(format, options, event) {
            // console.log('dueDatePickAction', format, options);
            const calendar = this.$refs['calendar' + this.item.id];
            let tempDate;
            switch (options[0]) {
                case 'confirm':
                    this.item['Due Date'] = this.dataPickerObj.date.getTime();
                    this.dataPickerObj.show = false;
                    this.setLastUpdateTime();
                    break;
                case 'cancel':
                    this.dataPickerObj.show = false;
                    break;
                case 'remove':
                    this.item['Due Date'] = null;
                    this.dueObj.initialised = false;
                    this.dataPickerObj.show = false;
                    break;
                case 'nextYear':
                    this.dataPickerObj.millis += 1000 * 60 * 60 * 24 * 365; // 1 year
                    tempDate = new Date(this.dataPickerObj.millis);
                    await calendar.move(tempDate);
                    break;
                case 'lastYear':
                    this.dataPickerObj.millis -= 1000 * 60 * 60 * 24 * 365; // 1 year
                    tempDate = new Date(this.dataPickerObj.millis);
                    await calendar.move(tempDate);
                    break;
                case 'today':
                    this.dataPickerObj.date = new Date();
                    this.dataPickerObj.millis = parseInt(this.dataPickerObj.date.getTime());
                    await calendar.move(this.dataPickerObj.date)
                    break;
            }

        },

        dueDateEditAction(format, options, event) {
            // console.log('dueDateEditAction', format, options, event);
            let dateNumber = this.item['Due Date'];
            this.dataPickerObj.date = new Date(parseInt(dateNumber));
            this.dataPickerObj.millis = parseInt(dateNumber);
            this.dataPickerObj.x = event.clientX;
            this.dataPickerObj.y = event.clientY;
            this.dataPickerObj.show = true;
            this.dataPickerObj.format = {
                'background-color': 'rgba(0, 0, 0, 0.1)',
            };

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
                    this.hintObj.show = true;
                    this.hintObj.content = obj.value;
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
                        'background-color' : 'rgb(100, 100, 100)',
                    }
                    break;
                }
                case 'Note.mouseleave': {
                    // console.log('hint close')
                    this.hintObj.show = false;
                    break;
                }
            }

        },

        textAreaZoomAction(format, options) {
            // console.log('textAreaZoomAction', format, options);
            let $ref = this.setRef(format)
            // console.log(format, options)
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
                $obj.style.zIndex = '10';
                this.textAreaZoomed = true;
                //close hint when zoomed
                this.textAreaHintAction(format, ['mouseleave']);
            } else {
                this.item[format.name] = $obj.value;
                $obj.style.position = 'relative';
                $obj.rows = $obj.rect_initial.rows;
                $obj.cols = $obj.rect_initial.cols
                $obj.style.zIndex = '0';
                this.textAreaZoomed = false;
            }
        },

        trackPlayAction(ref) {
            // let exitCode = 0;
            // console.log('trackPlayAction', ref);
            if (this.trackingObj.isTimeTracking === false) {
                this.trackingObj.timer = new Date(now());
                let exitCode = this.createTrackingRecord();
                if (exitCode === 0) {
                    setInterval(this.updateTimeTracking, 1000);
                    this.trackingObj.isTimeTracking = true;
                } else {
                    clearInterval(this.updateTimeTracking);
                    this.trackingObj.isTimeTracking = false;
                }

            } else {
                this.trackingObj.timer = null
                clearInterval(this.updateTimeTracking);
                this.postProcessTrackingRecord();
                this.trackingObj.isTimeTracking = false;
            }
        },

        trackEditAction(ref) {
            console.log('trackEditAction', ref);
        },

        createTrackingRecord: function () {
            let exitCode = 0
            // check if last record is stopped
            let createRecord = (id = 0) => {
                return Object({
                    'id': id,
                    'Start time': new Date(now()),
                    'End time': null,
                    'Remark': '',
                    'Work hour': 0
                });
            }
            //append new tracking record
            if (this.item['Time Tracking']['records'].length > 0) {
                let index = this.item['Time Tracking']['records'].length
                let record = this.item['Time Tracking']['records'][index - 1]
                // check if last record is not stopped
                if (record['End time'] === null) {
                    this.popupContent.name = 'Alert';
                    this.popupContent.targetField = 'Time Tracking';
                    this.popupContent.choices = Array(Object({
                        'name': 'Last time tracking is not stopped!',
                        'color': 'red',
                        'format': 'width: 150px; color: white; background-color: red',
                        'white-space': 'normal',
                        'overflow-wrap': 'normal',
                    }));
                    this.popupContent();//popup alert
                    exitCode = 1;
                } else {
                    this.item['Time Tracking']['records'].push(createRecord(index));
                }
            } else {//very first record
                this.item['Time Tracking']['records'].push(createRecord());
            }
            return exitCode
        },

        postProcessTrackingRecord() {
            // check if last record is stopped
            if (this.item['Time Tracking']['records'].length > 0) {
                let index = this.item['Time Tracking']['records'].length
                let record = this.item['Time Tracking']['records'][index - 1]
                record['End time'] = new Date(now());
                let startTime = new Date(record['Start time'])
                record['Work hour'] = round((record['End time'] - startTime) / 1000, 0);
                this.item['Time Tracking']['records'][index - 1] = record;
                this.item['Time Tracking']['total'] += record['Work hour'];
            }
        },

        getRef(format, option = null) {
            // console.log('getRef')
            // console.log(format, option)
            // console.log(ref)
            return this.setRef(format, option);
        },

        setRef($format, option = null) {
            if ($format['type'] === 'text') {
                return 'text' + this.item.id
            }
            if ($format['type'] === 'longtext') {
                return 'longtext' + this.item.id
            }
            if ($format['type'] === 'person') {
                return 'person' + this.item.id
            }
            if ($format['type'] === 'url') {
                return 'url' + this.item.id
            }
            if ($format['name'] === 'Status') {
                return 'status' + this.item.id
            }
            if ($format['name'] === 'Priority') {
                return 'priority' + this.item.id
            }
            if ($format['type'] === 'tracking') {
                return 'trackingShowBtn' + this.item.id
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
