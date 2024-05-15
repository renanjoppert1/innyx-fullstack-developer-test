<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'

import AdminLayout from '@/components/AdminLayout.vue'
import PageTitle from '@/components/App/PageTitle.vue'

import axios from 'axios'

import { useToast } from 'vue-toastification'
const toast = useToast()

interface User {
  id?: String
  name: String
  email: String
  password: String
}

const loading = ref(false)
const tableData = ref([])
const currentPage = ref(1)
const handleFetchData = (page = currentPage.value) => {
  loading.value = true
  axios
    .get(`${import.meta.env.VITE_API_URL}/users?page=${page}`, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then((res) => {
      tableData.value = res.data
    })
    .catch((err) => {
      if (err.response?.data?.message !== undefined) {
        toast.error(err.response.data.message)
      } else {
        toast.error('Houve um erro ao se comunicar com a API')
      }
    })
    .finally(() => (loading.value = false))
}
onMounted(() => handleFetchData())

watch(
  () => currentPage.value,
  async (to) => {
    await handleFetchData()
  }
)

const search = ref('')
const handleSearchSubmit = () => {}

const dataToDelete = ref<User | null>(null)
const modalDelete = ref<boolean>(false)
const loadingDelete = ref<boolean>(false)
const handleModalDelete = (row: User) => {
  dataToDelete.value = row
  modalDelete.value = true
}
const handleDelete = async () => {
  loadingDelete.value = true
  await axios
    .delete(`${import.meta.env.VITE_API_URL}/users/${dataToDelete.value.id}`, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then(async (res) => {
      toast.success('Usuário excluído com sucesso!')
      modalDelete.value = false
      dataToDelete.value = null
      await handleFetchData(1)
    })
    .catch((err) => {
      if (err.response?.data?.message !== undefined) {
        toast.error(err.response.data.message)
      } else {
        toast.error('Houve um erro ao se comunicar com a API')
      }
    })
    .finally(() => {
      loadingDelete.value = false
    })
}

const dataToEdit = ref<number | null>(null)
const formEdit = ref<User>({ name: '', email: '', password: '' })
const modalEdit = ref<boolean>(false)
const loadingEdit = ref<boolean>(false)

const handleModalEdit = (row: User) => {
  dataToEdit.value = row.id
  formEdit.value.name = row.name
  formEdit.value.email = row.email
  modalEdit.value = true
}

const handleEdit = async () => {
  loadingEdit.value = true
  await axios
    .put(`${import.meta.env.VITE_API_URL}/users/${dataToEdit.value}`, formEdit.value, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then(async (res) => {
      toast.success('Usuário alterado com sucesso!')
      dataToEdit.value = null

      formEdit.value.name = ''
      formEdit.value.email = ''

      modalEdit.value = false
      await handleFetchData(1)
    })
    .catch((err) => {
      if (err.response?.data?.message !== undefined) {
        toast.error(err.response.data.message)
      } else {
        toast.error('Houve um erro ao se comunicar com a API')
      }
    })
    .finally(() => {
      loadingEdit.value = false
    })
}

const formCreate = ref<object>({ name: '', email: '', password: '' })
const modalCreate = ref<boolean>(false)
const loadingCreate = ref<boolean>(false)

const handleModalCreate = () => {
  modalCreate.value = true
}

const handleCreate = async () => {
  loadingCreate.value = true
  await axios
    .post(`${import.meta.env.VITE_API_URL}/users`, formCreate.value, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then(async (res) => {
      toast.success('Usuário criado com sucesso!')
      formCreate.value = { name: '', email: '', password: '' }
      modalCreate.value = false
      await handleFetchData(1)
    })
    .catch((err) => {
      if (err.response?.data?.message !== undefined) {
        toast.error(err.response.data.message)
      } else {
        toast.error('Houve um erro ao se comunicar com a API')
      }
    })
    .finally(() => {
      loadingCreate.value = false
    })
}
</script>

<template>
  <AdminLayout>
    <v-card>
      <div class="tw-p-5">
        <PageTitle title="Usuários" />
      </div>
      <hr />
      <div class="tw-p-5 tw-grid tw-grid-cols-3 tw-gap-10">
        <v-form @submit.prevent="handleSearchSubmit">
          <!--          <v-text-field v-model="search" clearable label="Pesquise">-->
          <!--            <template #append-inner>-->
          <!--              <v-btn variant="text">-->
          <!--                <AppIcon icon="tabler:search"/>-->
          <!--              </v-btn>-->
          <!--            </template>-->
          <!--          </v-text-field>-->
        </v-form>
        <div class="tw-col-span-2 tw-flex tw-justify-end">
          <v-btn color="primary" @click="handleModalCreate">Adicionar</v-btn>
        </div>
      </div>
      <hr />
      <div class="tw-p-5">
        <v-table v-if="loading === false">
          <thead>
            <tr>
              <th class="text-left">Nome</th>
              <th class="text-right">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in tableData.data" :key="row.name">
              <td>{{ row.name }}</td>
              <td class="text-right">
                <v-btn variant="text">
                  <AppIcon
                    icon="mdi-edit-box"
                    height="24px"
                    color="grey"
                    @click="handleModalEdit(row)"
                  />
                </v-btn>

                <v-btn variant="text" color="red" @click="handleModalDelete(row)">
                  <AppIcon icon="mdi:trash" height="24px" color="red" />
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
        <div class="tw-flex tw-flex-col tw-items-center">
          <v-pagination v-model="currentPage" :length="tableData.last_page"> </v-pagination>
          <p class="tw-text-sm">
            Mostrando do {{ tableData.from }} ao {{ tableData.to }} de .Total de Registros
            {{ tableData.total }}
          </p>
          <pre v-if="false">
            {{ tableData }}
          </pre>
        </div>
      </div>
    </v-card>

    <v-dialog v-model="modalDelete" width="auto">
      <v-card max-width="400" title="Atenção">
        <p class="tw-px-6 tw-py-6">
          Você deseje mesmo apagar o usuário
          <strong v-if="dataToDelete !== null">{{ dataToDelete.name }}</strong
          >? Esta ação poderá não ter mais volta.
        </p>
        <template v-slot:actions>
          <v-btn text="Cancelar" @click="modalDelete = false" :disabled="loadingDelete"></v-btn>
          <v-btn
            variant="flat"
            color="red"
            text="Excluir"
            @click="handleDelete"
            :disabled="loadingDelete"
            :loading="loadingDelete"
          ></v-btn>
        </template>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalEdit" width="400">
      <v-card max-width="400" title="Editar Usuário">
        <v-form @submit.prevent="handleEdit" class="tw-px-4 tw-py-3 tw-flex tw-flex-col tw-gap-4">
          <v-text-field
            v-model="formEdit.name"
            hide-details="auto"
            label="Nome"
            :rules="[() => !!formEdit.name || 'O nome é obrigatório']"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>

          <v-text-field
            v-model="formEdit.email"
            hide-details="auto"
            label="E-mail"
            :rules="[() => !!formEdit.email || 'O e-mail é obrigatório']"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>

          <v-text-field
            v-model="formEdit.password"
            hide-details="auto"
            label="Senha"
            type="password"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>
        </v-form>

        <template v-slot:actions>
          <v-btn text="Cancelar" @click="modalEdit = false" :disabled="loadingDelete"></v-btn>
          <v-btn
            variant="flat"
            color="primary"
            text="Salvar"
            @click="handleEdit"
            :disabled="loadingEdit"
            :loading="loadingEdit"
          ></v-btn>
        </template>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalCreate" width="400">
      <v-card max-width="400" title="Criar Usuário">
        <v-form @submit.prevent="handleCreate" class="tw-px-4 tw-py-3 tw-flex tw-flex-col tw-gap-4">
          <v-text-field
            v-model="formCreate.name"
            hide-details="auto"
            label="Nome"
            :rules="[() => !!formCreate.name || 'O nome é obrigatório']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>

          <v-text-field
            v-model="formCreate.email"
            hide-details="auto"
            label="E-mail"
            :rules="[() => !!formCreate.email || 'O e-mail é obrigatório']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>

          <v-text-field
            v-model="formCreate.password"
            hide-details="auto"
            label="Senha"
            type="password"
            :rules="[() => !!formCreate.password || 'O senha é obrigatório']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>
        </v-form>

        <template v-slot:actions>
          <v-btn text="Cancelar" @click="modalEdit = false" :disabled="loadingDelete"></v-btn>
          <v-btn
            variant="flat"
            color="primary"
            text="Salvar"
            @click="handleCreate"
            :disabled="loadingCreate"
            :loading="loadingCreate"
          ></v-btn>
        </template>
      </v-card>
    </v-dialog>
  </AdminLayout>
</template>
