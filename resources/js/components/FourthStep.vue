<template>
    <a-flex :justify="'space-between'" style="margin-bottom: 10px;">
        <a-typography-paragraph>Meal</a-typography-paragraph>
        <a-typography-paragraph>{{ currentMeal }}</a-typography-paragraph>
    </a-flex>
    <a-flex :justify="'space-between'" style="margin-bottom: 10px">
        <a-typography-paragraph>No. of people</a-typography-paragraph>
        <a-typography-paragraph>{{ numberPeople }}</a-typography-paragraph>
    </a-flex>
    <a-flex :justify="'space-between'" style="margin-bottom: 10px">
        <a-typography-paragraph>Restaurant</a-typography-paragraph>
        <a-typography-paragraph>{{ currentRestaurant }}</a-typography-paragraph>
    </a-flex>
    <a-flex :justify="'space-between'">
        <a-typography-paragraph>Dishes</a-typography-paragraph>
        <div style="padding: 10px; border-radius: 6px; border: 1px solid #d9d9d9; box-shadow: 0 2px 0 rgba(0, 0, 0, 0.02);">
            <a-typography-paragraph v-for="(dish, index) in blockDishes" :key="index">
                {{ dish.currentDish + ' - ' + dish.numberServing  }}
            </a-typography-paragraph>
        </div>
    </a-flex>
</template>
<script>
    export default {
        props: {
            currentMeal: String,
            numberPeople: Number,
            currentRestaurant: String,
            blockDishes: Array
        }
    }
</script>
