<template>
    <a-typography-paragraph>Please select a meal</a-typography-paragraph>
    <a-select :value="currentMeal" :options="meals" style="width: 100px; margin-bottom: 10px;" @change="handleMealChange"></a-select>
    <a-typography-paragraph>Please enter number of people</a-typography-paragraph>
    <a-input-number :value="numberPeople" :min="1" :max="10" style="width: 65px;" @change="handleNumberPeopleChange"></a-input-number>
</template>
<script>
    export default {
        emits: ['handleMeal', 'handleNumberPeople'],

        props: {
            currentMeal: String,
            meals: Array,
            numberPeople: Number
        },

        methods: {
            handleMealChange(value) {
                this.$emit('handleMeal', value)
            },

            handleNumberPeopleChange(value) {
                this.$emit('handleNumberPeople', value);
            }
        }
    }
</script>
