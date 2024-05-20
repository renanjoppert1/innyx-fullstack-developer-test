<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'

import AdminLayout from '@/components/AdminLayout.vue'
import PageTitle from '@/components/App/PageTitle.vue'

import axios from 'axios'

import { useToast } from 'vue-toastification'

const toast = useToast()

const loading = ref(false)
const tableData = ref([])
const currentPage = ref(1)
const handleFetchData = async (page = currentPage.value) => {
  loading.value = true
  await axios
    .get(`${import.meta.env.VITE_API_URL}/products?page=${page}`, {
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

const categories = ref<array>([])
const fetchCategories = () => {
  axios
    .get(`${import.meta.env.VITE_API_URL}/categories/all`, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then((res) => {
      categories.value = res.data
    })
    .catch((err) => {
      if (err.response?.data?.message !== undefined) {
        toast.error(err.response.data.message)
      } else {
        toast.error('Houve um erro ao se comunicar com a API')
      }
    })
}
onMounted(() => fetchCategories())

const search = ref('')
const handleSearchSubmit = () => {}

const dataToDelete = ref<Object | null>(null)
const modalDelete = ref<boolean>(false)
const loadingDelete = ref<boolean>(false)
const handleModalDelete = (row: object) => {
  dataToDelete.value = row
  modalDelete.value = true
}
const handleDelete = async () => {
  loadingDelete.value = true
  await axios
    .delete(`${import.meta.env.VITE_API_URL}/products/${dataToDelete.value.id}`, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then(async (res) => {
      toast.success('Produto excluído com sucesso!')
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

const handleGetCategory = (categoryId) => {
  const findCategory = categories.value.find((el) => categoryId == el.id)
  return !!findCategory ? findCategory.id : null
}

const dataToEdit = ref<number | null>(null)
const formEdit = ref<object>({
  name: '',
  description: '',
  price: '',
  due_date: '',
  image_url: '',
  image: '',
  category_id: ''
})
const modalEdit = ref<boolean>(false)
const loadingEdit = ref<boolean>(false)
const editForm = ref(null)

const handleModalEdit = (row: object) => {
  dataToEdit.value = row.id
  formEdit.value.name = row.name
  formEdit.value.description = row.description
  formEdit.value.price = row.price
  formEdit.value.due_date = row.due_date.split('-').reverse().join('/')
  formEdit.value.image_url = row.image_url
  formEdit.value.image = null
  formEdit.value.category_id = handleGetCategory(row.category_id)
  modalEdit.value = true
}

const handleEdit = async () => {
  const { valid } = await editForm.value.validate()
  if (valid === false) {
    toast.error('Há um erro no formulário. Verifique os campos e tente novamente')
    return
  }

  let formData = new FormData()

  for (let field of Object.entries(formEdit.value)) {
    switch (field[0]) {
      case 'due_date':
        field[1] = field[1].split('/').reverse().join('-')
        break

      case 'price':
        field[1] = field[1].toString().replace(',', '.')
        break

      default:
        break
    }
    formData.append(field[0], field[1])
  }

  if (formEdit.value.image == null) {
    formData.delete('image')
  }

  loadingEdit.value = true
  await axios
    .post(`${import.meta.env.VITE_API_URL}/products/${dataToEdit.value}`, formData, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then(async (res) => {
      toast.success('Produto alterado com sucesso!')
      dataToEdit.value = null

      dataToEdit.value = null
      formEdit.value.name = ''
      formEdit.value.description = ''
      formEdit.value.price = ''
      formEdit.value.due_date = ''
      formEdit.value.image_url = ''
      formEdit.value.image = null
      formEdit.value.category_id = ''

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

const formCreate = ref<object>({
  name: 'Teste',
  description: 'Tete',
  price: '20',
  due_date: '10/10/2024',
  image: null,
  category_id: 35
})

const modalCreate = ref<boolean>(false)
const loadingCreate = ref<boolean>(false)
const createForm = ref(null)

const handleModalCreate = () => {
  modalCreate.value = true
}

const handleCreate = async () => {
  const { valid } = await createForm.value.validate()
  if (valid === false) {
    toast.error('Há um erro no formulário. Verifique os campos e tente novamente')
    return
  }

  let formData = new FormData()

  for (let field of Object.entries(formCreate.value)) {
    switch (field[0]) {
      case 'due_date':
        field[1] = field[1].split('/').reverse().join('-')
        break

      case 'price':
        field[1] = field[1].replace(',', '.')
        break

      default:
        break
    }
    formData.append(field[0], field[1])
  }

  loadingCreate.value = true
  await axios
    .post(`${import.meta.env.VITE_API_URL}/products`, formData, {
      headers: { authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    .then(async (res) => {
      toast.success('Produto criado com sucesso!')
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
        <PageTitle title="Produtos" />
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
              <th class="text-left tw-w-20"></th>
              <th class="text-left">Nome</th>
              <th class="text-left">Valor</th>
              <th class="text-left">Categoria</th>
              <th class="text-left">Data de Validade</th>
              <th class="text-right">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in tableData.data" :key="row.name">
              <td><img :src="row.image_url" class="tw-w-16" /></td>
              <td>{{ row.name }}</td>
              <td>R${{ row.price }}</td>
              <td>{{ row.category?.name }}</td>
              <td>{{ row.due_date.split('-').reverse().join('/') }}</td>
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
          Você deseje mesmo apagar o produto
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
      <v-card max-width="400" title="Editar Produto">
        <v-form
          ref="editForm"
          @submit.prevent="handleEdit"
          class="tw-px-4 tw-py-3 tw-flex tw-flex-col tw-gap-4"
        >
          <v-text-field
            v-model="formEdit.name"
            hide-details="auto"
            label="Nome"
            :rules="[() => !!formEdit.name || 'O nome é obrigatório']"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>

          <v-text-field
            v-model="formEdit.description"
            hide-details="auto"
            label="Descrição"
            :rules="[() => !!formEdit.description || 'A descrição é obrigatória']"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>

          <v-text-field
            v-model="formEdit.price"
            hide-details="auto"
            label="Preço"
            :rules="[() => !!formEdit.price || 'O preço é obrigatório']"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>

          <v-text-field
            v-model="formEdit.due_date"
            hide-details="auto"
            label="Vencimento"
            :rules="[() => !!formEdit.due_date || 'O vencimento é obrigatório']"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-text-field>

          <img v-if="!!formEdit.image_url" :src="formEdit.image_url" />

          <v-file-input
            v-model="formEdit.image"
            hide-details="auto"
            label="Imagem"
            accept="image/*"
          >
          </v-file-input>

          <v-select
            v-model="formEdit.category_id"
            label="Categoria"
            hide-details="auto"
            :rules="[() => !!formEdit.category_id || 'A categoria é obrigatória']"
            :items="categories"
            item-title="name"
            item-value="id"
            :readonly="loadingEdit"
            :loading="loadingEdit"
          ></v-select>
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
      <v-card max-width="400" title="Criar Produto">
        <v-form
          ref="createForm"
          @submit.prevent="handleCreate"
          class="tw-px-4 tw-py-3 tw-flex tw-flex-col tw-gap-4"
        >
          <v-text-field
            v-model="formCreate.name"
            hide-details="auto"
            label="Nome"
            :rules="[() => !!formCreate.name || 'O nome é obrigatório']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>

          <v-text-field
            v-model="formCreate.description"
            hide-details="auto"
            label="Descrição"
            :rules="[() => !!formCreate.description || 'A descrição é obrigatória']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>

          <v-text-field
            v-model="formCreate.price"
            hide-details="auto"
            label="Preço"
            :rules="[() => !!formCreate.price || 'O preço é obrigatório']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>

          <v-text-field
            v-model="formCreate.due_date"
            hide-details="auto"
            label="Vencimento"
            :rules="[() => !!formCreate.due_date || 'O vencimento é obrigatório']"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-text-field>

          <v-file-input
            v-model="formCreate.image"
            hide-details="auto"
            label="Imagem"
            accept="image/*"
            :rules="[() => !!formCreate.image || 'A imagem é obrigatória']"
          >
          </v-file-input>

          <v-select
            v-model="formCreate.category_id"
            label="Categoria"
            hide-details="auto"
            :rules="[() => !!formCreate.category_id || 'A categoria é obrigatória']"
            :items="categories"
            item-title="name"
            item-value="id"
            :readonly="loadingCreate"
            :loading="loadingCreate"
          ></v-select>
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
