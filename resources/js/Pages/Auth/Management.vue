<template>
    <Head title="Management"/>
    <div class="flex justify-between mb-6">
        <div class="flex items-center">
            <h1 className="text-3xl">
                Management
            </h1>
            <button @click="addGroup" class="border rounded-2xl text-gray-500 ml-5 px-2 py-1">Add new group</button>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full">
                <div class="overflow-hidden sm:rounded-lg">
                    <Group v-for="group in workspace.groups"
                           :key="group.id"
                           :group="group"
                           :workspace="workspace"
                    />
                </div>
            </div>
        </div>
        <!-- todo-->
        <!--        <div id="floating-scrollbar"-->
        <!--             style="position: fixed; bottom: 0px; height: 30px; overflow: auto hidden; left: 0px; width: 480.333px; display: block;">-->
        <!--            <div style="border: 1px solid rgb(255, 255, 255); opacity: 0.01; width: 736.333px;"></div>-->
        <!--        </div>-->
    </div>

</template>

<script setup>

import Group from "../../Shared/Group";
import {ref, watch} from "vue";
import debounce from "lodash/debounce";
import hash from "object-hash";
import {Inertia} from "@inertiajs/inertia";

let props = defineProps({
    workspace: Object,
    can: Object,
});

// update table in server every N seconds
let workspace = ref(props.workspace);
// let objList = {
//     dataPickerObj: false,
// }
// let count = 0
// setInterval(() => {
//     console.log("timeout management triggered");
//     console.log("Will post but not now")
//     // console.log(count++, hash(props.workspace));
//     // Inertia.post("/admin", {workspace: workspace.value}, {
//     //     preserveState: true,
//     //     preserveScroll: true,
//     //     replace: true,
//     // });
// }, 10000);

</script>

<script>
export default {
    name: "Management",

    methods: {
        addGroup() {
            console.log("addGroup", 'before', this.workspace.groups.length);
            let group = JSON.parse(JSON.stringify(this.workspace.groups[0]));
            group['name'] = 'New group'
            group['items'] = [group['items'][0]];
            group['items'][0]['subitems'] = [group['items'][0]['subitems'][0]];
            group['id'] = this.workspace.groups.length;
            this.workspace.groups.push(group);
            console.log("addGroup", 'after', this.workspace.groups.length);
        }
    }
}


</script>

<style scoped>

</style>
