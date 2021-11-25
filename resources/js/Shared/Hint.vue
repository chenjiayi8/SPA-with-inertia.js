<template>
    <div class="hint">
        <div class="hint-inner">
            <slot/>
        </div>
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
    top: 100px;
    left: 100px;
    z-index: 99;
    color: white;
    padding: 10px;
    background-color: gray;
    white-space: normal;
    max-width: 400px;
    font-size: small;
    vertical-align: top;
    text-align: left;
    overflow-wrap: normal;

    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
