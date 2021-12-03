<template>
    <div>Demonstration of popup menu</div>
    <button @click="() => TogglePopup('buttonTrigger')"
            class="bg-blue-500 text-white uppercase font-semibold text-ws py-2 px-10 rounded-2xl hover:bg-blue-600 mt-6">
        Open Popup
    </button>

    <Popup
        v-if="popupTriggers.buttonTrigger"
        :TogglePopup="() => TogglePopup('buttonTrigger')">
        <h2>{{ menu.title }}</h2>
        <div>
            <div v-for="item in menu.options">
                <button @click="chooseMenu(item, 'buttonTrigger')">
                    {{ item }}
                </button>
            </div>

        </div>
        <h2>{{ menu.footer }}</h2>
    </Popup>
    <Popup
        v-if="popupTriggers.timedTrigger"
        :TogglePopup="() => TogglePopup('timedTrigger')">
        <h2>My Timed Popup</h2>
        <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
        </p>
    </Popup>
</template>

<script setup>
import {ref} from 'vue';
import Popup from '../Backup/Popup.vue'

let props = defineProps({
    menu: Object,
})

let menu = {
    'title': 'Menu',
    'options': ['Burger', 'Salad', 'Fries'],
    'footer': 'Thanks for your order'
};

const popupTriggers = ref({
    buttonTrigger: false,
    timedTrigger: false
});

const TogglePopup = (trigger) => {
    popupTriggers.value[trigger] = !popupTriggers.value[trigger]
}

setTimeout(() => {
    popupTriggers.value.timedTrigger = true;
}, 3000);

function chooseMenu(item, trigger) {
    console.log(item);
    TogglePopup(trigger);
}

</script>

<style scoped>

</style>
