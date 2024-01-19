<script>
import axios from 'axios';
import { debounce } from 'lodash';
import moment from 'moment';
import ThreeDotIcon from './components/ThreeDotIcon.vue';

export default {
    mounted() {
        this.$store.dispatch('userList');
        Echo.private(`chat.${authuser.id}`)
            .listen('MessagePosted', (e) => {
                this.selectUser(e.message.sender_id);
            })
    },
    data() {
        return {
            userName: authuser ? authuser.name : null,
            userAvatar: authuser ? authuser.avatar : null,
            message: '',
            isChatOpen: false,
            openDropdowns: {},
            searchQuery: '',
            messages: [],

        };
    },
    computed: {
        userList() {
            return this.$store.getters.userList;
        },
        userMessage() {
            return this.$store.getters.userMessage;
        },
    },
    methods: {
        user_list() {
            axios.get(this.$host_url + "/users/list" + localStorage.getItem('user_id'), {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            })
                .then((resonse) => {
                    this.users = resonse.data;
                })
        },
        fileLink(file) {
            return `http://localhost:8000/build/assets/${file}`;
        },
        async logout(evt) {
            try {
                await axios.get('/logout');
                window.location.href = '/login';
            } catch (error) {
                console.error(error);
            }
        },
        selectUser(userId) {
            this.$store.dispatch('userMessage', userId);
            this.isChatOpen = true;
            // console.log(userId);
        },
        timestamp: function (date) {
            return moment(date, 'YYYY-MM-DD HH:mm:ss').format('MMMM Do YYYY, LT ');
        },
        sendMessage() {
            // e.preventDefault();
            if (this.message != '') {
                axios.post('/sendmessage', { message: this.message, user_id: this.userMessage.user.id })
                    .then(response => {
                        this.selectUser(this.userMessage.user.id);
                    });
                this.$emit('sentMessage', {
                    message: this.message,
                    user_id: this.userMessage.user.id
                });
                this.message = '';
            }
        },
        // fetchMessages() {
        //     axios.get('/fetchmessage') // Replace with your actual API endpoint
        //         .then(response => {
        //             this.messages = response.data; // Update the local message array
        //             this.messages.push(message);
        //         })
        //         .catch(error => {
        //             // Handle errors gracefully
        //             console.error('Error fetching messages:', error);
        //         });
        // },
        toggleDropdown(messageId) {
            // Check if the dropdown is open for the <specific></specific> message
            this.openDropdowns[messageId] = !this.openDropdowns[messageId];
        },
        isDropdownOpen(messageId) {
            return this.openDropdowns[messageId];
        },
        deleteSingleMessage(messageId) {
            axios.get(`/deletesinglemessage/${messageId}`)
                .then((response) => {
                    this.selectUser(this.userMessage.user.id);
                    this.isDropDownOpen = false;
                });
        },
        deleteAllMessage() {
            axios.get(`/deleteallmessage/${this.userMessage.user.id}`)
                .then((response) => {
                    this.selectUser(this.userMessage.user.id);
                })
        },
        searchUser: debounce(function () {
            axios.get('/searchuser', { params: { query: this.searchQuery } })
                .then((response) => {
                    const filterResult = response.data.filter(user => user.name.toLowerCase().includes(this.searchQuery.toLowerCase()))
                    this.$store.commit('setUserList', filterResult);
                })
                .catch((error) => {
                    console.log(error);
                })
        }, 300)
    },
    created() {
        // LISTEN ECHO FOR USER LOIGN OR OT NOT
        // Echo.join('liveuser')
        //     .here((users) => {
        //         // ...
        //     })
        //     .joining((user) => {
        //         console.log(user.name);
        //     })
        //     .leaving((user) => {
        //         console.log(user.name);
        //     })
        //     .error((error) => {
        //         console.error(error);
        //     });
    },
    watch: {
        searchQuery() {
            this.searchUser()
        },
    },
    components: { ThreeDotIcon }
}
</script>

