<template>
    <nav>
        <ul class="flex space-x-6">
            <li>
                <NavLink
                    href="/"
                    :active="$page.component=== 'Home'"
                >
                    Home
                </NavLink>
            </li>

            <li>
                <NavLink
                    href="/users"
                    :active="$page.component=== 'Users'"
                >
                    Users
                </NavLink>
            </li>

            <li>
                <NavLink
                    href="/settings"
                    :active="$page.component=== 'Settings'"
                >
                    Settings
                </NavLink>
            </li>
            <li v-if="logged">
                <NavLink
                    href="/admin"
                    :active="$page.component=== 'Management'"
                >
                    Administration
                </NavLink>
            </li>
            <li v-if="logged">
                <NavLink href="/logout" method="post">
                    Log Out
                </NavLink>
            </li>
            <li v-if="!logged">
                <NavLink href="/login">
                    Log in
                </NavLink>
            </li>
            <li v-if="!logged">
                <NavLink href="/users/create">
                    Register
                </NavLink>
            </li>
        </ul>
    </nav>
</template>

<script>
import NavLink from "./NavLink";
import {Head} from "@inertiajs/inertia-vue3";
export default {
    components: {NavLink},
    computed: {
        logged() {
            return this.$page.props.auth;
        }
    }
};
</script>
