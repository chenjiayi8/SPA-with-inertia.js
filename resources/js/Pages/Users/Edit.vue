<template>
    <Head title="Create User"/>
    <h1 class="text-3xl">Update User</h1>

    <!-- when submit, prevent the default action, and instead use the defined submit function in script setup-->
    <form @submit.prevent="submit" class="max-w-md max-auto mt-8">
        <div class="mb-6">
            <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                   for="name"
            >
                Name
            </label>
            <input class="border border-gray-400 p-2 w-full"
                   v-model="form.name"
                   type="text"
                   name="name"
                   id="name"
                   required
            >
            <div v-if="form.errors.name" v-text="form.errors.name" class="text-red-500 text-xs mt-1"></div>

        </div>
        <div class="mb-6">
            <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                   for="email"
            >
                Email
            </label>
            <input class="border border-gray-400 p-2 w-full"
                   v-model="form.email"
                   type="email"
                   name="email"
                   id="email"
                   required
            >
            <div v-if="form.errors.email" v-text="form.errors.email" class="text-red-500 text-xs mt-1"></div>
        </div>

        <div class="mb-6">
            <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                   for="password"
            >
                Password
            </label>
            <input class="border border-gray-400 p-2 w-full"
                   v-model="form.password"
                   type="password"
                   name="password"
                   id="password"
            >
            <div v-if="form.errors.password" v-text="form.errors.password" class="text-red-500 text-xs mt-1"></div>
        </div>

        <div class="mb-6">
            <button type="submit"
                    class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500" :disabled="form.processing"
            >
                Update
            </button>
        </div>

    </form>


</template>

<script setup>
// this setup script is ran before everything
import {useForm} from "@inertiajs/inertia-vue3";

let props = defineProps({
    user: Object,
    model: Object,
    can: Object,
});

let form = useForm({
    id: props.model.id,
    name: props.model.name,
    email: props.model.email,
    password: '',
});

function sleepFor(sleepDuration){
    var now = new Date().getTime();
    while(new Date().getTime() < now + sleepDuration){
        /* Do nothing */
    }
}


let submit = () => {
    form.post('/users/'+props.model.id+'/edit');
}
</script>

<style scoped>

</style>
