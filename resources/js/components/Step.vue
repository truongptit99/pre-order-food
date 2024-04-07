<template>
    <a-tabs v-model:activeKey="currentTab" centered>
        <a-tab-pane key="1" tab="Step 1" disabled>
            <a-typography-paragraph>Please select a meal</a-typography-paragraph>
            <a-select v-model:value="currentMeal.value" style="width: 100px;" @change="handleMealChange">
                <a-select-option value="breakfast">Breakfast</a-select-option>
                <a-select-option value="lunch">Lunch</a-select-option>
                <a-select-option value="dinner">Dinner</a-select-option>
            </a-select>
            <a-typography-paragraph>Please enter number of people</a-typography-paragraph>
            <a-input-number v-model:value="numberPeople" :min="1" :max="10" style="width: 65px;" @change="handleNumberPeopleChange"></a-input-number>
        </a-tab-pane>
        <a-tab-pane key="2" tab="Step 2" disabled>
            <a-typography-paragraph>Please select a restaurant</a-typography-paragraph>
            <a-select v-model:value="currentRestaurant" :options="restaurants" style="width: 150px;" @change="handleRestaurantChange"></a-select>
        </a-tab-pane>
        <a-tab-pane key="3" tab="Step 3" disabled>
            <div class="dish-select-group">
                <a-flex :justify="'space-between'">
                    <a-typography-paragraph>Please select a dish</a-typography-paragraph>
                    <a-typography-paragraph>Please enter no. of servings</a-typography-paragraph>
                </a-flex>
                <a-flex :justify="'space-between'" style="margin-bottom: 10px;" v-for="(block, index) in blockDishes" :key="index"> 
                    <a-select v-model:value="block.currentDish" :options="block.dishes" style="width: 180px;" @change="handleDishChange(block, index)"></a-select>
                    <a-input-number v-model:value="block.numberServing" :min="1" :max="10" style="width: 65px" @change="handleNumberServingChange(block)"></a-input-number>
                </a-flex>
            </div>
            <div style="margin-top: 10px;">
                <plus-circle-outlined :style="{ fontSize: '20px', cursor: 'pointer' }" @click="addDish"/>
            </div>
        </a-tab-pane>
        <a-tab-pane key="4" tab="Review" disabled>
            <a-flex :justify="'space-between'" style="margin-bottom: 10px;">
                <a-typography-paragraph>Meal</a-typography-paragraph>
                <a-typography-paragraph>{{ currentMeal.label }}</a-typography-paragraph>
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
                    <a-typography-paragraph v-for="(block, index) in blockDishes" :key="index">
                        {{ block.currentDish + ' - ' + block.numberServing  }}
                    </a-typography-paragraph>
                </div>
            </a-flex>
        </a-tab-pane>
    </a-tabs>
    <group-button :activeKey="currentTab" @nextTab="nextStep" @prevTab="prevStep" @submit="submitOrder"></group-button>
