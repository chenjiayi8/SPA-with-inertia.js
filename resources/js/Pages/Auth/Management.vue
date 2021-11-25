<template>
    <Head title="Management"/>
    <div class="flex justify-between mb-6">
        <div class="flex items-center">
            <h1 className="text-3xl">
                Management
            </h1>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full">
                <div class="overflow-hidden sm:rounded-lg">
                    <Group v-for="group in workspace.groups"
                           :key="group.id"
                           v-bind:group="group"
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

let props = defineProps({
    workspace: Object,
    can: Object,
});

</script>

<style scoped>

</style>
