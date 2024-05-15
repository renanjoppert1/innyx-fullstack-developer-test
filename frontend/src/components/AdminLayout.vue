<script setup lang="ts">
import {ref} from "vue";

const drawer = ref(true)
</script>

<template>
  <v-layout class="rounded rounded-md">
    <v-app-bar color="primary">
      <v-app-bar-nav-icon variant="text" @click.="drawer = !drawer">
        <AppIcon v-if="drawer === false" icon="fa:bars"/>
        <AppIcon v-else icon="tabler:x"/>
      </v-app-bar-nav-icon>

      <h1>
        Innyx - Gerenciador de Produtos
      </h1>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" elevation="10">
      <v-list>
        <v-list-item title="Dashboard" to="dashboard"></v-list-item>

        <v-list-item title="Produtos" to="products"></v-list-item>

        <v-list-item title="Categorias" to="categories"></v-list-item>

        <v-list-item title="Users" to="users"></v-list-item>
      </v-list>
    </v-navigation-drawer>


    <v-main class="tw-bg-slate-100" style="min-height: 100vh;">
      <div class="tw-w-full tw-p-6">
        <slot/>
      </div>
    </v-main>
  </v-layout>
</template>

<style scoped>

</style>