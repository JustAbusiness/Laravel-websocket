<!-- resources/js/components/Login.vue -->

<template>
    <x-guest-layout>
      <!-- Session Status -->
      <x-auth-session-status class="mb-4" :status="sessionStatus" />
  
      <form @submit.prevent="login">
        <!-- Email Address -->
        <div>
          <x-input-label :for="email" :value="$t('Email')" />
          <x-text-input v-model="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
          <x-input-error :messages="errors.email" class="mt-2" />
        </div>
  
        <!-- Password -->
        <div class="mt-4">
          <x-input-label :for="password" :value="$t('Password')" />
          <x-text-input v-model="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
          <x-input-error :messages="errors.password" class="mt-2" />
        </div>
  
        <!-- Remember Me -->
        <div class="block mt-4">
          <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" v-model="rememberMe" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
            <span class="ms-2 text-sm text-gray-600">{{ $t('Remember me') }}</span>
          </label>
        </div>
  
        <div class="flex items-center justify-end mt-4">
          <a v-if="passwordRequestRoute" :href="passwordRequestRoute" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ $t('Forgot your password?') }}
          </a>
  
          <x-primary-button type="submit" class="ms-3">{{ $t('Log in') }}</x-primary-button>
        </div>
      </form>
    </x-guest-layout>
  </template>
  
  <script>
import { Axios } from 'axios';

  export default {
    data() {
      return {
        email: '',
        password: '',
        rememberMe: false,
      };
    },
    computed: {
      sessionStatus() {
        return this.$route.query.status;
      },
      passwordRequestRoute() {
        return this.$route('password.request');
      },
    },
    methods: {
      login_user(e) {
        let frm_data = new FormData();
        frm_data.append('email', this.email);
        frm_data.append('password', this.password);
        let config = {
          headers: {
            'Content-Type': 'multipart/form-data',
          }
        }
        axios.post(this.$hosturl+'/login', frm_data, config).then((response) => {
          console.log(response.data);
          let token = response.data.token.token;
          let user_id = response.data.user.id;
          console.log(user_id);
          if (response.data.status == true ) {
              localStorage.setItem('token', token);
              localStorage.setItem('user_id', user_id);

          }
        }).catch((error) => {
          console.log(error);
        });
        ;
      },
    },
  };
  </script>
  