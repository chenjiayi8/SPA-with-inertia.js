<template>
    <div>
        <component
            :is="link.url ? 'Link':'span'"
            v-for="link in links"
            :href="link.url"
            v-html="link.label"
            class="px-1"
            :class="{'text-gray-500': !link.url, 'font-bold': link.active}"
        />
<!--        if link.url is null: text-gray-500-->
<!--        if link is active: font-bold-->
        <div />
    </div>
</template>

<script>
export default {
    name: "Pagination.vue",
    props: {
        links: Array
    }
}
</script>

<style scoped>

</style>
