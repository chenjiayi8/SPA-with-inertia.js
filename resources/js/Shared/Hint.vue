<template>
    <div class="hint">
        <slot/>
    </div>
</template>

<script>
export default {
    props: ['ToggleHint', 'format']
}
</script>

<style lang="scss" scoped>
.hint {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99;
    color: white;
    padding: 10px;
    background-color: gray;
    white-space: normal;
    font-size: medium;
    vertical-align: top;
    text-align: left;
    overflow-wrap: normal;

    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
