<template>
    <button @click="dataPickerObj.mouseTrigger=true">Show Date Picker</button>
    <Hint
        v-if="dataPickerObj.mouseTrigger"
        :ToggleHint="() => ToggleDatePicker('mouseTrigger')"
        class="flex items-center"

        :style="dataPickerObj.format">
        <div>
            <div>
                <DatePicker v-model="dataPickerObj.date"/>
            </div>
            <div class="flex justify-between ml-5 mr-5 mt-2">
                <button class="border text-gray-500 font-medium"
                        @click="dataPickerObj.mouseTrigger=false">Confirm
                </button>
                <button
                    class="border text-gray-500 font-medium"
                    @click="dataPickerObj.mouseTrigger=false; dataPickerObj.date=dataPickerObj.origin">Cancel
                </button>
            </div>
        </div>
    </Hint>
</template>

<script>
import {DatePicker} from 'v-calendar';
import {ref} from "vue";
import Hint from "../../Shared/Hint";

export default {
    name: "TestVCalendar",

    setup() {
        const dataPickerObj = ref({
            mouseTrigger: false,
            show: false,
            x: 0,
            y: 0,
            origin: new Date(),
            date: new Date(),
            format: {
                'justify-content': 'center',
                'background-color': 'white',
            },
        })

        const ToggleDatePicker = (trigger) => {
            dataPickerObj[trigger] = !dataPickerObj[trigger]
        };

        return {
            dataPickerObj,
            ToggleDatePicker,
        }
    },

    components: {DatePicker, Hint},

}
</script>

<style scoped>

</style>
