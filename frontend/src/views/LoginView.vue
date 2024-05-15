<script setup lang="ts">
import {ref} from "vue";

import axios from 'axios'
import {useToast} from "vue-toastification";
const toast = useToast()

import { useRouter } from 'vue-router';
const router = useRouter()


const form = ref({
  email: 'admin@innyx.com',
  password: '123456'
})

const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true
  axios.post(`${import.meta.env.VITE_API_URL}/login`, form.value).then(res => {
    localStorage.setItem('token', res.data.bearer)
    router.push('/dashboard')
  }).catch(err => {
    if (err.response?.data?.message !== undefined) {
      toast.error(err.response.data.message);
    } else {
      toast.error("Houve um erro ao se comunicar com a API");
    }
  }).finally(() => loading.value = false)

  loading.value = false
}
</script>

<template>
  <main>

    <div class="tw-relative tw-py-16">
      <div class="container tw-relative tw-m-auto tw-px-6 tw-text-gray-500 md:tw-px-12 xl:tw-px-40">
        <div class="tw-m-auto tw-space-y-8 md:tw-w-8/12 lg:tw-w-6/12 xl:tw-w-6/12">
          <img src="https://innyx.com/wp-content/uploads/2023/10/imagem_2023-10-09_115224533.png" loading="lazy"
               class="tw-ml-4 tw-w-36" alt="innyx logo"/>
          <div
              class="tw-rounded-3xl tw-border tw-border-gray-100 tw-bg-white dark:tw-bg-gray-800 dark:tw-border-gray-700 tw-shadow-2xl shadow-gray-600/10 backdrop-blur-2xl">
            <div class="tw-p-8 tw-py-12 sm:tw-p-16">
              <h2 class="tw-mb-8 tw-text-2xl tw-font-bold tw-text-gray-800 dark:tw-text-white">Acesse sua conta</h2>

              <p class="mb-4">Para acessar a sua conta, digite suas credenciais abaixo</p>

              <v-form @submit.prevent="handleSubmit" class="tw-grid tw-grid-cols-1 tw-gap-6">
                <v-text-field
                    v-model="form.email"
                    hide-details="auto"
                    label="E-mail"
                    :rules="[() => !!form.email || 'O e-mail é obrigatório']"
                    :readonly="loading" :loading="loading"
                ></v-text-field>

                <v-text-field
                    v-model="form.password"
                    hide-details="auto"
                    type="password"
                    label="Senha"
                    :rules="[() => !!form.password || 'A senha é obrigatória']"
                    :readonly="loading" :loading="loading"
                ></v-text-field>

                <v-btn type="submit" color="primary" :disabled="loading" :loading="loading">Acessar</v-btn>
              </v-form>
            </div>
          </div>
          <div class="tw-space-x-4 tw-text-center tw-text-gray-500">
            <span>&copy; Innyx</span>
          </div>
        </div>
      </div>
    </div>

  </main>
</template>