<template>
    <!-- Header -->
    <div class="bg-white border-b border-gray-300 fixed top-0 w-full shadow">
        <div class="lg:container mx-auto p-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 min-w-[250px]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <img class="w-[60px] h-[50px] rounded-full" :src="userAvatar" alt="Nanoco">
                            <p class="font-semibold text-xl pl-1"> {{ userName }} </p>
                        </div>
                        <div class="cursor-pointer relative inline-block text-left group">
                            <ThreeDotIcon />
                            <div
                                class="origin-top-right absolute right-0  w-56 rounded-md shadow-lg z-50 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden group-hover:block">
                                <div class="py-1 text-center">
                                    <a href="#" class="rounded block px-4 py-2 hover:bg-gray-400 hover:text-gray-500">
                                        Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 text-right">
                    <a class="bg-red-500 text-white px-4 py-3 rounded-lg mr-2" @click.prevent="logout" href="#">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- BODY SECTION -->
    <div class="lg:container mx-auto mt-[100px] px-4 mb-4">
        <div class="grid grid-cols-3 gap-3">
            <div class="col-span-1 min-w-[300px] bg-whitep p-4 shadow-md rounded-md">
                <div class="flex items-center justify-between gap-2">
                    <input v-model="searchQuery" @keyup.enter="searchUser" type="text" placeholder="Search a user..."
                        class="w-full p-2 rounded-md border border-gray-300 focus:border-slate-500" />
                    <button @click.prevent="searchUser" class="bg-blue-500 text-white px-2 py-2 rounded-lg">Search</button>
                </div>
                <!-- Chat List -->
                <div class="max-h-96 overflow-y-auto mt-4">
                    <div v-if="userList" @click.prevent="selectUser(user.id)" v-for="user in userList" :key="user.id"
                        class="flex mt-5 items-center mb-2 cursor-pointer hover:bg-slate-100 rounded-md transition ease-in-out hover:-translate-x-1 hover:scale-20">
                        <div class=" flex items-center relative">
                            <img :src="user.avatar" alt="avatar" class="w-16 h-15 rounded-full border-blue-400 ">
                            <div class="absolute h-3 w-3 bg-slate-400 rounded-full top-0 right-0.5"
                                :class="{ 'status': user.status }">
                            </div>
                        </div>
                        <div class="ml-3 w-[80%]">
                            <p class="font-bold">{{ user.name }}</p>
                            <p v-if="userMessage.user && userMessage.user.id == user.id && userMessage.messages.length > 0"
                                class="text-gray-400 text-sm font-semibold "> {{
                                    userMessage.messages[userMessage.messages.length -
                                        1].message }}</p>
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-cente  m-[25%]">
                        <p class="text-gray-600 text-base text-center">No user found, try again !!!</p>
                    </div>
                </div>
            </div>
            <div class="col-span-2 bg-stone-100 shadow-md">
                <div v-if="isChatOpen" class="w-full h-auto">
                    <!-- Chat Header -->
                    <div
                        class="flex items-center justify-between mb-4 bg-slate-200 px-4 pt-4 pb-2 rounded-tl-md rounded-tr-md">
                        <div class="flex items-center">
                            <img v-if="userMessage.user" :src="userMessage.user.avatar" alt="icon"
                                class="w-15 h-14 rounded-full border border-slate-100 ">
                            <div class="ml-3">
                                <p v-if="userMessage.user" class="font-semibold">{{ userMessage.user.name }}</p>
                                <div class="flex items-center justify-center gap-2 float-left">
                                    <div class="h-3 w-3 bg-green-400 rounded-full top-2 left-3">
                                    </div>
                                    <p class="text-gray-500 text-sm">Online</p>
                                </div>
                            </div>
                            <!-- DROP DOWN OPTION -->
                            <ul class="nav nav-tabs relative">
                                <li class="nav-item dropdown">
                                    <a @click="toggleDropdown(message.id)" class="nav-link dropdown-toggle"
                                        data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                                        aria-expanded="false"><i class="fas fa-ellipsis-v"
                                            style="color: gray; font-size: 0.8em; margin-top:30px"></i></a>
                                    <div class="dropdown-menu " v-show="isDropdownOpen(message.id)">
                                        <a @click.prevent="deleteAllMessage(message.id)"
                                            class="dropdown-item hover:text-red-500 text-xs w-[7rem] absolute bottom-1 left-4"
                                            href="#">Delete All
                                            message</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Chat Body -->
                    <div class="overflow-y-auto max-h-64 min-h-[545px] px-4">
                        <div v-for="message in userMessage.messages" :key="message.id" class="flex items-center gap-2 mt-6"
                            :class="{ 'flex-row-reverse': message.user.id === userMessage.user.id }">
                            <img :src="message.user.avatar" alt="avatar" class="w-12 h-12 rounded-full border-blue-400">
                            <div class=" relative group text-md py-2 px-4 shadow-sm bg-white rounded-lg max-w-lg">
                                {{ message.message }}
                                <div class="absolute w-[150px] text-center top-1/2 left-full ml-3 -translate-y-1/2 bg-gray-200 py-1 px-1.5 rounded hidden group-hover:block z-10 text-[10px]"
                                    :class="{ 'self-message': message.user.id === userMessage.user.id }">
                                    {{ timestamp(message.created_at) }}
                                </div>
                            </div>
                            <ul class="nav nav-tabs relative">
                                <li class="nav-item dropdown">
                                    <a @click="toggleDropdown(message.id)" class="nav-link dropdown-toggle"
                                        data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                                        aria-expanded="false"><i class="fas fa-ellipsis-v"
                                            style="color: gray; font-size: 0.8em;"></i></a>
                                    <div class="dropdown-menu" v-show="isDropdownOpen(message.id)">
                                        <a @click.prevent="deleteSingleMessage(message.id)"
                                            class="dropdown-item hover:text-red-500 text-xs w-[7rem] absolute top-1 left-7"
                                            :class="{ 'self-delete': message.user.id === userMessage.user.id }"
                                            href="#">Delete
                                            message</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Chat Footer -->
                    <div class="flex items-center bg-white rounded-bl-md rounded-br-md gap-2 mt-5">
                        <input @keydown.enter="sendMessage" v-model="message" type="text" placeholder="Type a message"
                            class="w-full p-2 rounded-md border border-gray-300 focus:border-slate-500">
                        <button @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                            Send
                        </button>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center min-h-[19rem]">
                    <img :src="fileLink('icon.jpg')" alt="icon" class="w-20 h-15 rounded-full border border-slate-200 ">
                    <p class="text-2xl font-semibold mt-4">Nanoco</p>
                    <p class="text-gray-500">Select a chat to start messaging</p>
                </div>
            </div>
        </div>
    </div>
</template>


<style lang="css" scoped>
.self-message {
    left: auto;
    right: calc(100% + 15px);
}

.self-delete {
    left: auto;
    right: calc(100% + 0px);
}

.status {
    background-color: green;
}
</style>