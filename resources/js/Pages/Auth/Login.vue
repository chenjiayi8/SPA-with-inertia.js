<template>
    <Head title="Login page"/>

    <main class="grid place-items-center min-h-screen">
        <section class="bg-white p-8 rounded-xl max-w-md mx-auto border">
            <div class="flex  items-center justify-between">
                <h1 class="text-3xl mb-6">Login</h1>
                <Link class="text-sm text-blue-500 ml-2" href="/users/create">Not registered?</Link>
            </div>

            <form @submit.prevent="submit" class="max-w-md max-auto mt-8">

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="email"
                    >
                        Email
                    </label>
                    <input class="border p-2 w-full rounded"
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
                    <input class="border p-2 w-full rounded"
                           v-model="form.password"
                           type="password"
                           name="password"
                           id="password"
                           required
                    >
                    <div v-if="form.errors.password" v-text="form.errors.password"
                         class="text-red-500 text-xs mt-1"></div>
                </div>

                <div class="mb-6">
                    <button type="submit"
                            class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500"
                            :disabled="form.processing"
                    >
                        Log in
                    </button>
                </div>

            </form>


        </section>
    </main>
</template>

<script>
export default {
    name: "Login",
    layout: null,
}
</script>

<script setup>
import {useForm} from "@inertiajs/inertia-vue3";

let form = useForm({
    email: '',
    password: '',
});

let submit = () => {
    form.post('/login/');
}
</script>

<style scoped>

</style>
