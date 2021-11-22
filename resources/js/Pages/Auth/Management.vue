<template>
    <Head title="Management"/>
    <div class="flex justify-between  mb-6">
        <div class="flex items-center">
            <h1 className="text-3xl">
                Management
            </h1>
        </div>
    </div>


    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="group in workspace.groups"
                            :key="group.id"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"
                                        >
                                            <div :style="groupColor(group.color)">
                                                <p>{{ group.name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import Pagination from "../../Shared/Pagination";
import {ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
// during typing make the call every N milliseconds
import throttle from "lodash/throttle";
// make the call when no typing for N milliseconds
import debounce from "lodash/debounce";

let props = defineProps({
    workspace: Object,
    can: Object,
});

let groupColor = ($color) => {
    return {'background-color': $color,}
}
</script>

<style scoped>

</style>
