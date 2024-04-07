<template>
    <a-flex :justify="'space-between'">
        <a-typography-paragraph>Please select a dish</a-typography-paragraph>
        <a-typography-paragraph>Please enter no. of servings</a-typography-paragraph>
    </a-flex>
    <a-flex :justify="'space-between'" style="margin-bottom: 10px;" v-for="(dish, index) in blockDishes" :key="index"> 
        <a-select :value="dish.currentDish" :options="dish.dishes" style="width: 180px;" @change="handleDishChange(dish, index, $event)"></a-select>
        <a-input-number :value="dish.numberServing" :min="1" :max="10" style="width: 65px" @change="handleNumberServingChange(dish, $event)"></a-input-number>
    </a-flex>
    <div style="margin-top: 10px;">
        <plus-circle-outlined :style="{ fontSize: '20px', cursor: 'pointer' }" @click="$emit('handleAddDish')"/>
    </div>
</template>
<script>
    import { PlusCircleOutlined } from '@ant-design/icons-vue';

    export default {
        components: {
            PlusCircleOutlined
        },

        props: {
            blockDishes: Array
        },

        methods: {
            handleDishChange(dish, index, value) {
                this.$emit('handleDish', dish, index, value);
            },

            handleNumberServingChange(dish, value) {
                this.$emit('handleNumberServing', dish, value);
            }
        }
    }
</script>