</template>
<script>
    import GroupButton from './GroupButton.vue';
    import { PlusCircleOutlined } from '@ant-design/icons-vue';
    import { message } from 'ant-design-vue';

    export default {
        components: { PlusCircleOutlined, GroupButton },

        data() {
            return {
                currentTab: '1',
                currentMeal: {
                    value: 'breakfast',
                    label: 'Breakfast'
                },
                isMealChanged: false,
                numberPeople: 1,
                linkGetRestaurant: 'http://127.0.0.1:8000/api/restaurants/',
                currentRestaurant: '',
                isRestaurantChanged: false,
                restaurants: [],
                linkGetDishes: 'http://127.0.0.1:8000/api/dishes?',
                blockDishes: [],
                arrCurrentDish: [],
                arrDish: [],
            }
        },

        methods: {
            nextStep() {
                if (Number(this.currentTab) === 3) {
                    let totalDishServing = this.blockDishes.reduce((accumulator, currentValue) => accumulator + currentValue.numberServing, 0);

                    if (totalDishServing < this.numberPeople) {
                        let strPersonOrPeople = this.numberPeople > 1 ? ' people' : ' person';
                        message.warning('The number of servings is not enough to ' + this.numberPeople + strPersonOrPeople);
                        return;
                    }
                }

                if (Number(this.currentTab) < 4) {
                    this.currentTab = String(Number(this.currentTab) + 1);
                }

                if ((Number(this.currentTab) === 2) && (this.isMealChanged === true || this.restaurants.length === 0)) {
                    fetch(this.linkGetRestaurant + this.currentMeal.value)
                        .then(response => response.json())
                        .then(restaurants => {
                            let arrRestaurants = [];
                            Object.values(restaurants[0]).forEach(restaurant => {
                                arrRestaurants.push({
                                    value: restaurant,
                                    label: restaurant
                                });
                            });

                            this.restaurants = arrRestaurants;
                            this.currentRestaurant = this.restaurants[0].value;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }

                if ((Number(this.currentTab) === 3) && (this.isMealChanged === true || this.isRestaurantChanged === true || this.blockDishes.length === 0)) {
                    let params = new URLSearchParams();
                    params.append('meal', this.currentMeal.value);
                    params.append('restaurant', this.currentRestaurant);

                    fetch(this.linkGetDishes + params.toString())
                        .then(response => response.json())
                        .then(dishes => {
                            let arrDishes = [];
                            Object.values(dishes[0]).forEach(dish => {
                                arrDishes.push({
                                    value: dish,
                                    label: dish
                                });
                            });

                            this.arrDish = arrDishes;
                            this.arrCurrentDish = [];
                            this.blockDishes = [];

                            this.arrCurrentDish.push(arrDishes[0].value);
                            this.blockDishes.push({
                                currentDish: arrDishes[0].value,
                                dishes: arrDishes,
                                numberServing: 1,
                            });
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            },

            prevStep() {
                if (Number(this.currentTab) > 1) {
                    this.currentTab = String(Number(this.currentTab) - 1);
                }

                if (Number(this.currentTab) === 1) {
                    this.isMealChanged = false;
                }

                if (Number(this.currentTab) === 2) {
                    this.isMealChanged = false;
                    this.isRestaurantChanged = false;
                }
            },

            addDish() {
                if (this.blockDishes.length === 1 && this.blockDishes[0].dishes.length === 1) {
                    message.error('Only have one dish and you can not add more');
                    return;
                }

                let totalNumberServing = this.blockDishes.reduce((accumulator, currentValue) => accumulator + currentValue.numberServing, 0);

                if (totalNumberServing === 10) {
                    message.error('The maximum number of dishes can not exceed 10');
                    return;
                }

                let remainingDish = this.arrDish.filter(item => {
                    return !this.arrCurrentDish.some(item2 => item.value === item2);
                });

                if (remainingDish.length == 0) {
                    message.error('All dishes have been selected!');
                    return;
                }

                this.arrCurrentDish.push(remainingDish[0].value);

                this.arrDish.forEach(item => {
                    let check = this.arrCurrentDish.some(item2 => item.value === item2);

                    if (check) {
                        item.disabled = true;
                    }
                });

                this.blockDishes.push({
                    currentDish: remainingDish[0].value,
                    dishes: this.arrDish,
                    numberServing: 1,
                });
            },

            handleDishChange(block, index) {
                let oldDish = this.arrCurrentDish[index];
                this.arrCurrentDish[index] = block.currentDish;
                this.arrDish.forEach(item => {
                    if (item.value === block.currentDish) {
                        item.disabled = true;
                    }

                    if (item.value === oldDish) {
                        delete item.disabled;
                    }
                });
            },

            submitOrder() {
                let previewDishes = [];
                this.blockDishes.forEach(item => {
                    previewDishes.push({
                        dish: item.currentDish,
                        numberServing: item.numberServing
                    });
                });

                let previewOrder = {
                    meal: this.currentMeal.label,
                    numberPeople: this.numberPeople,
                    restaurant: this.currentRestaurant,
                    dishes: previewDishes
                }

                console.log(previewOrder);
            },

            handleMealChange() {
                this.isMealChanged = true;
            },

            handleRestaurantChange() {
                this.isRestaurantChanged = true;
            },

            handleNumberPeopleChange(value) {
                if (value === null || value === '') {
                    this.numberPeople = 1;
                }
            },

            handleNumberServingChange(block) {
                if (block.numberServing === null || block.numberServing === '') {
                    block.numberServing = 1;
                }
            }
        }
    }
</script>
