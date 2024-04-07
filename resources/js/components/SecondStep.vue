<template>
    <a-typography-paragraph>Please select a restaurant</a-typography-paragraph>
    <a-select :value="currentRestaurant" :options="restaurants" style="width: 150px;" @change="handleRestaurantChange"></a-select>
</template>
<script>
    export default {
        props: {
            currentRestaurant: String,
            restaurants: Array
        },

        methods: {
            handleRestaurantChange(value) {
                this.$emit('handleRestaurant', value);
            },
        }
    }
</script>
