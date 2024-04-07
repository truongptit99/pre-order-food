<style>
    .wrapper {
        width: 40%;
        margin: 70px auto 0;
    }
</style>
<template>
    <div class="wrapper">
        <a-tabs v-model:activeKey="currentTab" centered>
            <a-tab-pane key="1" tab="Step 1" disabled>
                <first-step :meals="meals" :currentMeal="currentMeal" :numberPeople="numberPeople" @handleMeal="handleMealChange" @handleNumberPeople="handleNumberPeopleChange"></first-step>
            </a-tab-pane>
            <a-tab-pane key="2" tab="Step 2" disabled>
                <second-step :restaurants="restaurants" :currentRestaurant="currentRestaurant" @handleRestaurant="handleRestaurantChange"></second-step>
            </a-tab-pane>
            <a-tab-pane key="3" tab="Step 3" disabled>
                <third-step :blockDishes="blockDishes" @handleDish="handleDishChange" @handleNumberServing="handleNumberServingChange" @handleAddDish="addDish"></third-step>
            </a-tab-pane>
            <a-tab-pane key="4" tab="Review" disabled>
                <fourth-step :currentMeal="labelMeal" :numberPeople="numberPeople" :currentRestaurant="currentRestaurant" :blockDishes="blockDishes"></fourth-step>
            </a-tab-pane>
        </a-tabs>
        <group-button :activeKey="currentTab" @nextTab="nextStep" @prevTab="prevStep" @submit="submitOrder"></group-button>
    </div>
</template>
<script>
    import FirstStep from './FirstStep.vue';
    import SecondStep from './SecondStep.vue';
    import ThirdStep from './ThirdStep.vue';
    import FourthStep from './FourthStep.vue';
    import GroupButton from './GroupButton.vue';
    import { message } from 'ant-design-vue';

    export default {
        components: {
            FirstStep,
            SecondStep,
            ThirdStep,
            FourthStep,
            GroupButton
        },

        data() {
            return {
                currentTab: '1',

                currentMeal: 'breakfast',
                meals: [
                    {
                        value: 'breakfast',
                        label: 'Breakfast'
                    },
                    {
                        value: 'lunch',
                        label: 'Lunch'
                    },
                    {
                        value: 'dinner',
                        label: 'Dinner'
                    },
                ],
                isMealChanged: false,

                numberPeople: 1,

                linkGetRestaurant: 'http://127.0.0.1:8000/api/restaurants/',
                currentRestaurant: '',
                restaurants: [],
                isRestaurantChanged: false,

                linkGetDishes: 'http://127.0.0.1:8000/api/dishes?',
                blockDishes: [],
                arrCurrentDish: [],
                arrDish: [],
            }
        },

        computed: {
            labelMeal() {
                let oneMeal = this.meals.find(item => item.value === this.currentMeal);

                return oneMeal ? oneMeal.label : null;
            }
        },

        methods: {
            nextStep() {
                if (Number(this.currentTab) === 3) {
                    let totalDishServing = this.blockDishes.reduce((accumulator, currentValue) => accumulator + currentValue.numberServing, 0);

                    if (totalDishServing < this.numberPeople) {
                        let strPersonOrPeople = this.numberPeople > 1 ? ' people' : ' person';
                        message.warning('The number of dish servings is not enough to ' + this.numberPeople + strPersonOrPeople);
                        return;
                    }

                    if (totalDishServing > 10) {
                        message.error('The maximum number of dish servings can not exceed 10');
                        return;
                    }
                }

                if (Number(this.currentTab) < 4) {
                    this.currentTab = String(Number(this.currentTab) + 1);
                }

                if ((Number(this.currentTab) === 2) && (this.isMealChanged === true || this.restaurants.length === 0)) {
                    fetch(this.linkGetRestaurant + this.currentMeal)
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
                    params.append('meal', this.currentMeal);
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

                if (totalNumberServing >= 10) {
                    message.error('The maximum number of dish servings can not exceed 10');
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

            submitOrder() {
                let previewDishes = [];
                this.blockDishes.forEach(item => {
                    previewDishes.push({
                        dish: item.currentDish,
                        numberServing: item.numberServing
                    });
                });

                let previewOrder = {
                    meal: this.currentMeal,
                    numberPeople: this.numberPeople,
                    restaurant: this.currentRestaurant,
                    dishes: previewDishes
                }

                console.log(previewOrder);
            },

            // Handle change event

            handleMealChange(value) {
                this.isMealChanged = true;
                this.currentMeal = value;
            },

            handleNumberPeopleChange(value) {
                if (value === null || value === '') {
                    this.numberPeople = 1;
                } else {
                    this.numberPeople = value;
                }
            },

            handleRestaurantChange(value) {
                this.isRestaurantChanged = true;
                this.currentRestaurant = value;
            },

            handleDishChange(dish, index, value) {
                dish.currentDish = value;
                let oldDish = this.arrCurrentDish[index];
                this.arrCurrentDish[index] = dish.currentDish;
                this.arrDish.forEach(item => {
                    if (item.value === dish.currentDish) {
                        item.disabled = true;
                    }

                    if (item.value === oldDish) {
                        delete item.disabled;
                    }
                });
            },

            handleNumberServingChange(dish, value) {
                if (value=== null || value === '') {
                    dish.numberServing = 1;
                } else {
                    dish.numberServing = value;
                }
            }
        }
    }
</script>

