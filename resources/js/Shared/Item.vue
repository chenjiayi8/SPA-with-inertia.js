<template>
    <tr class="flex items-center mt-4">
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
                <div v-if="format.name === 'Person'" class="namesHolder flex items-center"
                     :ref="'namesHolder'+this.item.id"
                     v-on:mouseover="personAction('mouseover', $event)"
                     v-on:mouseleave="personAction('mouseleave', $event)"
                     v-on:change="itemAction(format, ['change', ''], $event)"
                     :style="{'width': ((1+getNames().length)*30)+'px'}">
                    <button v-show="this.personObj.buttonShown"
                            :ref="'personsEdit'+this.item.id" class="circledButton"
                            style="position: absolute; left: 0px; background-color: blue"
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
                        @click="() => itemAction(format,['click', 'buttonTrigger'])"
                        @ondblclick="() => itemAction(format,['dblclick', 'buttonTrigger'])"
                        @focus="itemAction(format, ['focus','in'])"
                        @blur="itemAction(format, ['focus','out'])"
                        :style="formatContent(format)"
                        class="px-1"
                    />

                    <Popup
                        v-if="popupTriggers.buttonTrigger"
                        :TogglePopup="() => TogglePopup('buttonTrigger')"
                    >
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
import Popup from './Popup.vue'
import Hint from "./Hint";
import {now} from "lodash/date";
import {round} from "lodash/math";


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

        const hintTriggers = ref({
            mouseTrigger: false,
        });

        const hintObj = ref({
            show: false,
            x: 0,
            y: 0,
            content: '',
            format: '',
        })

        const popupContent = ref({
            name: '',
            choices: [],
            format: '',
            targetField: '',
        });

        const personObj = ref({
            buttonShown: false,
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

        const TogglePopup = (trigger) => {
            popupTriggers.value[trigger] = !popupTriggers.value[trigger]
        };

        // setTimeout(() => {
        //     console.log("item triggered");
        //
        // }, 1000);

        return {
            popupTriggers,
            hintTriggers,
            hintObj,
            TogglePopup,
            popupContent,
            personObj,
            dueObj,
            subItemOpen,
            subItemContent,
            textAreaZoomed,
            trackingObj,
        }
    },

    components: {Subitem, Popup, Hint},

    props: {
        item: Object,
        group: Object,
        workspace: Object,
    },

    methods: {
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

        setLastUpdateTime(){
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
                    obj.innerHTML = this.group[itemField][index].name;
                    this.item[targetField] = index;
                    this.setLastUpdateTime()
                    break;
                case 'Time Tracking':
                    this.trackEditAction('trackingShowBtn' + this.item.id);
                    this.setLastUpdateTime()
                    break;
                default:
                    console.log(targetField);
            }
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
                case 'Time Tracking': {
                    return {
                        'align-items': 'center'
                    }
                }
                case 'subitems': {
                    return {
                        'align-items': 'center'
                    }
                }
                case 'Person': {
                    return {
                        'text-align': 'center',
                        'justify-content': 'center',
                    }
                }
                case 'Due Date': {
                    return {
                        'text-align': 'center',
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

        setDueObj(){
            if (this.item['Due Date'] !== null){
                this.dueObj.initialised = true;
                let diff = new Date(this.item['Due Date']).getTime() - new Date().getTime();
                let diffDays = Math.ceil(diff / (1000 * 3600 * 24));
                let dayOrDays = Math.abs(diffDays) > 1 ? ' days ': ' day ';
                if (diffDays < 0){
                    this.dueObj.overdue = true;
                    this.dueObj.msg = Math.abs(diffDays) + dayOrDays + 'overdue';
                } else {
                    this.dueObj.overdue = false;
                    this.dueObj.msg = Math.abs(diffDays) + dayOrDays + 'left';
                }
                console.log(this.dueObj.msg);
            } else {
                this.dueObj.initialised = false;
            }


        },

        setCellValue($format) {
            switch($format['name']){
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
                    'left': (20 * (1 + i) + (i ===0 ? 2:0)) + 'px',
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
            if (!['mouseleave', 'mouseover'].includes(options[0])) {
                console.log(key)
            }
            if (options[0] === 'change'){
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

                case 'Name.focus':
                case 'Note.focus' : {
                    this.textAreaZoomAction(format, options);
                    break;
                }

                case 'Priority.click':
                case 'Status.click': {
                    this.stateAction(format, options)
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
            console.log('dueDateHintAction')
            let key = format.name + '.' + options[0];
            // console.log(key);
            switch (key) {
                case 'Due Date.mouseover': {
                    // console.log('hint show')
                    this.hintObj.x = event.clientX;
                    this.hintObj.y = event.clientY;
                    this.hintTriggers['mouseTrigger'] = true;
                    this.hintObj.show = true;
                    this.hintObj.content = this.dueObj.msg;
                    this.hintObj.format = {
                        'top': (this.hintObj.y - 40) + 'px',
                        'left': (this.hintObj.x + 40) + 'px',
                    }
                    break;
                }
                case 'Due Date.mouseleave': {
                    // console.log('hint close')
                    this.hintTriggers['mouseTrigger'] = false;
                    this.hintObj.show = false;
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
            console.log('trackPlayAction', ref);
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
                    this.TogglePopup('buttonTrigger');//popup alert
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
                let startTime =  new Date(record['Start time'])
                record['Work hour'] = round((record['End time']  - startTime) / 1000, 0);
                this.item['Time Tracking']['records'][index-1] = record;
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

.circledButton{
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
