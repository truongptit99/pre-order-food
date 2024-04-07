<style>
    .group-button {
        margin-top: 20px;
    }
</style>
<template>
    <div class="group-button">
        <a-flex :justify="Number(this.activeKey) > 1 ? 'space-between' : 'end'"> 
            <a-button v-if="Number(this.activeKey) > 1" @click="$emit('prevTab')">Previous</a-button>
            <a-button v-if="Number(this.activeKey) < 4" type="primary" @click="$emit('nextTab')">Next</a-button>
            <a-button v-if="Number(this.activeKey) == 4" type="primary" @click="$emit('submit')">Submit</a-button>
        </a-flex>
    </div>
</template>
<script>
    export default {
        props: {
            activeKey: String
        },
    }
</script>
